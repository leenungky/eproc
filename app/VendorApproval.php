<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorApproval extends Model
{
    use SoftDeletes;
    
    protected $table = 'vendor_approvals';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    
    protected $fillable = [
        'vendor_id',
        'as_position',
        'approver',
        'sequence_level',
        'is_done',
        'created_by'
    ];
}
