<?php

namespace App\Models;

use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\TenderLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderVendorScores extends BaseModel
{
    use SoftDeletes, TenderLog;

    public $table = 'tender_vendor_scores';


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        // 'submission_date',
    ];

    protected $fillable = [
        "tender_number" ,
        "weight_id",
        "vendor_id" ,
        "vendor_code" ,
        'submission_method',
        'score',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }

    public function tenderWeighting()
    {
        return $this->belongsTo(TenderWeighting::class, 'line_id','weight_id');
    }
}
