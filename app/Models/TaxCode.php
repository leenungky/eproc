<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxCode extends BaseModel
{
    use SoftDeletes;

    public $table = 'tax_codes';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tax_code" ,
        "description" ,
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
