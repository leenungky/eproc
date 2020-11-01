<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileBusinessPermit extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_business_permits';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'business_permit_type',
        'business_permit_number',
        'valid_from_date',
        'valid_thru_date',
        'issued_by',
        'attachment',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
