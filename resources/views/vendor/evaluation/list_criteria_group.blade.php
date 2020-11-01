@extends('layouts.one_column')

@php
$formName = "frmCriteria";
@endphp

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">
        <i class="fa fa-list mr-2"></i>{{__('navigation.criteria_group')}}
    </span>
</div>
<div class="card-header-right">
    @if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_group_modify'))
    <button id="btn_create" class="btn btn-sm btn-success" data-toggle="modal" data-target="#{{$formName}}_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('common.new_entry')}}</button>
    @endif
</div>
@endsection

@section('contentbody')
<div class="has-footer">
        <div class="card-fixed">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th>{{__('homepage.action')}}</th>
                        @foreach ($fields as $field)
                        <th>{{__('homepage.'.$field)}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('footer')
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
        </div>
    </div>
</div>
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __("homepage.create_new_entry"),
        'contents'=>'',
        'form_layout'=>'vendor.evaluation.form_criteria_group',
        'form_name'=>$formName,
    ];
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script>
var table;
require(["jquery","datatablesb4"], function () {
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#page_numbers").ready(function () {
            $("#datatable_serverside_paginate").appendTo($("#page_numbers"));
            $("#page_numbers").append('<input id="input-page" class="form-control form-control-sm" type="number" min="1" max="1000">')
            $("#datatable_serverside_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
            $('#input-page').keypress(function (event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    table.page($(this).val() * 1 - 1).draw(false);
                }
            });
        });

        @if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_modify'))
        $('#btn_create').click(function(){
            $('#id').val('');
            $('#name').val('');
        });
        @endif

        options = getDTOptions();
        options.ajax.url = "{{ route('vendor.evaluation.criteria_group_data') }}";
        options.select=undefined;
        options.columns=[
                {data: 'id', name: 'id'},
        @foreach ($fields as $field)
        {data: '{{$field}}', name: '{{$field}}'},
        @endforeach
        ];
        options.columnDefs=[{
                "render": function ( data, type, row ) {
                    @if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_group_modify'))
                    return ''+
                        '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.name+'"><i class="fa fa-edit"></i></a>'+
                        '<a onClick="deleteServerside(this)" style="cursor:pointer" data-id="'+data+'" data-label="'+row.name+'"><i class="fa fa-trash"></i></a>'+
                    '';
                    @else
                    return '';
                    @endif
                },
                "className": 'text-center',
                "targets": 0,
            },{
                "render": function ( data, type, row, dt ) {
                    var column = dt.settings.aoColumns[dt.col].data;
                    switch(column){
                        case 'created_at':
                            return moment(data).format(uiDatetimeFormat);
                        break;
                        default: 
                            return data; 
                        break;
                    }
                },
                "targets": "_all"
            }
        ];

        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);

        @if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_group_modify'))
        $('#{{$formName}}-save').off('click').on('click',function(){
            let frmId = '#{{$formName}}';

            if ($(frmId)[0].checkValidity()) {
                let frmData = parseFormData('{{$formName}}');
                $(frmId+'_fieldset').attr("disabled",true);
                $.ajax({
                    url : "{{ route('vendor.evaluation.criteria_group_store') }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Group "+$(frmId+' #name').val()+" saved.", "success", 3000);
                        setTimeout(() => {
                            table.draw(false);
                            $(frmId+'_modal .close').click();
                            $(frmId)[0].reset();
                            $(frmId+'_fieldset').attr("disabled",false);
                        }, 1000);

                    }else{
                        showAlert("Group not saved.", "danger", 3000);
                        $(frmId+'_fieldset').attr("disabled",false);
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                });
            }else{
                showAlert("Please complete the form", "danger");
            }
            return false;
        });
        @endif
    });
});

@if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_group_modify'))
function edit(obj){
    var data = table.row('#'+$(obj).data('id')).data();

    $('#id').val(data.id);
    $('#name').val(data.name);
    
    $('#{{$formName}}_modal').modal();
}
function deleteServerside(obj){
    $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
    $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('vendor.evaluation.criteria_group') }}/"+$(obj).data('id'),
        }).done(function(data) {
            if(data.success==true){
                $('#delete_modal .close').click();
                showAlert("Group "+$(obj).data('label')+" deleted.", "danger", 3000);
                table.draw(false);
            }else{
                showAlert(data.message, "danger", 3000);
            }
        });
        return false;
    });
}
@endif
</script>
@endsection
