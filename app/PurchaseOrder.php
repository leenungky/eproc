<?php

namespace App;

use App\Models\BaseModel;
use App\TenderParameter;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends BaseModel
{
    use SoftDeletes;
    public $table = 'po_list';
    //public function vendor()
    // {
    //     return $this->hasOne('App\Vendor','vendor_code','vendor_code');
    // }

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "vendor_code" ,
        "eproc_po_number" ,
        "sap_po_number" ,
        "eproc_po_status",
        'created_by',
        'updated_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }
}
