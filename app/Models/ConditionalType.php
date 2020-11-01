<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConditionalType extends BaseModel
{
    use SoftDeletes;

    public $table = 'conditional_types';

    const TYPE_PERCENTAGE = 1;
    const TYPE_VALUE = 2;
    const POSITION_PLUS = 1;
    const POSITION_MINUS = 2;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "type" ,
        "description" ,
        "calculation_type" ,
        'calculation_pos',
        'order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
