<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefCountry extends Model
{
    use SoftDeletes;
    
    protected $table = 'ref_countries';
    protected $primaryKey = 'country_code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;
    
}
