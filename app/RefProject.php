<?php

namespace App;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class RefProject extends BaseModel
{
    protected $table = 'ref_projects';
    protected $primaryKey = 'code';
    protected $guarded = ['created_at','updated_at'];
    public $incrementing = false;
}
