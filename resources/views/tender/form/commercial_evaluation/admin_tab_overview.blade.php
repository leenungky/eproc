<div class="tab-body">
    <div class="has-footer has-tab" style="padding: 0">
        @if($statusProcess == 'registration' || $statusProcess == 'registration-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_qualification')}}</p>
        </div>
        @elseif($statusProcess == 'started-3' || $statusProcess == 'started-3-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_qualification2')}}</p>
        </div>
        @elseif($statusProcess == 'started-4' || $statusProcess == 'finish-3-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_com2')}}</p>
        </div>
        @elseif($statusProcess == 'opened-3' || $statusProcess == 'opened-3-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_tc3')}}</p>
        </div>
        @elseif($statusProcess == 'opened-4' || $statusProcess == 'opened-4-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_com3')}}</p>
        </div>
        @elseif($statusProcess == 'finish-3' || $statusProcess == 'finish-3-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_tc4')}}</p>
        </div>
        @elseif($statusProcess == 'finished-4' || $statusProcess == 'finish-3-view')
        <div class="alert alert-info alert-flat no-margin" role="alert">
            <p>{{__('tender.process.info_pre_com4')}}</p>
        </div>
        @endif
        <div id="card-schedule" class="card">
            <div class="card-body card-schedule" style="padding-top: 20px;">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.start_date', ['type' => __('tender.schedule_type_label.technical')])}}</th>
                        <td class="text-center" style="width: 2%">:</td>
                        <td style="width: 20%">{{$schedule ? $schedule->start_date : ''}} </td>
                        <th class="text-right" style="width: 20%"></th>
                        <td class="text-center" style="width: 2%"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.end_date', ['type' => __('tender.schedule_type_label.technical')])}}</th>
                        <td class="text-center" style="width: 2%">:</td>
                        <td style="width: 20%">{{$schedule ? $schedule->end_date : ''}} </td>
                        <th class="text-right" style="width: 20%"></th>
                        <td class="text-center" style="width: 2%"></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="">
            <table id="dt-vendor-submission" class="table table-sm table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        @foreach ($tenderData['process_commercial_evaluation']['fields1'] as $field)
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
        <div class="app-footer__inner">
            <div class="app-footer-left">
                <div class="page_numbers" style="display:inherit"></div>
            </div>
            <div class="app-footer-right">
                {{-- @include('tender.form.commercial_evaluation.admin_button_technical') --}}
                @if(in_array($statusProcess,['registration-','registration-4']))
                    <button class="btn btn-success btn_start_com" @if(!$canStart) disabled @endif>
                        <i class="fa fa-play"></i>&nbsp;&nbsp;&nbsp;{{__('tender.process.start_com')}}</button>
                @elseif($statusProcess == '')
                    <button class="btn btn-primary btn_next_flow ml-2">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                @endif
            </div>
        </div>
    </div>
</div>
