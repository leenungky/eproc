@if(in_array($statusProcess, ['registration-','registration-4']))
    <button class="btn btn-success btn_start_com" @if(!$canStart) disabled @endif>
        <i class="fa fa-play"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.start_com')}}</button>
@elseif($statusProcess == 'started-4' && (count($workflowValues) > 1 && (int)$workflowValues[1] == 4))
    <button class="btn btn-success btn_open_com" data-action="{{$workflowValues[1]}}" @if(!$canOpen) disabled @endif>
        <i class="fa fa-play"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.open_com')}}</button>
@elseif($statusProcess == 'opened-4' && (count($workflowValues) > 1 && (int)$workflowValues[1] == 4))
    <button class="btn btn-warning ml-2 btn_request_resubmission" @if(!$canOpen) disabled @endif>
        <i class="fa fa-undo-alt"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.request_resubmission_com')}}</button>
@elseif($statusProcess == '')
    <button class="btn btn-primary btn_next_flow ml-2">
        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
@endif
