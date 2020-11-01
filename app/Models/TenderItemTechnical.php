<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\VendorView;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderItemTechnical extends BaseModel
{
    use SoftDeletes, VendorView;

    public $table = 'tender_item_technical';
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
        'description',
        'qty',
        'compliance',
        'status',
        'submission_method',
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
