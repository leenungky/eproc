<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use \DB;
use \App\Traits\AccessLog;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    use AccessLog;
    public function __construct(){
        $this->logQuery = env("LOG_QUERY",true);
    }
    public function register()
    {
        if($this->logQuery){
            DB::listen(function ($query) {
                $arr_db_sql = explode("?", $query->sql);
                $str_sql = "";
                $arr_binding = $query->bindings;
                $len = count($arr_db_sql);
                if ($len>1){
                    foreach($arr_db_sql as $key=>$sql){
                        if (($len-1) == $key){
                            $str_sql .= $sql;
                        }else{
                            $str_sql .= $sql."'".$arr_binding[$key]."'";
                        }
                    }
                }else{
                    $str_sql .= $arr_db_sql[0];
                }
                if( strpos( $str_sql, "sap_pr_list" ) !== false) {
                    $this->log("SQL <<< ".$str_sql);
                }else{
                    $this->log("SQL <<< ".$str_sql);
                }

            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
