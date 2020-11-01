@section('contentbody')
<div class="has-footer content-view" style="padding: 0">
    @foreach ($scheduleTypes as $key => $val)
    @php $sch = $tenderData['schedules']->first(function($item) use($key) { return $item->type == $key;}); @endphp
    <div id="{{'card-'.$val}}" class="card">
        <div class="card-header">
            <div class="card-header-left">
                <span class="heading-title"><small>{{__('tender.schedule_type.'.$val)}}</small></span>
            </div>
            <div class="card-header-right">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                        name="s-check" value="true" disabled
                        @if($sch) checked @endif
                        >
                    <label class="custom-control-label" for="s-check">&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="card-body card-schedule" style="padding-top: 20px;">
            <table class="table table-borderless">
                <tr>
                    <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.start_date', ['type' => __('tender.schedule_type_label.'.$val)])}}</th>
                    <td class="text-center" style="width: 2%">:</td>
                    <td style="width: 20%">{{$sch ? $sch->start_date : ''}} </td>
                    <th class="text-right" style="width: 20%"></th>
                    <td class="text-center" style="width: 2%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.end_date', ['type' => __('tender.schedule_type_label.'.$val)])}}</th>
                    <td class="text-center" style="width: 2%">:</td>
                    <td style="width: 20%">{{$sch ? $sch->end_date : ''}} </td>
                    <th class="text-right" style="width: 20%"></th>
                    <td class="text-center" style="width: 2%"></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    @endforeach


    <div id="card-signature" class="card">
        <div class="card-header">
            <div class="card-header-left">
                <span class="heading-title"><small>{{__('tender.schedule.signature_title')}}</small></span>
            </div>
            <div class="card-header-right">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                        name="s-check" value="true" disabled
                        @if(count($tenderData['signatures']) > 0) checked @endif>
                    <label class="custom-control-label" for="s-check">&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="card-body" style="padding-top: 20px;">
            <table class="table table-borderless">
                @php
                    $sign = $tenderData['signatures']->first(function($item) use($key) { return $item->type == 1;});
                @endphp
                <tr>
                    <th class="text-right" style=" width: 25%">{{__('tender.schedule.fields.proposed_by')}}</th>
                    <td class="text-center" style="width: 2%">:</td>
                    <td style="width: 20%">{{$sign ? $sign->sign_by : ''}} </td>
                    <th class="text-right" style="width: 20%">{{__('tender.schedule.fields.position')}}</th>
                    <td class="text-center" style="width: 2%">:</td>
                    <td>{{$sign ? $sign->position : ''}}</td>
                </tr>
                @foreach ($approvers as $k => $appr)
                    @php
                        $sign = $tenderData['signatures']->first(function($item) use($appr) { return $item->type == 2 && $item->order == $appr->order;});
                    @endphp
                    <tr>
                        <th class="text-right" style=" width: 25%">{{__('tender.schedule.fields.approved_by')}}</th>
                        <td class="text-center" style="width: 2%">:</td>
                        <td style="width: 20%">{{$sign ? $sign->sign_by : ''}} </td>
                        <th class="text-right" style="width: 20%">{{__('tender.schedule.fields.position')}}</th>
                        <td class="text-center" style="width: 2%">:</td>
                        <td>{{$sign ? $sign->position : ''}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection

