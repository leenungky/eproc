<?php

namespace App\Models;

use App\Models\BaseModel;
use App\RefListOption;
use App\TenderParameter;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderTaxCode extends BaseModel
{
    use SoftDeletes;

    public $table = 'tender_tax_codes';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "pr_number" ,
        "pr_line_number" ,
        'tax_code',
        'description',
        'public_status',
        'action_status',
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
