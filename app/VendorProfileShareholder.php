<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileShareholder extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_shareholders';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'full_name',
        'nationality',
        'share_percentage',
        'email',
        'ktp_number',
        'ktp_attachment',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
