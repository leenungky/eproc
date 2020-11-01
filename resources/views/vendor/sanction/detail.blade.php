@extends('layouts.one_column')

@php 
$formName = 'frmsanctionapproval';
$current = null;
$next = null;
foreach($sanctions as $s){
    if($s->status=='APPROVED') $current = $s;
    if($s->status=='SUBMITTED') $next = $s;
}
$uicolors = ['RED'=>'danger','YELLOW'=>'warning','GREEN'=>'success'];
@endphp

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">
        <a href="{{route('vendor.sanction')}}"><i class="fa fa-list mr-2"></i>{{__('navigation.sanction')}}</a> / {{$vendor->vendor_name}}
    </span>
</div>
<div class="card-header-right">
</div>
@endsection

@section('contentbody')
<div class="has-footer"><div class="card-fixed">
    <h3 class="ml-3 mt-3" id="partner_name">{{$vendor->vendor_name}} <span style="font-size:small">({{$vendor->purchase_org_description}})</span></h3>
    <div class="col-12 mb-2" style="background-color:#eee;padding:1rem">
        <table class="table table-borderless table-sm" style="margin-bottom:0">
            <tr>
                <th style="width:150px;">{{ __('homepage.register_number') }}</th>
                <td style="width:10px;">:</td>
                <td>{{$vendor->vendor_code}}</td>
                <td></td>
            </tr>
            <tr>
                <th>{{ __('homepage.sanction_type') }}</th>
                <td>:</td>
                <td>@if(isset($current->sanction_type)) <span class="badge badge-{{$uicolors[$current->sanction_type]}}">{{$current->sanction_type}} ({{$sanctionTypes[$current->sanction_type]}})</span> @endif</td>
                <td>@if(isset($next->sanction_type)) ( <span class="badge badge-{{$uicolors[$next->sanction_type]}}">{{$next->sanction_type}} ({{$sanctionTypes[$next->sanction_type]}})</span> ) @endif</td>
            </tr>
            <tr>
                <th>{{ __('homepage.valid_from_date') }}</th>
                <td>:</td>
                <td>{{isset($current->valid_from_date) ? date('d.m.Y',strtotime($current->valid_from_date)):''}}</td>
                <td>{{isset($next->valid_from_date) ? '( '.date('d.m.Y',strtotime($next->valid_from_date)).' )':''}}</td>
            </tr>
            <tr>
                <th>{{ __('homepage.valid_thru_date') }}</th>
                <td>:</td>
                <td>{{isset($current->valid_thru_date) ? date('d.m.Y',strtotime($current->valid_thru_date)):''}}</td>
                <td>{{isset($next->valid_thru_date) ? '( '.date('d.m.Y',strtotime($next->valid_thru_date)).' )':''}}</td>
            </tr>
            <tr>
                <th>{{ __('homepage.attachment') }}</th>
                <td>:</td>
                <td><a href="{{$storage.'/'.$vendor->id.'/'.($current->attachment ?? '')}}" target="_blank">{{$current->attachment ?? ''}}</a></td>
                <td>@if(isset($next->attachment)) ( <a href="{{$storage.'/'.$vendor->id.'/'.($next->attachment ?? '')}}" target="_blank">{{$next->attachment ?? ''}}</a> ) @endif</td>
            </tr>
        </table>
    </div>
    <h3 class="ml-3 mt-3" >{{__('homepage.comments_history')}}</h3>
    <table id="comment-history" class="table table-sm table-bordered table-striped table-vcenter table-ellipsis">
        <thead>
            <tr>
                <th>{{__('homepage.action')}}</th>
                @foreach ($commentHistoryFields as $key=>$field)
                <th title="{{__('homepage.'.$field)}}" style="width:{{$chFieldSizes[$key]}}px">{{__('homepage.'.$field)}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div></div>
@endsection

@section('footer')
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
            @if($isBuyerActive && $samePurchOrg && auth()->user()->can('vendor_sanction_approval') && !is_null($next))
            <button id="btn-revise" class="btn btn-danger mr-2">{{__('homepage.revise')}}</button>
            <button id="btn-approve" class="btn btn-success">{{__('homepage.approve')}}</button>
            @endif
        </div>
    </div>
</div>
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __("homepage.approval"),
        'contents'=>'',
        'form_layout'=>'vendor.sanction.form_approval',
        'form_name'=>$formName,
    ]
?>
@include('layouts.modal_common',$modal1)
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script>
var table;
var sanctionTypes = {!!json_encode($sanctionTypes)!!};
require(["moment"],function(){
require(["jquery","datatablesb4", "bootstrap-fileinput", "datetimepicker"], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if(table) if(typeof(table.destroy)=='function') table.destroy();
    let options = getOptions();
    options.ajax.url = "{{ route('vendor.sanction') }}/comment-history/{{$vendor->id}}";
    table = $('#comment-history').DataTable(options);
    $("#page_numbers").ready(function () {
        $("#history_paginate").appendTo($("#page_numbers"));
        $("#page_numbers").append('<input id="input-page" class="form-control form-control-sm" type="number" min="1" max="1000">')
        $("#history_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
        $('#input-page').keypress(function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                table.page($(this).val() * 1 - 1).draw(false);
            }
        });
    });
    @if($isBuyerActive && $samePurchOrg && auth()->user()->can('vendor_sanction_approval'))
    $("#btn-revise").click(function(){
        approval(false);
    })
    $("#btn-approve").click(function(){
        approval(true);
    })
    @endif
});
});
function getOptions(){
    var options = getDTOptions();
    options.select=undefined;
    options.autoWidth = false;
    options.columns=[
        {data: 'id', name: 'id'},
        @foreach ($commentHistoryFields as $field)
        @if($field == 'position')
        {data: null, name: 'position',
            render: function(data, type, row){
                return 'System';
            }
        },
        @else
        {data: '{{$field}}', name: '{{$field}}'},
        @endif
        @endforeach
    ];
    options.columnDefs=[
        {
            'visible':false,
            'targets':0,
        },
        {
            "render": function ( data, type, row, dt ) {
                var column = dt.settings.aoColumns[dt.col].data;
                switch(column){
                    case 'sanction_type':
                        let badge = 'badge-primary';
                        if(data=="RED") badge = 'badge-danger';
                        if(data=="YELLOW") badge = 'badge-warning';
                        if(data=="GREEN") badge = 'badge-success';
                        return '<span class="badge '+badge+'">'+data+' ('+sanctionTypes[data]+')</span>';
                    break;
                    case 'sanction_detail':
                        return '<a href="{{$storage}}/{{$vendor->id}}/'+data+'" target="_blank">'+data+'</a>';
                    break;
                    case 'valid_from_date':
                    case 'valid_thru_date':
                        return moment(data,dbDateFormat).format(uiDateFormat);
                    break;
                    case 'activity_date':
                        return moment(data,dbDatetimeFormat).format(uiDatetimeFormat);
                    break;
                    default: 
                        return data; 
                    break;
                }
            },
            "targets": "_all"
        }
    ];
    options.lengthChange = false;
    options.searching = false;
    options.order = [[0, 'desc']];
    options.columnDefs.unshift({
        "render": function ( data, type, row, dt ) {
            return '';
        },
        "className": 'text-center',
        "targets": 0
    });
    return options;
}
@if($isBuyerActive && $samePurchOrg && auth()->user()->can('vendor_sanction_approval'))
function approval(status){
    $('#id').val('{{$next->id ?? ''}}');
    $('#current').val('{{$current->id ?? ''}}');
    $('#comment').val('');
    $('#approved').val(status);
    let approval = status ? 'Approve' : 'Revise';
    $('#{{$formName}}_modal .modal-title').text(approval+' {{$vendor->vendor_name}} [{{$next->sanction_type ?? ''}}]');
    $('#{{$formName}}_modal #message').html('Are you sure to '+approval.toLowerCase()+' sanction for {{$vendor->vendor_name}} [{{$next->sanction_type ?? ''}}] ?');
    $('#{{$formName}}_modal .modal-dialog').removeClass('modal-lg');
    $('#{{$formName}}_modal').modal();
    $('#{{$formName}}-save').off('click').on('click',function(){
        let frmId = '#{{$formName}}';
        if ($(frmId)[0].checkValidity()) {
            let frmData = parseFormData('{{$formName}}');
            $(frmId+'_fieldset').attr("disabled",true);
            Loading.Show();
            $.ajax({
                url : "{{ route('vendor.sanction_patch', $vendor->id) }}",
                type : 'POST',
                data : frmData,
                cache : false,
                processData: false,
                contentType: false,
            }).done(function(response, textStatus, jqXhr) {

                if(response.success){
                    showAlert(response.message, "success", 3000);
                    setTimeout(() => {
                        table.draw(false);
                        $(frmId+'_modal .close').click();
                        $(frmId)[0].reset();
                        $(frmId+'_fieldset').attr("disabled",false);
                        location.reload();
                    }, 1000);

                }else{
                    showAlert("Data not saved. "+response.message, "danger", 3000);
                    $(frmId+'_fieldset').attr("disabled",false);
                }
                Loading.Hide();

            }).fail(function(jqXHR, textStatus, errorThrown) {
                $(frmId+'_fieldset').attr("disabled",false);
                Loading.Hide();
            });

        }else{
            showAlert("Please complete the form", "danger");
        }
    });
}
@endif
</script>
@endsection