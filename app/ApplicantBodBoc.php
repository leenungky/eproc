<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantBodBoc extends Model
{
    use SoftDeletes;
    //
    protected $table = 'applicant_company_profiles';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
}
