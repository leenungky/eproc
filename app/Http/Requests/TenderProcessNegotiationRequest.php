<?php
namespace App\Http\Requests;

use App\Enums\TenderSubmissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;

class TenderProcessNegotiationRequest extends FormRequest
{
    private $tender;
    private $pageType;

    public function __construct($tender, $type, ValidationFactory $validationFactory)
    {
        $this->tender = $tender;
        $this->pageType = $type;
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            '*file' => 'file|max:5000', // |mimes:jpeg,bmp,png,zip,doc,docx,xls,xlsx,pdf,csv', // 5000 = 5MB
            'attachment' => 'file|max:5000', // |mimes:jpeg,bmp,png,zip,docx,doc,xls,xlsx,pdf,csv', // 5000 = 5MB
        ];
    }

    private function getStageType()
    {
        $params = $this->all();
        return !empty($params['stage_type'])
            ? $params['stage_type']
            : TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE[$this->pageType];
    }
}
