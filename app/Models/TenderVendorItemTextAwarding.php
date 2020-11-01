<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderVendorItemTextAwarding extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_vendor_item_text_awarding';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        'vendor_id',
        'vendor_code',
        'item_id',
        "PREQ_NO",
        "PREQ_ITEM",
        "TEXT_ID",
        "TEXT_ID_DESC",
        "TEXT_FORM",
        "TEXT_LINE",
        'submission_method',
        'status',
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
