<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\VendorView;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderVendorItemDetail extends BaseModel
{
    use SoftDeletes, VendorView;

    public $table = 'tender_vendor_item_detail';
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'tender_number',
        'item_spec_id',
        'vendor_id',
        'vendor_code',
        'description',
        'requirement',
        'reference',
        'data',
        'respond',
        'category_id',
        'submission_method',
        'action_status',
        'status',
        'line_id',
        'created_by',
        'updated_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }
    public function itemSpecification()
    {
        return $this->belongsTo(TenderItemDetail::class, 'line_id','item_spec_id');
    }
}
