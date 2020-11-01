<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderHeaderTechnicalAwarding extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_header_technical_awarding';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'quotation_date',
    ];

    protected $fillable = [
        'tender_number',
        'vendor_id',
        'vendor_code',
        'quotation_number',
        'quotation_date',
        'quotation_note',
        'tkdn_percentage',
        'quotation_file',
        'tkdn_file',
        'proposed_item_file',
        "status",
        "submission_method",
        'line_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }
    public function getQuotationDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(static::DATETIME_FORMAT) : null;
    }
}
