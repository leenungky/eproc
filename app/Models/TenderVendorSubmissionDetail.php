<?php

namespace App\Models;

use App\Enums\TenderSubmissionEnum;
use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\TenderLog;
use App\Traits\VendorView;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderVendorSubmissionDetail extends BaseModel
{
    use SoftDeletes, TenderLog, VendorView;

    public $table = 'tender_vendor_submission_detail';
    const STATUS = TenderSubmissionEnum::STATUS_ITEM;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "vendor_id",
        "vendor_code",
        "bidding_document_id" ,
        "submission_method" ,
        "attachment" ,
        "status",
        'order',
        'action_status',
        'line_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }
}
