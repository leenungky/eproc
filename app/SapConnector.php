<?php

namespace App;

use DB;
use Illuminate\Support\Facades\Log;

class SapConnector {

    protected $sapUser;
    protected $sapPassword;
    public $debugMessage;
    public $responseMessage;
    public $requestMessage;
    public $inputMessage;

    public function __construct(){

        $this->sapUser = env('SAP_USER', 'aby_dita');
        $this->sapPassword = env('SAP_PASSWORD', 'dita90');

    }

    public function call($function, $input){
        $this->debugMessage = "";
        $result = false;

        $functionList = config('eproc.sap.functions');

        try{
            if(array_key_exists($function, $functionList)){
                $func = $functionList[$function];
    
                $sapFunction = $func['proxy_function'];
                $sapInput = $func['parameters']['input'];
                $sapOutput = $func['parameters']['output'];
                $wsdl = $func['wsdl'];
    
                foreach($input as $key=>$value){
    
                    foreach($sapInput as $skey=>$sval){
    
                        if(strtoupper($skey)===$key){
                            $sapInput[$skey] = $value;
                        }
    
                    }
    
                }
    
                $parameters = array_merge($sapInput, $sapOutput);
                $this->inputMessage = $sapInput;
    
                $client= new \nusoap_client($wsdl,true);
                $client->setCredentials($this->sapUser,$this->sapPassword);
                $client->soap_defencoding = 'UTF-8';
                $client->decode_utf8 = false;
                $proxy = $client->getProxy();
    
                $result = $proxy->$sapFunction($parameters);
                $this->requestMessage = $proxy->request;
                $this->responseMessage = $proxy->response;
                $this->upperKey($result);
            }else{
                $message = "Function [$function] is not yet available.";
                Log::info($message);
                $this->responseMessage = $message;
            }
        }catch(Exception $e){
            $this->debugMessage = htmlspecialchars($proxy->debug_str, ENT_QUOTES);
            Log::error($e->getMessage());
        }

        return $result;
        
    }

    function upperKey(&$array){
        if(is_array($array)){
            foreach(array_keys($array) as $key){
                $value = &$array[$key];
                unset($array[$key]);
    
                $newKey = strtoupper($key);
    
                if(is_array($value)) $this->upperKey($value);
                $array[$newKey] = $value;
                unset($value);
            }
        }
    }
}