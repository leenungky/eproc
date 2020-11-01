<?php

namespace App\Models;

use App\Models\BaseModel;

class TenderPermission extends BaseModel
{
    public $table = 'tender_permissions';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'page',
        'order',
        'created_by',
        'updated_by',
    ];
}
