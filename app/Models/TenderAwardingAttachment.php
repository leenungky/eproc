<?php

namespace App\Models;

use App\Enums\TenderSubmissionEnum;
use App\Models\BaseModel;
use App\TenderParameter;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderAwardingAttachment extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_awarding_attachment';
    const STATUS = TenderSubmissionEnum::STATUS_ITEM;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number",
        "vendor_id",
        "vendor_code",
        "description",
        "attachment",
        "status",
        'order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number', 'tender_number');
    }
}
