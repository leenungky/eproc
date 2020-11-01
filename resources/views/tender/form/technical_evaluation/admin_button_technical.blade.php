@if($statusProcess == 'registration-')
    <button class="btn btn-success btn_start_tc" @if(!$canStart) disabled @endif>
        <i class="fa fa-play"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.start_tc')}}</button>
@elseif($statusProcess == 'started-3' && (count($workflowValues) > 1 && (int)$workflowValues[1] == 3))
    <button class="btn btn-success btn_open_tc" data-action="{{$workflowValues[1]}}" @if(!$canOpen) disabled @endif>
        <i class="fa fa-play"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.open_tc')}}</button>
@elseif($statusProcess == 'opened-3' && (count($workflowValues) > 1 && (int)$workflowValues[1] == 3))
    <button class="btn btn-warning ml-2 btn_request_resubmission" @if(!$canOpen) disabled @endif>
        <i class="fa fa-undo-alt"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.request_resubmission_tc')}}</button>
    {{-- <button class="btn btn-success ml-2 btn_finish_tab" @if(!$canFinish) disabled @endif>
        <i class="fa fa-paper-plane"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_finish_tc')}}</button> --}}
@elseif($statusProcess == '')
    <button class="btn btn-primary btn_next_flow ml-2">
        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
@endif
