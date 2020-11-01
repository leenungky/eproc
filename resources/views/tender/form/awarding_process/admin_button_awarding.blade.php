<button class="btn btn-warning ml-2 btn_awarding_bottom" id="btn_resubmit_awarding" data-action-type="resubmit"
    data-modal-title="{{__('tender.process.btn_resubmit_awarding')}}"
    data-alert-message="{{__('tender.process.message_resubmit_awarding')}}" @if(!$canReSubmit) disabled
    @endif>
    <i class="fa fa-undo-alt"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_resubmit_awarding')}}</button>

<button class="btn btn-success ml-2 btn_awarding_bottom" id="btn_submit_awarding" data-action-type="submit"
    data-modal-title="{{__('tender.process.btn_submit_awarding')}}"
    data-alert-message="{{__('tender.process.message_submit_awarding')}}" @if(!$canSubmit) disabled
    @endif>
    <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_submit_awarding')}}</button>

<button class="btn btn-success ml-2 btn_awarding_bottom" id="btn_submit_awarding" data-action-type="submit_po"
    data-modal-title="{{__('tender.process.btn_submit_po')}}"
    data-alert-message="{{__('tender.process.message_submit_po')}}">
    {{-- data-alert-message="{{__('tender.process.message_submit_po')}}" @if(!$canSubmitPO || $canReSubmit) disabled @endif> --}}
    {{-- data-alert-message="{{__('tender.process.message_submit_po')}}" @if(!$canSubmit) disabled @endif> --}}
    <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_submit_po')}}</button>

@if($next != $type)
<button class="btn btn-primary ml-2 btn_next_flow">
    {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
@endif
