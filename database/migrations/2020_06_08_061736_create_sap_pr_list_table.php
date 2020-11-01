<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSapPRListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('sap_pr_list', function (Blueprint $table) {
            $table->id();

            $table->string('BANFN')->nullable(true); // "0011000012$table->string('
            $table->string('BNFPO')->nullable(true); // "00020$table->string('
            $table->string('MATNR')->nullable(true); // "000000000050000029$table->string('
            $table->string('TXZ01')->nullable(true); // "PRINTING CONSUMABLES$table->string('
            $table->string('MATKL')->nullable(true); // "TF02$table->string('
            $table->string('WGBEZ60')->nullable(true); // "$table->string('
            $table->string('DESCTXZ01')->nullable(true); // "$table->string('
            $table->string('MENGE')->nullable(true); // "10.0$table->string('
            $table->string('MEINS')->nullable(true); // "EA$table->string('
            $table->string('PEINH')->nullable(true); // "0$table->string('
            $table->string('MSEHL')->nullable(true); // "$table->string('
            $table->string('PREIS')->nullable(true); // "0.0$table->string('
            $table->string('PREIS2')->nullable(true); // "0.0$table->string('
            $table->string('BADAT')->nullable(true); // "2020-02-06$table->string('
            $table->string('DISPO')->nullable(true); // "$table->string('
            $table->string('DSNAM')->nullable(true); // "$table->string('
            $table->string('WERKS')->nullable(true); // "1100$table->string('
            $table->string('NAME1')->nullable(true); // "$table->string('
            $table->string('LGORT')->nullable(true); // "$table->string('
            $table->string('LGOBE')->nullable(true); // "$table->string('
            $table->string('KNTTP')->nullable(true); // "K$table->string('
            $table->string('KNTTX')->nullable(true); // "$table->string('
            $table->string('YEARS')->nullable(true); // "$table->string('
            $table->string('YEARS2')->nullable(true); // "$table->string('
            $table->string('WAERS')->nullable(true); // "IDR$table->string('
            $table->string('LTEXT')->nullable(true); // "$table->string('
            $table->string('PSTYP')->nullable(true); // "0$table->string('
            $table->string('PTEXT')->nullable(true); // "$table->string('
            $table->string('LFDAT')->nullable(true); // "2020-02-10$table->string('
            $table->string('EKGRP')->nullable(true); // "$table->string('
            $table->string('EKNAM')->nullable(true); // "$table->string('
            $table->string('COSTCODE')->nullable(true); // "$table->string('
            $table->string('COSTDESC')->nullable(true); // "$table->string('
            $table->string('SAKTO')->nullable(true); // "$table->string('
            $table->string('TXT50')->nullable(true); // "$table->string('
            $table->string('LOEKZ')->nullable(true); // "$table->string('
            $table->string('EBAKZ')->nullable(true); // "$table->string('
            $table->string('STATU')->nullable(true); // "N$table->string('
            $table->string('DESCSTATU')->nullable(true); // "$table->string('
            $table->string('BSART')->nullable(true); // "ZPRM$table->string('
            $table->string('BATXT')->nullable(true); // "$table->string('
            $table->string('ERNAM')->nullable(true); // "APR_TN04$table->string('
            $table->string('AFNAM')->nullable(true); // "TSON-010$table->string('
            $table->string('ZRDESC')->nullable(true); // "$table->string('
            $table->string('FRGKZ')->nullable(true); // "2$table->string('
            $table->string('FKZTX')->nullable(true); // "$table->string('
            $table->string('BEDNR')->nullable(true); // "$table->string('
            $table->string('BSMNG')->nullable(true); // "0.0$table->string('
            $table->string('ZZCERT')->nullable(true); // "$table->string('
            $table->string('ZZSTAT')->nullable(true); // "$table->string('
            $table->string('SUMLIMIT')->nullable(true); // "0.0$table->string('
            $table->string('COMMITMENT')->nullable(true); // "0.0$table->string('

            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_pr_list');
        DB::statement('DROP SEQUENCE IF EXISTS sap_pr_list_id_seq');
    }
}
