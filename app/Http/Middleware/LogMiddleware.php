<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Traits\AccessLog;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;


final class LogMiddleware extends Middleware
{
    use AccessLog;

    protected $except = [
        'word*',
        
    ];
    
    // author muhammad.salam@abyor.com
    private $start;
    private $end;
    private $response;

    public function __construct(){
        $this->log_function = env("LOG_FUNCTION",true);
    }

    public function handle($request, Closure $next){
        if ($this->log_function){
            $this->start = microtime(true);
            
            $str_data = date("Y-m-d h:i:s");
            $str_data = $str_data. "method : ".$request->method().", ";
            $url    = $request->fullUrl();
            $input  = json_encode($request->input()); 
            $method = $request->method();
            $msg = "request: >>". $method." >> : ".$url." > ".$input;
            //dd($request->url() );
            $this->log($msg);
            $msgLog = "request: >>". $url;            
            $this->stresLog($msgLog);
            $response = $next($request);
            $this->end = microtime(true);
            $durations = number_format($this->end - $this->start, 3);
            $content = "Report success open word|excel|pdf";
            if (!$request->is('word/*') && !$request->is('excel/*') && !$request->is('pdf/*') ) {
                $content = $response->content();
            }
            $dt_content= $content;
            $firstcontent = substr($dt_content,0, 35);
            $lastcontent = substr($dt_content, - 10);
            $isHtml = strpos($lastcontent, "</html>");
            $str_content = "";
            if ($isHtml){
                $str_content = "html success >> ".$firstcontent."...".$lastcontent;
            }else{
                $str_content = $content;
            }
            $msg = "response: >> durasi: ". $durations." > \n".$str_content;
            $this->log($msg);
            $msgLog = "response: >> durasi: ". $durations;            
            $this->stresLog($msgLog);
            return $response;
        }else{
            $response = $next($request);
            return $response;
        }
    }

}