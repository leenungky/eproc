<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Support\Facades\Log;

class TenderScheduleRequest extends FormRequest
{
    private $tender;

    public function __construct($tender, ValidationFactory $validationFactory)
    {
        $this->tender = $tender;
        $message = $this->getValidationMessage();
        $tmessage = $this->getTeamValidationMessage();
        $bmessage = $this->getBiddingValidationMessage();
        $validationFactory->extend(
            'have_vendor',
            [$this,'haveVendorRule'],
            $message
        );
        $validationFactory->extend(
            'have_teams',
            [$this,'haveTeamRule'],
            $tmessage
        );
        $validationFactory->extend(
            'have_bidding_docs',
            [$this,'haveBiddingRule'],
            $bmessage
        );
    }

    public function authorize()
    {
        // abort_if(Gate::denies('user_alert_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        return [
            'actionType' => 'required|have_vendor|have_teams|have_bidding_docs',
        ];
    }

    public function haveVendorRule($attribute, $value, $parameters)
    {
        if(strtolower($value) == 'submit'){
            if($this->tender && $this->tender->tender_method == 'APPOINTMENT'){
                return $this->tender->vendors->count() > 0;
            }else if($this->tender && $this->tender->tender_method != 'COMPETITIVE'){
                return $this->tender->vendors->count() >= 3;
            }
            return true;
        }
        return true;
    }

    public function getValidationMessage()
    {
        if($this->tender && $this->tender->tender_method == 'APPOINTMENT'){
            return __('validation.have_one_vendor');
        }else if($this->tender && $this->tender->tender_method != 'COMPETITIVE'){
            return __('validation.have_three_vendor');
        }
        return '';
    }

    public function haveTeamRule($attribute, $value, $parameters)
    {
        if(strtolower($value) == 'submit'){
            return $this->tender->evaluator->count() > 0;
        }
        return true;
    }

    public function getTeamValidationMessage()
    {
        return __('validation.have_teams');
    }

    public function haveBiddingRule($attribute, $value, $parameters)
    {
        if(strtolower($value) == 'submit'){
            $preq = $this->tender->biddingDocument->where('stage_type',1)->count() > 0;
            $tech = $this->tender->biddingDocument->where('stage_type',3)->count() > 0;
            $comm = $this->tender->biddingDocument->where('stage_type',4)->count() > 0;
            // Log::info([$this->tender->prequalification, $preq,$tech,$comm]);
            if($this->tender){
                if($this->tender->prequalification == 1){
                    return $preq && $tech && $comm;
                }else{
                    return $tech && $comm;
                }
            }else{
                return false;
            }
        }
        return true;
    }

    public function getBiddingValidationMessage()
    {
        // Log::info($this->tender->prequalification);
        if($this->tender){
            if($this->tender->prequalification == 1){
                return __('validation.have_bidding_docs_preq');
            }else{
                return __('validation.have_bidding_docs_nopreq');
            }
        }else{
            return 'Tender is invalid';
        }
}

}
