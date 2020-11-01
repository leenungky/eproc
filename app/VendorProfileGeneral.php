<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileGeneral extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_generals';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'company_name',
        'company_type_id',
        'location_category',
        'street',
        'house_number',
        'building_name',
        'kavling_floor_number',
        'rt',
        'rw',
        'village',
        'country',
        'province',
        'city',
        'sub_district',
        'postal_code',
        'address_1',
        'address_2',
        'address_3',
        'phone_number',
        'fax_number',
        'website',
        'company_email',
        'parent_id',
        'primary_data',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
    protected $appends = ['company_type'];

    public function company(){
        return $this->belongsTo('App\RefCompanyType', 'company_type_id');
    }
    public function getCompanyTypeAttribute(){
        return $this->company->company_type;
    }
}
