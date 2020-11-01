@if($statusProcess == 'registration')
    <button class="btn btn-success btn_start_flow" @if(!$canStart) disabled @endif>
        <i class="fa fa-play"></i>&nbsp;&nbsp;&nbsp;{{__('common.start')}}</button>
@elseif($statusProcess == 'started-pq')
    <button class="btn btn-success btn_open_flow ml-2" data-action="{{$workflowValues[1]}}" @if(!$canOpen) disabled @endif>
        <i class="fa fa-play"></i>&nbsp;&nbsp;&nbsp;{{__('common.open')}}</button>
@elseif($statusProcess == 'opened-pq')
    <button class="btn btn-warning ml-2 btn_request_resubmission" @if(!$canOpen) disabled @endif>
        <i class="fa fa-undo-alt"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.btn_request_resubmission')}}</button>
@elseif($statusProcess == '')
    <button class="btn btn-primary btn_next_flow ml-2">
        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
@endif
