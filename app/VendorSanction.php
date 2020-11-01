<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorSanction extends Model
{
    use SoftDeletes;
    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];

    protected $appends = ['vendor_name', 'vendor_code', 'created_name'];

    public function profile(){
        return $this->belongsTo('App\VendorProfile', 'vendor_profile_id');
    }
    public function getVendorNameAttribute(){
        return $this->profile->company_name ?? "";
    }
    public function getVendorCodeAttribute(){
        return $this->profile->vendor_code ?? "";
    }
    public function sanctionCreated(){
        return $this->belongsTo('App\User', 'created_by');
    }
    public function getCreatedNameAttribute(){
        return $this->sanctionCreated->name ?? "";
    }
}
