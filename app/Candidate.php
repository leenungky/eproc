<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    //
    protected $table = 'applicants';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
}
