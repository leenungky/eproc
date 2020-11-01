<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefProvince extends Model
{
    use SoftDeletes;
    
    protected $table = 'ref_provinces';
    protected $primaryKey = ['country_code','region_code'];
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
}
