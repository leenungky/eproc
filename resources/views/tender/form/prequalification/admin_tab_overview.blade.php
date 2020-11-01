<div class="tab-body">
    <div class="has-footer has-tab" style="padding: 0">
        @if($statusProcess == 'registration' || $statusProcess == 'registration-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_qualification')}}</p>
        </div>
        @elseif($statusProcess == 'started-pq' || $statusProcess == 'started-pq-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_qualification2')}}</p>
        </div>
        @elseif($statusProcess == 'opened-pq' || $statusProcess == 'opened-pq-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_qualification3')}}</p>
        </div>
        @endif
        <div id="card-schedule" class="card">
            <div class="card-body card-schedule" style="padding-top: 20px;">
                <div class="">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.start_date', ['type' => __('tender.schedule_type_label.pre_qualification')])}}</th>
                            <td class="text-center" style="width: 2%">:</td>
                            <td style="width: 20%">{{$schedule ? $schedule->start_date : ''}} </td>
                            <th class="text-right" style="width: 20%"></th>
                            <td class="text-center" style="width: 2%"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.end_date', ['type' => __('tender.schedule_type_label.pre_qualification')])}}</th>
                            <td class="text-center" style="width: 2%">:</td>
                            <td style="width: 20%">{{$schedule ? $schedule->end_date : ''}} </td>
                            <th class="text-right" style="width: 20%"></th>
                            <td class="text-center" style="width: 2%"></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="">
            <table id="dt-vendor-submission" class="table table-sm table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        @foreach ($tenderData['process_prequalification']['fields1'] as $field)
                            <th class="{{$field}}">{{__('tender.'.$field)}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="app-footer">
        <div class="app-footer__inner top-border">
            <div class="app-footer-left">
                <div class="page_numbers" style="display:inherit"></div>
            </div>
            <div class="app-footer-right">
                @if($statusProcess == 'registration')
                    <button class="btn btn-success btn_start_flow" @if(!$canStart) disabled @endif>
                        <i class="fa fa-play"></i>&nbsp;&nbsp;&nbsp;{{__('common.start')}}</button>
                @elseif($statusProcess == 'started-pq')
                    <button class="btn btn-success btn_open_flow ml-2" data-action="{{$workflowValues[1]}}" @if(!$canOpen) disabled @endif>
                        <i class="fa fa-play"></i>&nbsp;&nbsp;&nbsp;{{__('common.open')}}</button>
                @elseif($statusProcess == '')
                    <button class="btn btn-primary btn_next_flow ml-2">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                @endif
            </div>
        </div>
    </div>
</div>