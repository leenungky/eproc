<?php

namespace App\Repositories;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use App\SapConnector;

class VendorSanctionRepository extends BaseRepository
{
    private $logName = 'VendorRepository';

    public $type = ["BLOCK" => 4, "UNBLOCK" => 5];

    public function __construct(){
    }

    public function block($data){
        return $this->sapSend($data, $this->type['BLOCK']);
    }
    public function unblock($data){
        return $this->sapSend($data, $this->type['UNBLOCK']);
    }

    public function sapSend($data,$type){
        $sap = new SapConnector();
        $result = $sap->call('bp_sanction',[
            'T_DATA'=>[
                'item'=>[
                    'PROC_TYPE'=> $type,
                    'PARTNER_NO'=>$data['business_partner_code'],
                    'VENDOR_NO'=>$data['sap_vendor_code'],
                    'EKORG'=>$data['org_code'],
                ],
            ],
        ]);
        Log::debug("============== REQUEST TO SAP (BP Sanction) ===============");
        Log::debug($sap->requestMessage);
        Log::debug("============== SAP RESPONSE (BP Sanction) ===============");
        Log::debug($sap->responseMessage);
        if(isset($result['RETURN'])){
            if(!isset($result['RETURN']['ITEM']['TYPE'])){
                $status = true;
                foreach($result['RETURN']['ITEM'] as $item){
                    switch($item['TYPE']){
                        case 'S' : $status = $status && true; break; //success
                        case 'E' : $status = $status && false; break; //error
                        case 'W' : $status = $status && true; break; //warning
                        case 'I' : $status = $status && true; break; //info
                        case 'A' : $status = $status && false; break; //abort
                    }
                }
            }else{
                switch($result['RETURN']['ITEM']['TYPE']){
                    case 'S' : $status = true; break; //success
                    case 'E' : $status = true; break; //error
                    case 'W' : $status = true; break; //warning
                    case 'I' : $status = true; break; //info
                    case 'A' : $status = false; break; //abort
                }
            }
            $message = "";
            if(!isset($result['RETURN']['ITEM']['MESSAGE'])){
                foreach($result['RETURN']['ITEM'] as $item){
                    $message.=$item['MESSAGE']."\n";
                }
            }else{
                $message = $result['RETURN']['ITEM']['MESSAGE'];
            }
            return ['status'=>$status,'message'=>$message];
        }else{
            //something wrong
            return ['status'=>false,'message'=>"Network Connection Error. Please contact administrator."];
        }
    }


}