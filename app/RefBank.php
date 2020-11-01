<?php

namespace App;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class RefBank extends BaseModel
{
    protected $table = 'ref_banks';
    protected $primaryKey = 'id';
    protected $guarded = ['id','created_at','updated_at'];
    public $incrementing = false;
}
