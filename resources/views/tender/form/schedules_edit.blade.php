@section('contentbody')
<div class="has-footer" style="padding: 0">
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
            <form class="needs-validation" novalidate>
                <fieldset class="col-6">
                    <div class="form-group row mb-2">
                        <label for="start_date" class="col-5 col-form-label text-right">
                            {{__('tender.schedule.fields.start_date', ['type' => __('tender.schedule_type_label.'.$val)])}}
                        </label>
                        <div class="col-7" data-toggle="datetimepicker">
                            <input type="hidden" name="id" value="{{$sch->id ?? ''}}"/>
                            <input type="hidden" name="type" value="{{$key}}"/>
                            <input type="text" name="start_date" id="{{'start_date-'.$val}}"
                                class="form-control form-control-sm start_date datetimepicker-input"
                                data-target="#{{'start_date-'.$val}}"
                                data-toggle="datetimepicker"
                                data-value="{{$sch->start_date ?? ''}}"
                                autocomplete="off"
                                required="required"/>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="end_date" class="col-5 col-form-label text-right">
                            {{__('tender.schedule.fields.end_date', ['type' => __('tender.schedule_type_label.'.$val)])}}
                        </label>
                        <div class="col-7">
                            <input type="text" name="end_date" id="{{'end_date-'.$val}}"
                                class="form-control form-control-sm datetimepicker-input"
                                data-toggle="datetimepicker" required="required"
                                data-value="{{$sch->end_date ?? ''}}"
                                autocomplete="off"
                                data-target="#{{'end_date-'.$val}}"/>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-5 col-form-label text-right"></label>
                        <div class="col-7">
                            <button class="btn btn-save btn-success mr-2" type="submit" disabled>
                                <i class="fa fa-save"></i> {{__('tender.save')}}</button>
                        </div>
                    </div>
                </fieldset>
            </form>
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
        <div class="card-body col-sm-12" style="padding-top: 20px;">
            <div class="col-12">
                <form class="needs-validation row" novalidate>
                    <div class="col-6">
                        <div class="form-group row mb-2">
                            <label for="sign_by_id1_0" class="col-5 col-form-label text-right">
                                {{__('tender.schedule.fields.proposed_by')}}
                            </label>
                            <div class="col-7">
                                @php
                                    $sign = null;
                                    if(count($tenderData['signatures']) > 0){
                                        $sign = $tenderData['signatures']->first(function($item) use($key) { return $item->type == 1;});
                                        $proposedBy = $sign ? $sign->sign_by_id : null;
                                    }else{
                                        $proposedBy = $tender->createdBy ? $tender->createdBy->id : null;
                                    }
                                @endphp
                                <select name="sign_by_id1_0" required class="sign_by custom-select custom-select-sm"
                                    data-id="{{$sign ? $sign->id : ''}}"
                                    data-order="0"
                                    @if(($tender->workflow_status != 'tender_requirements') && $tender->workflow_values != 'procurement_approval-rejected') disabled @endif
                                    data-type="1">
                                    <option></option>
                                    @php $selected=($proposedBy ?? auth()->user()->id); @endphp
                                    @php $position=""; @endphp
                                    @foreach ($buyerOptions as $key => $val)
                                    <option value="{{$val->user_id}}"
                                        @if($selected == $val->user_id) 
                                            selected 
                                            @php $position = !is_null($sign) ? $val->position : ''; @endphp
                                        @endif
                                        data-position="{{$val->position}}"
                                        >{{$val->buyer_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group row mb-2">
                            <label for="position1_0" class="col-4 col-form-label text-right">
                                {{__('tender.schedule.fields.position')}}
                            </label>
                            <div class="col-8">
                                <input type="text" name="position1_0" class="form-control form-control-sm"
                                value="{{$sign ? $sign->position : (!empty($position) ? $position : '')}}" required readonly/>
                            </div>
                        </div>
                    </div>

                    @foreach ($approvers as $k => $appr)
                    @php
                        $sign = null;
                        $proposedBy = null;
                        if(count($tenderData['signatures']) > 0){
                            $sign = $tenderData['signatures']->first(function($item) use($appr) { return $item->type == 2 && $item->order == $appr->order;});
                            $proposedBy = $sign ? $sign->sign_by_id : null;
                        }
                    @endphp
                    <div class="col-6">
                        <div class="form-group row mb-2">
                            <label for="sign_by_id2_{{$appr->order}}" class="col-5 col-form-label text-right">
                                {{__('tender.schedule.fields.approved_by')}}
                            </label>
                            <div class="col-7">
                                <select name="sign_by_id2_{{$appr->order}}" required class="sign_by custom-select custom-select-sm"
                                    data-id="{{$sign ? $sign->id : ''}}"
                                    data-order="{{$appr->order}}"
                                    data-type="2"
                                    @if(($tender->workflow_status != 'tender_requirements') && $tender->workflow_values != 'procurement_approval-rejected') disabled @endif
                                    >
                                    <option></option>
                                    @php $selected=($sign && $sign->sign_by_id ?  $sign->sign_by_id : $appr->user_id); @endphp
                                    @php $position=""; @endphp
                                    @foreach ($buyerOptions as $key => $val)
                                    <option value="{{$val->user_id}}"
                                        @if($selected == $val->user_id) 
                                            selected 
                                            @php $position=$sign && $sign->sign_by_id ? $val->position : ''; @endphp
                                        @endif
                                        data-position="{{$val->position}}"
                                        >{{$val->buyer_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="form-group row mb-2">
                            <label for="position2_{{$appr->order}}" class="col-4 col-form-label text-right">
                                {{__('tender.schedule.fields.position')}}
                            </label>
                            <div class="col-8">
                                <input type="text" name="position2_{{$appr->order}}" class="form-control form-control-sm"
                                    value="{{$sign ? $sign->position : (!empty($position) ? $position : '')}}" required readonly/>
                            </div>
                        </div>
                    </div>
                    @php $position=""; @endphp
                    @endforeach

                    <div class="col-6">
                        <div class="form-group row mb-2">
                            <label for="" class="col-5 col-form-label text-right"></label>
                            <div class="col-7">
                                <button class="btn btn-save btn-success mr-2" type="submit" 
                                @if(($tender->workflow_status != 'tender_requirements') && $tender->workflow_values != 'procurement_approval-rejected') disabled @endif
                                >
                                    <i class="fa fa-save"></i> {{__('tender.save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

