<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class SapPRList extends BaseModel
{
    use SoftDeletes;

    public $table = 'sap_pr_list';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "BANFN" , // 0011000012"
        "BNFPO" , // 00020"
        "MATNR" , // 000000000050000029"
        "TXZ01" , // PRINTING CONSUMABLES"
        "MATKL" , // TF02"
        "WGBEZ60" , // "
        "DESCTXZ01" , // "
        "MENGE" , // 10.0"
        "MEINS" , // EA"
        "PEINH" , // 0"
        "MSEHL" , // "
        "PREIS" , // 0.0"
        "PREIS2" , // 0.0"
        "BADAT" , // 2020-02-06"
        "DISPO" , // "
        "DSNAM" , // "
        "WERKS" , // 1100"
        "NAME1" , // "
        "LGORT" , // "
        "LGOBE" , // "
        "KNTTP" , // K"
        "KNTTX" , // "
        "YEARS" , // "
        "YEARS2" , // "
        "WAERS" , // IDR"
        "LTEXT" , // "
        "PSTYP" , // 0"
        "PTEXT" , // "
        "LFDAT" , // 2020-02-10"
        "EKGRP" , // "
        "EKNAM" , // "
        "COST_CODE" , // "
        "COST_DESC" , // "
        "SAKTO" , // "
        "TXT50" , // "
        "LOEKZ" , // "
        "EBAKZ" , // "
        "STATU" , // N"
        "DESCSTATU" , // "
        "BSART" , // ZPRM"
        "BATXT" , // "
        "ERNAM" , // APR_TN04"
        "AFNAM" , // TSON-010"
        "ZRDESC" , // "
        "FRGKZ" , // 2"
        "FKZTX" , // "
        "BEDNR" , // "
        "BSMNG" , // 0.0"
        "ZZCERT" , // "
        "ZZSTAT" , // "
        "SUMLIMIT" , // 0.0"
        "COMMITMENT" , // 0.0"
    ];

    public function services()
    {
        return $this->hasMany(SapPRListServices::class, ['BANFN','BNFPO'], ['BANFN','BNFPO']);
    }

    public function itemTexts()
    {
        return $this->hasMany(SapPRListItemText::class, ['PREQ_NO','PREQ_ITEM'], ['BANFN','BNFPO']);
    }
}
