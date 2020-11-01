<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileDeed extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_deeds';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'deed_type',
        'deed_number',
        'deed_date',
        'notary_name',
        'sk_menkumham_number',
        'sk_menkumham_date',
        'attachment',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
