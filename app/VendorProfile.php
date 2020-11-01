<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfile extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profiles';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_id',
        'company_name',
        'company_type',
        'company_category',
        'company_status',
        'active_skl_number',
        'active_skl_attachment',
        'company_warning',
        'created_by',
        'avl_no',
        'avl_date'
    ];
    protected $appends = ['vendor_code', 'is_blacklisted'];
    
    public function vendorClass(){
        return $this->belongsTo('App\Vendor', 'vendor_id');
    }
    
    public function getVendorCodeAttribute(){
        return $this->vendorClass->vendor_code;
    }

    public function getIsBlacklistedAttribute(){
        return $this->company_warning=='RED';
    }
}
