<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderVendorTaxCodeAwarding extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_vendor_tax_codes_awarding';

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
        'tax_code',
        'description',
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

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class, 'tax_code','tax_code');
    }
}
