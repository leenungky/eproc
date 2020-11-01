<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderVendorAwarding extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_vendors_awarding';

    const STATUS = [
        1 => 'winner',
        2 => 'lose',
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
        "awarding_status",
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }
}
