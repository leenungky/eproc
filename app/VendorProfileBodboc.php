<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfileBodboc extends Model
{
    use SoftDeletes;

    //
    protected $table = 'vendor_profile_bodbocs';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'vendor_profile_id',
        'board_type',
        'is_person_company_shareholder',
        'full_name',
        'nationality',
        'position',
        'email',
        'phone_number',
        'company_head',
        'parent_id',
        'is_finished',
        'is_submitted',
        'is_current_data',
        'created_by'
    ];
}
