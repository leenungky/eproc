<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantCompanyProfile extends Model
{
    use SoftDeletes;
    //
    protected $table = 'applicant_company_profiles';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $fillable = [
        'applicant_id',
        'company_name',
        'company_type_id',
        'created_by'
    ];
}
