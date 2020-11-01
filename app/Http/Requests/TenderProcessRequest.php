<?php
namespace App\Http\Requests;

use App\Enums\TenderSubmissionEnum;
use App\Models\TenderVendorSubmission;
use App\Repositories\TenderProcessRepository;
use App\Repositories\TenderSignatureRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;
use Log;

class TenderProcessRequest extends FormRequest
{
    private $tender;
    private $pageType;

    public function __construct($tender, $type, ValidationFactory $validationFactory)
    {
        $this->tender = $tender;
        $this->pageType = $type;

        $validationFactory->extend(
            'required_vendor_score',
            [$this,'requiredVendorScore']
        );
        $validationFactory->extend(
            'required_vendor_evaluation',
            [$this,'requiredVendorEvaluation']
        );
        $validationFactory->extend(
            'validate_submit',
            [$this,'validateSubmit']
        );
        $validationFactory->extend(
            'validate_commercial_approval',
            [$this,'validateCommercialApproval']
        );
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            '*file' => 'file|max:'.config('eproc.max_file_upload_size'), // |mimes:jpeg,bmp,png,zip,doc,docx,xls,xlsx,pdf,csv', // 5000 = 5MB
            'attachment' => 'file|max:'.config('eproc.max_file_upload_size'), // |mimes:jpeg,bmp,png,zip,docx,doc,xls,xlsx,pdf,csv', // 5000 = 5MB
            'action_type' => 'required_vendor_evaluation|validate_submit|validate_commercial_approval'
            // 'action_type' => 'required_vendor_evaluation|required_vendor_score'
        ];
    }

    public function requiredVendorScore($attribute, $value, $parameters)
    {
        // $params = $this->all();
        // if(isset($params['action_type']) && in_array($params['action_type'], [TenderSubmissionEnum::FLOW_STATUS[5],TenderSubmissionEnum::FLOW_STATUS[6]])){
        //     $submissions = (new TenderProcessRepository)
        //         ->findVendorSubmission($this->tender->tender_number, $this->getStageType());
        //     if($submissions != null && $submissions->count() > 0){
        //         foreach($submissions as $k => $sub){
        //             // if($sub->score == null && $sub->status == TenderSubmissionEnum::STATUS_ITEM[2]) return false;
        //             if($sub->score == null) return false;
        //         }
        //     }
        //     return true;
        // }
        return true;
    }
    public function requiredVendorEvaluation($attribute, $value, $parameters)
    {
        $params = $this->all();
        if(
            (isset($params['action_type']) && in_array($params['action_type'], [TenderSubmissionEnum::FLOW_STATUS[6]]))
            || (isset($params['action_type']) && $params['action_type']=='commercialSignature' && $params['subaction']=='submit')
        ){
            $stageType = $this->getStageType() == TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_tender_evaluation']
                ? TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']
                : $this->getStageType();
            $submissions = (new TenderProcessRepository)
                ->findVendorSubmission($this->tender->tender_number, $stageType);
            // if($this->getStageType() )
            if($submissions != null && $submissions->count() > 0){
                foreach($submissions as $k => $sub){
                    if(in_array($sub->status, [TenderVendorSubmission::STATUS[1],TenderVendorSubmission::STATUS[2]]))
                        return false;
                }
            }
            return true;
        }
        return true;
    }

    public function validateSubmit($attribute, $value, $parameters)
    {
        $params = $this->all();
        if(isset($params['action_type']) && in_array($params['action_type'],
            [
                'submit-submission-detail',
                'resubmit-submission-detail',
                'delete-all-submission-detail',
                'upload-submission-detail',
                'save-tender-header',
                'save-tender-items',
            ])
        ){
            $stageType = $this->getStageType();
            $statusEnums = TenderSubmissionEnum::FLOW_STATUS;
            $workflowValues = explode('-', $this->tender->workflow_values);
            $actIndex = 2;
            if(!isset($workflowValues[$actIndex])){
                $workflowValues[$actIndex] = '';
            }
            $statusProcess = '';

            if(in_array($stageType, [TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'], TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_commercial_evaluation']])){
                if(($workflowValues[$actIndex] == $statusEnums[2] || $workflowValues[$actIndex]==$statusEnums[4])){
                    $statusProcess = 'opened';
                }

                if($this->tender->submission_method == '1E'){
                    if($statusProcess == 'opened'){
                        return false;
                    }
                }else{
                    $curStageType = TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$workflowValues[0]]; //$workflowValues[1];
                    if(!empty($workflowValues[1])){
                        $curStageType =$workflowValues[1];
                    }
                    if($statusProcess == 'opened' && $curStageType == $stageType){
                        return false;
                    }
                }
            }
            return true;
        }
        return true;
    }

    public function validateCommercialApproval($attribute, $value, $parameters)
    {
        $params = $this->all();
        if(
            (isset($params['action_type']) && $params['action_type']=='commercialSignature' && $params['subaction']=='submit')
        ){
            //rejected, need to recreate
            if($this->tender->commercial_approval_status==TenderSubmissionEnum::STATUS_ITEM[4]) return false;

            $signRepo = new TenderSignatureRepository();
            $proposed = $signRepo->findCommercialByTenderNumber($this->tender->tender_number,1)->count();
            $approved = $signRepo->findCommercialByTenderNumber($this->tender->tender_number,2)->count();
            return $proposed > 0 && $approved > 0;
        }
        return true;
    }

    private function getStageType()
    {
        $params = $this->all();
        return !empty($params['stage_type'])
            ? $params['stage_type']
            : TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$this->pageType];
    }
}
