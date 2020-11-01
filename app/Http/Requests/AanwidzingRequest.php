<?php
namespace App\Http\Requests;

use App\Models\TenderAanwijzings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;

class AanwidzingRequest extends FormRequest
{
    private $tender;

    public function __construct($tender, ValidationFactory $validationFactory)
    {
        $this->tender = $tender;
        $validationFactory->extend(
            'have_one_vendor',
            [$this,'haveVendorRule']
            // __('validation.custom.have_vendor')
        );

    }

    public function authorize()
    {
        // abort_if(Gate::denies('user_alert_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function haveVendorRule($attribute, $value, $parameters)
    {
        if($value == TenderAanwijzings::STATUS[2]){
            if($this->tender && $this->tender->vendors->count() > 0){
                return true;
            }
            return false;
        }
        return true;
    }

    public function rules()
    {
        return [
            'result_attachment' => 'file|max:5000', // 5MB
            'public_status' => 'required|have_one_vendor',
        ];
    }
}
