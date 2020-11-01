<?php

namespace App\Models;

use App\Models\BaseModel;

class Role extends BaseModel
{
    // use SoftDeletes;

    public $table = 'roles';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        "name" ,
        "guard_name" ,
    ];
}
