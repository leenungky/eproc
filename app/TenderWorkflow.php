<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class TenderWorkflow extends Model
{
    use SoftDeletes;
    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];

}
