<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefSubDistrict extends Model
{
    use SoftDeletes;
    
    protected $table = 'ref_sub_districts';
    protected $primaryKey = 'district_code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
}
