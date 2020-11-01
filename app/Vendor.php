<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Vendor extends Model
{
    protected $table = 'vendors';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    // protected $dateFormat = 'm/d/Y';
    
    protected $fillable = [
        'vendor_name',
        'company_type_id',
        'purchase_org_id',
        'purchase_org_id_1',
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
        'identification_type', 
        'tin_number', 
        'tin_attachment',        
        'idcard_number',        
        'idcard_attachment',        
        'pkp_type',
        'pkp_number',
        'pkp_attachment', 
        'non_pkp_number',
        'vendor_code',
        'vendor_group',
        'business_partner_code',
        'sap_vendor_code',
        'already_exist_sap',
        'created_by',
        'street',
        'building_name',
        'kavling_floor_number',
        'village',
        'rt',
        'rw',
    ];
    
    public function profile()
    {
        return $this->hasOne('App\VendorProfile', 'vendor_id')->select('*');
    }
    
    public function purchaseorg()
    {
        return $this->hasOneThrough('App\Vendors', 'App\RefPurchaseOrg', 'purchase_ord_id');
    }

    static public function getCurrent(){
        return DB::table('vendors')
        ->leftJoin('vendor_profiles as p', function ($join) {
            $join->on('p.vendor_id', '=', 'vendors.id');
        })
        ->leftJoin('vendor_profile_generals as g', function ($join) {
            $join->on('p.id', '=', 'g.vendor_profile_id')
                 ->where('g.primary_data',true)
                 ->where('g.is_current_data',true)
                 ->whereNull('g.deleted_at');
        });
    }
}
