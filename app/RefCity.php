<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefCity extends Model
{
    use SoftDeletes;
    
    protected $table = 'ref_cities';
    protected $primaryKey = 'city_code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
}
