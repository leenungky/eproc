<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderConfigApprovers extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_config_approvers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        "purch_org_id" ,
        "role_id" ,
        "order" ,
        "created_by" ,
        "updated_by" ,
    ];
}
