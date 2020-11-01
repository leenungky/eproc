<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\VendorView;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderItemCommercial extends BaseModel
{
    use SoftDeletes, VendorView;

    public $table = 'tender_item_commercial';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'tender_number',
        'vendor_id',
        'vendor_code',
        'item_id',
        'est_unit_price',
        'price_unit',
        'subtotal',
        'overall_limit',
        'currency_code',
        'compliance',
        "status",
        "submission_method",
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

    public function item()
    {
        return $this->belongsTo(TenderParameter::class, 'line_id','item_id');
    }
}
