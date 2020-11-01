<?php

namespace App\Models;

use App\Enums\TenderSubmissionEnum;
use App\Models\BaseModel;
use App\TenderParameter;
use App\Traits\TenderLog;
use Illuminate\Support\Facades\Auth;
use App\Vendor;

class TenderReference extends BaseModel
{
    use TenderLog;

    public $table = 'tender_references';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'tender_number',
        'ref_type',
        'ref_value',
        'ref_vendor_id',
        'submission_method',
        'created_by',
        'updated_by',
    ];

    public function parameter()
    {
        return $this->belongsTo(TenderParameter::class, 'tender_number','tender_number');
    }

    public static function QuotationValidityEndDate($tenderNumber, $stageType = 3)
    {
        $model = static::where('tender_number', $tenderNumber)
            ->where('submission_method', $stageType)->first();
        if($model){
            return $model->created_at->add($model->parameter->validity_quotation,'day')->format(static::DATE_FORMAT);
        }
        return '';
    }

    public static function isStarted($tenderNumber)
    {
        return static::where('tender_number', $tenderNumber)
            ->where('submission_method', TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation'])
            ->where('ref_type', TenderSubmissionEnum::FLOW_STATUS[1])
            ->count() > 0;
    }
    public static function hasResubmission($tenderNumber)
    {
        return static::where('tender_number', $tenderNumber)
            ->where('ref_type', TenderSubmissionEnum::FLOW_STATUS[3])
            ->count() > 0;
    }

    protected static function logValues($activity, $model)
    {
        $user = Auth::user();
        $act = $model->ref_type;
        $pageTypes = array_flip(TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE);
        if(in_array($model->submission_method, [TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_technical'],TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['negotiation_commercial']])
            && in_array($model->ref_type, [TenderSubmissionEnum::FLOW_STATUS[1],TenderSubmissionEnum::FLOW_STATUS[2], TenderSubmissionEnum::FLOW_STATUS[4]])
        ){
            $vendor = Vendor::where('id', $model->ref_vendor_id)->first();
            $userId = $vendor->vendor_code ?? null;
        }else{
            $userId = $user->userid ?? null;
        }
        return [
            'user_id' => $userId,
            'activity' => $act,
            'model_id' => $model->id ?? null,
            'model_type' => get_class($model) ?? null,
            'page_type' => $pageTypes[$model->submission_method] ?? null,
            'ref_number' => $model->tender_number,
            'properties' => $model,
            'host' => request()->ip() ?? null,
        ];
    }
}
