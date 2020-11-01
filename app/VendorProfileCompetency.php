<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileCompetency extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_competencies';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'classification',
        'sub_classification',
        'detail_competency',
        'vendor_type',
        'attachment',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
