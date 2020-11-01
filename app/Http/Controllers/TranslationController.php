<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use URL;
use App;

class TranslationController extends Controller {
    //
    public function __construct(){
        $this->middleware('auth');
        $this->default = config('app.fallback_locale');
    }

    /**
     * Change session locale
     * @param  Request $request
     * @return Response
     */
    public function changeLocale($locale) {
        if (!in_array($locale, ['en', 'id'])) {
            $locale = $this->default;
        }
        Session::put('locale', $locale);
        return redirect(url(URL::previous()));
    }

    public function manage(){
        $default = $this->default;
        $path = resource_path().'/lang';
        $files = scanDir($path);
        $files = array_diff($files, ['.','..']);

        $data[$default] = $this->loadLanguageFiles($default);
        foreach($files as $file){
            if(is_dir($path.'/'.$file)){
                if($file!==$default){
                    $data[$file] = $this->loadLanguageFiles($file);
                }
            }
        }
        $keys = array_keys($data);
        return view('admin.locale.manager',['default'=>$default, 'languages'=>$keys, 'data' => $data]);
    }

    public function storeLocale(Request $request){
        $input = $request->input();
        $languages = $input['languages'];
        $path = resource_path().'/lang/';
        foreach($languages as $language){
            $output = "<?php\n\nreturn\n[\n";
            $output .= $this->arrayToContents($input[$language],$language,"");
            $output .= "];\n";
            // echo $output;die();
            file_put_contents($path.$language.'/'.$input['file'].'.php', $output);
        }
        Session::put('success','<b>Success!</b> '.$input['file'].' saved');
        return redirect()->back();
    }

    private function arrayToContents($array,$language,$prepend=""){
        $output = "";
        foreach ($array as $key=>$value){
            if($value!=''){
                if(is_array($value)){
                    ksort($value);
                    $output .= $prepend."\t\"".$key."\" => [\n";
                    $output .= $this->arrayToContents($value,$language,$prepend."\t");
                    $output .= $prepend."\t],\n";
                }else{
                    $output .= $prepend."\t\"".$key."\" => \"".$value."\",\n";
                }
            }else if($language==$this->default){
                //if language is default language, still save the key value.
                $output .= $prepend."\t\"".$key."\" => \"".$value."\",\n";
            }
        }
        return $output;
    }

    private function loadLanguageFiles($locale){
        $path = resource_path().'/lang/'.$locale;
        $files = scanDir($path);
        $files = array_diff($files, ['.','..']);

        $output = [];
        foreach($files as $file){
             $tmp = include($path.'/'.$file);
             ksort($tmp);
             $f = explode('.php',$file)[0];
             $output[$f] = $tmp;
        }

        return $output;
    }
}