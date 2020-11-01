<?php

namespace App\Models;

use App\Models\BaseModel;
use App\RefListOption;
use App\TenderParameter;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderVendorAdditionalCostAwarding extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_vendor_additional_costs_awarding';

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
        'conditional_code',
        'conditional_name',
        'percentage',
        'value',
        'calculation_pos',
        'conditional_type',
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

    public function conditionalType()
    {
        return $this->belongsTo(RefListOption::class, 'conditional_type','key')->where('type', 'conditional_type_option');
    }
}
