@extends('tender.show')

@section('contentbody')
<div class="has-footer" style="padding: 0">
    <div id="card-schedule" class="card" style="margin-bottom: 10px">
        <div class="card-header">
            <div class="card-header-left"><span class="heading-title">{{__('tender.process.schedule_title')}}</span></div>
        </div>
        <div class="card-body card-schedule" style="padding-top: 20px;">
            <table class="table table-borderless">
                <tr>
                    <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.start_date', ['type' => __('tender.schedule_type_label.registration')])}}</th>
                    <td class="text-center" style="width: 2%">:</td>
                    <td style="width: 20%">{{$schedule ? $schedule->start_date : ''}} </td>
                    <th class="text-right" style="width: 20%"></th>
                    <td class="text-center" style="width: 2%"></td>
                    <td></td>
                </tr>
                <tr>
                    <th class="text-right" style="width: 25%">{{__('tender.schedule.fields.end_date', ['type' => __('tender.schedule_type_label.registration')])}}</th>
                    <td class="text-center" style="width: 2%">:</td>
                    <td style="width: 20%">{{$schedule ? $schedule->end_date : ''}} </td>
                    <th class="text-right" style="width: 20%"></th>
                    <td class="text-center" style="width: 2%"></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="card-header">
        <div class="card-header-left"><span class="heading-title">{{__('tender.process.tender_participants')}}</span></div>
    </div>
    <table id="dt-process-registration" class="table table-sm table-bordered table-striped table-vcenter">
        <thead>
            <tr>
                @foreach ($tenderData['tender_vendors']['fields'] as $field)
                    <th class="{{$field}}">{{__('tender.'.$field)}}</th>
                @endforeach
            </tr>
        </thead>
    </table>
</div>
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
            <ul class="nav">
                <li id="action_group" class="nav-item">
                    <button id="btn_next_flow" class="btn btn-primary">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection


@section('modals')
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
<script type="text/javascript">
var table;
require(["datatablesb4","dt.plugin.select",'datetimepicker'], function(datetimepicker){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#btn_next_flow').click(function(){
        onClickNext();
    });

    $("#page_numbers").ready(function () {
        $("#dt-process-registration_paginate").appendTo($("#page_numbers"));
        $("#dt-process-registration_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
    });

    let dtOptions = getDTOptions();
    let options = {
        deferRender: dtOptions.deferRender,
        rowId: dtOptions.rowId,
        lengthChange: false,
        searching: false,
        processing: true,
        language: dtOptions.language,
        ajax : "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}",
        columns: [
            {data: 'vendor_code', name: 'vendor_code',"width": 100},
            {data: 'vendor_name', name: 'vendor_name'},
            {data: 'status', name: 'status', "width": 100},
            {data: 'updated_at', name: 'updated_at', "width": 100},
        ],
        columnDefs:[
            {
                "render": function ( data, type, row, dt ) {
                    var column = dt.settings.aoColumns[dt.col].data;
                    let tmp = data;
                    switch(column){
                        case 'status':
                            return row.status_text;
                        case 'updated_at':
                            return moment(data).format(uiDatetimeFormat);
                        default:
                            return data;
                    }
                },
                "targets": "_all"
            }
        ],
    };
    //## Initilalize Datatables
    table = $('#dt-process-registration').DataTable(options);
});
</script>
@endsection

