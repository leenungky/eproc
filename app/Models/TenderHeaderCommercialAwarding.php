<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderHeaderCommercialAwarding extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_header_commercial_awarding';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'quotation_date',
        'bid_bond_end_date',
    ];

    protected $fillable = [
        'tender_number',
        'vendor_id',
        'vendor_code',
        'quotation_number',
        'quotation_date',
        'quotation_note',
        'quotation_file',
        'incoterm',
        'incoterm_location',
        'bid_bond_value',
        'bid_bond_file',
        'bid_bond_end_date',
        'currency_code',
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
        // return $value ? Carbon::parse($value)->format(static::DATETIME_FORMAT) : null;
        return $value ? Carbon::parse($value)->setTimeZone(config('timezone'))->format(static::DATETIME_FORMAT) : null;
    }
    public function getBidBondEndDateAttribute($value)
    {
        // return $value ? Carbon::parse($value)->format(static::DATETIME_FORMAT) : null;
        return $value ? Carbon::parse($value)->setTimeZone(config('timezone'))->format(static::DATETIME_FORMAT) : null;
    }
}
