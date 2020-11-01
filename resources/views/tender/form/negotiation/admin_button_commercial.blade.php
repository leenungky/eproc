{{-- @if ($workflowValues[$actIndex] != "awarding_process") --}}
<a id="btn_print_nbe" target="_blank" href="" class="btn btn-outline-secondary btn_print_nbe"><i
        class="fa fa-file-excel"></i> NBE {{__('common.print')}}</a>
<button class="btn btn-warning ml-2 btn_negotiation_bottom" id="btn_request_resubmission"
    data-action-type="{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[3]}}"
    data-modal-title="{{__('tender.process.request_resubmission_negotiation')}}"
    data-alert-message="{{__('tender.process.message_resubmission_negotiation')}}" @if(!$canResubmission)
    disabled @endif>
    <i class="fa fa-undo-alt"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.request_resubmission_negotiation')}}</button>
<button class="btn btn-success ml-2 btn_negotiation_bottom" id="btn_finish_negotiation"
    data-action-type="{{\App\Enums\TenderSubmissionEnum::FLOW_STATUS[6]}}"
    data-modal-title="{{__('tender.process.btn_finish_negotiation')}}"
    data-alert-message="{{__('tender.process.message_finish_negotiation')}}" @if(!$canFinish) disabled @endif>
    <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_finish_negotiation')}}</button>
{{-- @endif

@if($next != $type) --}}
<button class="btn btn-primary btn_next_flow ml-2">
    {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
{{-- @endif --}}
