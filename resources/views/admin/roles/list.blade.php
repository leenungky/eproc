@extends('layouts.one_column')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title"><i class="fa fa-list mr-2"></i>Role Management</span>
</div>
<div class="card-header-right">
    <div class="button-group">
        <button id="btn_create" class="btn btn-sm btn-success" data-toggle="modal" data-target="#frmadd_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('common.new_entry')}}</button>
    </div>
</div>
@endsection

@section('contentbody')
<div class="has-footer">
    <div class="card-fixed">
        <table id="datatable_serverside" class="table table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th class="action">{{__('homepage.action')}}</th>
                    @foreach ($fields as $field)
                    <th class="{{$field}}">{{__('homepage.'.$field)}}</th>
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
        'form_layout'=>'admin.roles.form_add',
        'form_name'=>'frmadd',
    ];
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script type="text/javascript">
var table;
require(["jquery","datatablesb4","select2"], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // $('.modal-dialog').removeClass('modal-lg');

    $("#page_numbers").ready(function () {
        $("#datatable_serverside_paginate").appendTo($("#page_numbers"));
        $("#page_numbers").append('<input id="input-page" class="form-control form-control-sm" style="width:70px;" type="number" min="1">')
        $("#datatable_serverside_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
        $('#input-page').keypress(function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                table.page($(this).val() * 1 - 1).draw(false);
            }
        });
    });

    var options = getDTOptions();
    options.ajax.url = "{{ route('role.data') }}";
    options.select=undefined;
    options.columns=[
        {data: 'id', name: 'id', width: 50},
        @foreach ($fields as $field)
        {data: '{{$field}}', name: '{{$field}}'},
        @endforeach
    ];
    options.columnDefs=[
        {
            "render": function ( data, type, row ) {
                return ''+
                    '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.name+'"><i class="fa fa-edit"></i></a>'+
                    '<a onClick="deleteServerside(this)" style="cursor:pointer" data-id="'+data+'" data-label="'+row.name+'"><i class="fa fa-trash"></i></a>'+
                '';
            },
            "className": 'text-center',
            "targets": 'action'
        },
    ];
    options.initComplete = CustomDTtOptions.InitComplete;

    table = $('#datatable_serverside').DataTable(options);
    $('#permissions').select2({
        theme: 'bootstrap4',
        minimumInputLength: 1
    });

    $('#btn_create').click(function(){
        $('#id').val('');
        $('#permissions').val(null).trigger('change');
        $('#name').val('');
    });

    $('#frmadd-save').off('click').on('click',function(){
        let frmId = '#frmadd';
        if ($(frmId)[0].checkValidity()) {
            let frmData = new FormData($(frmId+'')[0]);
            $(frmId+'_fieldset').attr("disabled",true);
            $.ajax({
                url : "{{ route('role.store') }}",
                type : 'POST',
                data : frmData,
                cache : false,
                processData: false,
                contentType: false,
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    showAlert("Role "+$(frmId+' #name').val()+" saved.", "success", 3000);
                    setTimeout(() => {
                        table.draw(false);
                        $(frmId+'_modal .close').click();
                        $(frmId)[0].reset();
                        $(frmId+'_fieldset').attr("disabled",false);
                    }, 1000);

                }else{
                    showAlert("Role not saved.", "danger", 3000);
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
});
function edit(obj){
    var data = table.row('#'+$(obj).data('id')).data();
    var permissions = [];

    $.each(data.permissions, function(k,v){
        permissions.push(v.name);
    });
    $('#permissions').val(null).trigger('change');
    $('#id').val(data.id);
    $('#name').val(data.name);
    $('#permissions').val(permissions).trigger('change');
    $('#frmadd_modal').modal();
}
function deleteServerside(obj){
    $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
    $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('role.list') }}/"+$(obj).data('id'),
        }).done(function(data) {
            $('#delete_modal .close').click();
            showAlert("Role "+$(obj).data('label')+" deleted.", "danger", 3000);
            table.draw(false);
        });
        return false;
    });
}
</script>
<style>
.select2-results__option[aria-selected=true] { display: none;}
</style>
@endsection
