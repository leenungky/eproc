<?php

namespace App\Models;

use App\Enums\TenderSubmissionEnum;
use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\PublicView;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderWeighting extends BaseModel
{
    use SoftDeletes, PublicView;

    public $table = 'tender_weightings';

    const TYPE = TenderSubmissionEnum::STAGE_TYPE;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "tender_number" ,
        "criteria" ,
        "submission_method" ,
        'weight',
        'is_commercial',
        'order',
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

    public static function getEnableType()
    {
        $tenderTypes = TenderWeighting::TYPE;
        unset($tenderTypes[5]);
        // unset($tenderTypes[6]);
        return $tenderTypes;
    }
}
