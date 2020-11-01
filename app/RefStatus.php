<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefStatus extends Model
{
    //
    protected $table = 'ref_statuss';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
}
