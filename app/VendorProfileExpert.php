<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileExpert extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_experts';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'full_name',
        'date_of_birth',
        'education',
        'university',
        'experts_university',
        'major',
        'ktp_number',
        'address',
        'job_experience',
        'years_experience',
        'certification_number',
        'parent_id',
        'attachment',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
