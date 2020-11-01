<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantGeneralAdministration extends Model {

    use SoftDeletes;

    //
    protected $table = 'applicant_general_administrations';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'applicant_id',
        'company_name',
        'company_type_id',
        'location_category',
        'country',
        'province',
        'city',
        'sub_district',
        'postal_code',
        'address',
        'phone_number',
        'fax_number',
        'website',
        'company_email',
        'created_by',
        'created_at',
        'parent_id'
    ];

}
