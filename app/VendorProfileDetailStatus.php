<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileDetailStatus extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_detail_statuses';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $fillable = [
        'vendor_profile_id',
        'general_status',
        'deed_status',
        'shareholder_status',
        'bodboc_status',
        'businesspermit_status',
        'pic_status',
        'equipment_status',
        'expert_status',
        'certification_status',
        'scopeofsupply_status',
        'experience_status',
        'bankaccount_status',
        'financial_status',
        'tax_status',
        'created_by'
    ];
}
