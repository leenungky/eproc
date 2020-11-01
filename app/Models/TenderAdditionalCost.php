<?php

namespace App\Models;

use App\Models\BaseModel;
use App\RefListOption;
use App\TenderParameter;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\PublicView;

class TenderAdditionalCost extends BaseModel
{
    use SoftDeletes, PublicView;

    public $table = 'tender_additional_costs';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "pr_number" ,
        "pr_line_number" ,
        'conditional_code',
        'conditional_name',
        'percentage',
        'value',
        'calculation_pos',
        'conditional_type',
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

    public function conditionalType()
    {
        return $this->belongsTo(RefListOption::class, 'conditional_type','key')->where('type', 'conditional_type_option');
    }
}
