<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileExperience extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_experience';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'classification',
        'sub_classification',
        'project_name',
        'project_location',
        'contract_owner',
        'address',
        'country',
        'province',
        'city',
        'sub_district',
        'postal_code',
        'contact_person',
        'phone_number',
        'contract_number',
        'valid_from_date',
        'valid_thru_date',
        'currency',
        'contract_value',
        'bast_wan_date',
        'bast_wan_number',
        'bast_wan_attachment',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
