<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class SapPRListServices extends BaseModel
{
    use SoftDeletes;

    public $table = 'sap_pr_list_services';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    protected $fillable = [
        "BANFN",
        "BNFPO",
        "EXTROW",
        "SRVPOS",
        "KTEXT1",
        "MENGE",
        "MEINS",
        "WAERS",
        "BRTWR",
        "NETWR",
        "COST_CODE",
        "COST_DESC",
    ];
}
