<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class TenderVendor extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_vendors';

    const STATUS = [
        0 => 'draft',
        1 => 'invitation',
        2 => 'accepted',
        3 => 'rejected',
        4 => 'registered',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "vendor_id" ,
        "vendor_code" ,
        "tender_vendor_type" ,
        "status",
        "negotiation_status",
        'public_status',
        'action_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }
}
