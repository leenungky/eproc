<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    //
    protected $table = 'applicants';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    // protected $dateFormat = 'm/d/Y';
    
    protected $fillable = [
        'partner_name',
        'company_type_id',
        'purchase_org_id',
        'president_director',
        'address_1',
        'address_2',
        'address_3',
        'address_4',
        'address_5',
        'country',
        'province', 
        'city',
        'sub_district', 
        'house_number',
        'postal_code', 
        'phone_number',
        'fax_number', 
        'company_email',
        'company_site', 
        'pic_full_name', 
        'pic_mobile_number',
        'pic_email', 
        'tender_ref_number',
        'pkp_number',
        'pkp_attachment', 
        'npwp_tin_number', 
        'npwp_tin_attachment'                  
    ];
}
