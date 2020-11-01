<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileTool extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_tools';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'equipment_type',
        'total_qty',
        'measurement',
        'brand',
        'condition',
        'location',
        'manufacturing_date',
        'ownership',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
