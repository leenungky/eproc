<?php

namespace App\Http\Controllers;

use App\Pages;
use App\PageContents;
use App\SapConnector;
use App\Repositories\RefBankRepository;
use App\Mail\TestMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Session;
use View;
use DataTables;
use App\Repositories\VendorRepository;
use Illuminate\Support\Facades\Log;
use App\Traits\AccessLog;
use App\Jobs\ProcessEmail;

class PagesController extends Controller {
    use AccessLog;

    public function __construct(){
        $this->default_locale = config('app.fallback_locale');
        $this->vendorRepo = new VendorRepository();
    }

    public function index(Request $request) {   
        $locale = $request->session()->get('locale');
        $locale = $locale ?? $this->default_locale;
        if (Auth::user() !== NULL && Auth::user()->user_type === 'vendor') {
            return redirect('/vendor/profile');
        } else {
            return view('index', array('title' => 'home', 'data' => $this->_getPage('home', $locale)));
        }
    }

    public function contact(Request $request) {
        $locale = $request->session()->get('locale');
        $locale = $locale ?? $this->default_locale;
        return view('index', array('title' => 'contact', 'data' => $this->_getPage('contact', $locale)));
    }

    public function page(Request $request, $page, $name) {
        $locale = $request->session()->get('locale');
        $locale = $locale ?? $this->default_locale;
        return view('index', array('title' => $page . "_" . $name, 'data' => $this->_getPage($name, $locale)));
    }

    private function _getPage($name, $locale) {
        $page = Pages::where('name', $name)->first();
        $content = null;
        $default = null;
        if (!is_null($page)) {
            $content = PageContents::where('page_id', $page->id)
                    ->where('language', $locale)
                    ->first();
            $default = PageContents::where('page_id', $page->id)
                    ->where('language', $this->default_locale)
                    ->first();
        }

        $content = $content ?? $default;
        $content = $content ?? json_decode('{"content":"Not Found"}');
        return $content;
    }

    private function getNavigation(){
        // $navigations = 
    }

    public function test(){
        // return redirect()->route('home');
        // $sap = new SapConnector();
        
        // $result = $sap->call('pr_list',['IPRLIST'=>'X']);

        // return json_encode($result,true);
        $tmp = User::role(['Admin Vendor'])->join("user_extensions","user_extensions.user_id","=","users.id")->where("user_extensions.status","=",1)->whereNotNull("users.email")->pluck('email', 'name')->toArray();
        return json_encode($tmp);
    }

    public function testBank(){
        return redirect()->route('home');
        $listRepo = new RefBankRepository();
        $result = $listRepo->syncSAPData();
        $result = $listRepo->findAll();
        return json_encode($result,true);
    }

    public function testEmail(){

        //return redirect()->route('home');
        try{
            $obj = [
                'mailtype'=>'userinfo',
                'vendor_name'=>'Vendor',
                'npwp_tin_number'=>'0123123123',
                'username'=>'Hello',
                'password'=>'password',
            ];
            $obj = (object)$obj;
             foreach ([config('eproc.default_email')] as $recipient) {
                if ($this->vendorRepo->isValidEmail($recipient)){ 
                    ProcessEmail::dispatch($recipient, null, $obj);
                    //Mail::to($recipient)->send(new TestMail($obj));
                }else{
                    $this->maillog("===========email failed==============. email :".json_encode($recipient).", obj: ".json_encode($obj));
                }
            }
            return 'mail sent';
        } catch (Exception $e){
            return response (['status' => false,'errors' => $e->getMessage()]);
        }
    }

    public function managePage(){
        if(null==auth()->user()) return redirect()->route('home');
        $data = [
            'fields' => ['name','language','title','content'],
            'pages' => Pages::all(),
        ];
        return view('admin.page.manager', $data);
    }
    public function storePage(Request $request){
        if(null==auth()->user()) return redirect()->route('home');
        if (request()->ajax()) {
            if(is_null($request->id)){
                $query = PageContents::where('page_id',$request->page_id)->where('language',$request->language);
                if($query->count()>0){
                    $data = $query->first();
                }else{
                    $data = new PageContents();
                }
            }else{
                $data = PageContents::find($request->id);
            }
            $data->page_id = $request->page_id;
            $data->language = $request->language;
            $data->title = $request->title;
            $data->content = $request->content;
            $data->save();

            return response()->json([
                'success'=> true, 
                'message' => 'Page Saved',
            ]);
        }
    }
    public function deletePage($id){
        if(null==auth()->user()) return redirect()->route('home');
        if (request()->ajax()) {
            $data = PageContents::find($id);
            $data->delete();
            return response()->json([
                'success'=> true, 
                'message' => 'Page Deleted',
            ]);
        }
    }
    public function datatable_serverside(Request $request) {
        if(null==auth()->user()) return redirect()->route('home');
        if (request()->ajax()) {
            $data = PageContents::select(
                'page_contents.id',
                'page_contents.page_id',
                'pages.name',
                'page_contents.language',
                'page_contents.title',
                'page_contents.content'
            )
            ->join('pages', function ($join) {
                $join->on('pages.id', '=', 'page_contents.page_id')
                ;
            })
            ->orderBy('page_id')->get();
            return DataTables::of($data)
            ->make(true);
        }
    }
}