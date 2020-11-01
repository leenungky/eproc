<?php
namespace App\Traits;

use App\Enums\TenderStatusEnum;
use App\Scopes\PublicViewScope;
use App\TenderParameter;
use Illuminate\Support\Facades\Log;

/**
 * @method static App\Traits withDraft()
 */
trait AccessLog
{
    public static function log($msg){
        try{
            $dir_app_system = storage_path() . "/logs/appsystem/";
            if(!is_dir($dir_app_system)){
                mkdir($dir_app_system, 0777); 
            }
            $f = fopen(storage_path()."/logs/appsystem/".date("Y-m-d").".log", 'a');
            $msg = $msg."\n";
            fwrite($f, date('H:i:s').' >>>> '.$msg);
            fclose($f);
        }catch(Exception $e){

        }
    }

    public static function dblog($msg){
        try{
            $f = fopen(storage_path()."/logs/db_".date("Y-m-d").".log", 'a');
            $msg = $msg."\n";
            fwrite($f, date('H:i:s').' >>>> '.$msg);
            fclose($f);
        }catch(Exception $e){

        }
    }

    public static function sapListLog($msg){
        try{
            $f = fopen(storage_path()."/logs/sapList_".date("Y-m-d").".log", 'a');
            $msg = $msg."\n";
            fwrite($f, date('H:i:s').' >>>> '.$msg);
            fclose($f);
        }catch(Exception $e){

        }
    }

    public static function poToSapListLog($msg){
        try{
            $f = fopen(storage_path()."/logs/sapPo_".date("Y-m-d").".log", 'a');
            $msg = $msg."\n";
            fwrite($f, date('Y-m-d H:i:s').' >>>> '.$msg);
            fclose($f);
        }catch(Exception $e){

        }
    }

    public static function stresLog($msg){
        try{
            $f = fopen(storage_path()."/logs/stress".date("Y-m-d").".log", 'a');
            $msg = $msg."\n";
            fwrite($f, date('Y-m-d H:i:s').' >>>> '.$msg);
            fclose($f);
        }catch(Exception $e){

        }
    }

    public static function maillog($msg){
        try{
            $f = fopen(storage_path()."/logs/appsystem/mail_".date("Y-m-d").".log", 'a');
            $msg = $msg."\n";
            fwrite($f, date('H:i:s').' >>>> '.$msg);
            fclose($f);
        }catch(Exception $e){

        }
    }
}