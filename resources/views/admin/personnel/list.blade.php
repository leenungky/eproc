@extends('layouts.one_column')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title"><i class="fa fa-list mr-1"></i> {{ __('personnel.personnel') }}</span>
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
        'title'=> __("personnel.add"),
        'contents'=>'',
        'form_layout'=>'admin.personnel.form_add',
        'form_name'=>'frmadd',
    ];
    $modal2 = [
        'title'=> __("personnel.edit"),
        'contents'=>'',
        'form_layout'=>'admin.personnel.form_edit',
        'form_name'=>'frmedit',
    ];
    $modal3 = [
        'title'=> __("homepage.change_password"),
        'contents'=>'',
        'form_layout'=>'admin.personnel.form_password',
        'form_name'=>'frmpassword',
    ];
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_common',$modal2)
@include('layouts.modal_common',$modal3)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script type="text/javascript">
    var table;
    var selectedRows = [];
    var selectedData = [];
    require(["datatablesb4","dt.plugin.select","select2"], function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
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
        options = getDTOptions();
        options.ajax.url = "{{ route('personnel.data') }}";
        options.select=undefined;
        options.columns=[
                {data: 'id', name: 'id'},
        @foreach ($fields as $field)
        {data: '{{$field}}', name: '{{$field}}'},
        @endforeach
        ];
        
        options.columnDefs=[
            {
                "render": function ( data, type, row ) {
                    return ''+
                        '<a onClick="chpw(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.userid+' - '+row.name+'"><i class="fa fa-key"></i></a>'+
                        '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.userid+' - '+row.name+'"><i class="fa fa-edit"></i></a>'+
                        '<a onClick="deleteServerside(this)" style="cursor:pointer" data-id="'+data+'" data-label="'+row.userid+' - '+row.name+'"><i class="fa fa-trash"></i></a>'+
                    '';
                },
                "className": 'text-center',
                "targets": 0
            },
            {
                "render": function ( data, type, row ) {
                    // return '<a href="{{ route("tender.list") }}/'+row.id+'">'+data+'</a>';
                    if(data==0){
                        return "<span class='badge badge-danger'>{{__('common.non_active')}}</span>";
                    }else{
                        return "<span class='badge badge-success'>{{__('common.active')}}</span>";
                    }
                },
                "targets": 'status'
            },
        ];
        options.initComplete= function () {
            var tr = document.createElement("tr");
            var api = this.api();
            $('#datatable_serverside thead th').each(function (id, el) {
                var th = document.createElement("th");
                var title = $(this).text();
                // if (id == $('#datatable_serverside thead th').length - 1) {
                if (id == 0) {
                } else {
                    $(document.createElement("input"))
                        //.attr("placeholder", title)
                        .addClass('form-control form-control-sm')
                        .appendTo(th)
                        .on("change", function () {
                            table.column(id).search(this.value).draw();
                    });
                }
                $(th).appendTo($(tr));
            });
            $(tr).appendTo($('#datatable_serverside thead'));
        }

        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);

        //## Initialize Buttons
        $('#addroles, #editroles').select2({
            theme: 'bootstrap4',
        });
        $('#frmadd #clear-roles').click(function(e){
            e.preventDefault();
            $('#addroles').val(null).trigger('change');
            return false;
        });
        $('#frmedit #clear-roles').click(function(e){
            e.preventDefault();
            $('#editroles').val(null).trigger('change');
            return false;
        });
        $('#frmadd-save').click(function(){
            let frmId = '#frmadd';
            if ($(frmId)[0].checkValidity()) {
                let frmData = new FormData($(frmId+'')[0]);
                $(frmId+'_fieldset').attr("disabled",true);
                $.ajax({
                    url : "{{ route('personnel.store') }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {

                    if(response.success){
                        showAlert("User "+$(frmId+' #name').val()+" ("+$(frmId+' #userid').val()+") saved.", "success", 3000);
                        setTimeout(() => {
                            let row = {
                                'id': $(frmId+' #id').val() =='' ? response.data.id : $(frmId+' #id').val(),
                                'userid': $(frmId+' #userid').val(),
                                'name': $(frmId+' #name').val(),
                                'position': '',
                                'status': '1',
                                'email': '',
                                'roles': $(frmId+' .roles').val().reduce(function(o, val) { o['name'] = val; return o; }, {}),
                            }
                            if($(frmId+' #id').val()=='') {
                                table.row.add(row).draw();
                            }else{
                                table.rows('#'+$(frmId+' #id').val()).remove().draw();
                                table.row.add(row).draw();
                            }
                            $(frmId+'_modal .close').click();
                            $(frmId)[0].reset();
                            $('#addroles').val(null).trigger('change');
                            $(frmId+'_fieldset').attr("disabled",false);
                        }, 1000);

                    }else{
                        showAlert("Document not saved.", "danger", 3000);
                        $(frmId+'_fieldset').attr("disabled",false);
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                });

            }else{
                showAlert("Please complete the form", "danger");
            }
        });
        $('#frmedit-save').click(function(){
            let frmId = '#frmedit';
            if ($(frmId)[0].checkValidity()) {
                let frmData = new FormData($(frmId+'')[0]);
                $(frmId+'_fieldset').attr("disabled",true);
                $.ajax({
                    url : "{{ route('personnel.list') }}/"+$(frmId+" #id").val(),
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {

                    if(response.success){
                        showAlert("User "+$(frmId+' #name').val()+" ("+$(frmId+' #userid').val()+") saved.", "success", 3000);
                        setTimeout(() => {
                            let row = {
                                'id': $(frmId+' #id').val() =='' ? response.data.id : $(frmId+' #id').val(),
                                'userid': $(frmId+' #userid').val(),
                                'name': $(frmId+' #name').val(),
                                'position': $(frmId+' #position').val(),
                                'status': $(frmId)[0].status.value,
                                'email': $(frmId+' #email').val(),
                                'roles': $(frmId+' .roles').val().reduce(function(o, val) { o['name'] = val; return o; }, {}),
                            }
                            if($(frmId+' #id').val()=='') {
                                table.row.add(row).draw();
                            }else{
                                table.rows('#'+$(frmId+' #id').val()).remove().draw();
                                table.row.add(row).draw();
                            }
                            $(frmId+'_modal .close').click();
                            $(frmId)[0].reset();
                            $('#addroles').val(null).trigger('change');
                            $(frmId+'_fieldset').attr("disabled",false);
                        }, 1000);

                    }else{
                        showAlert("Document not saved.", "danger", 3000);
                        $(frmId+'_fieldset').attr("disabled",false);
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                });

            }else{
                showAlert("Please complete the form", "danger");
            }
        });

        //ch pw

        $('#new_password').change(function(){
            $('#repeat_new_password').attr('pattern',this.value);
        });
        $('.togglepassword').on('click',function(){
            if($(this).hasClass('fa-eye')){
                $(this).closest('.input-group').find('input').attr('type','text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            }else{
                $(this).closest('.input-group').find('input').attr('type','password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        $('#frmpassword-save').click(function(){
            let frmId = '#frmpassword';
            if ($(frmId)[0].checkValidity()) {
                let frmData = new FormData($(frmId+'')[0]);
                $(frmId+'_fieldset').attr("disabled",true);
                $.ajax({
                    url : "{{ route('personnel.list') }}/"+$(frmId+" #id").val()+"/password",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {

                    if(response.success){
                        showAlert("User "+$(frmId+' #name').val()+" ("+$(frmId+' #userid').val()+") saved.", "success", 3000);
                        $(frmId+'_modal .close').click();
                        $(frmId)[0].reset();
                        $(frmId+'_fieldset').attr("disabled",false);

                    }else{
                        showAlert("Password not saved. "+response.message, "danger", 3000);
                        $(frmId+'_fieldset').attr("disabled",false);
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                });

            }else{
                showAlert("Please complete the form", "danger");
            }
        });

    });
    function edit(obj){
        var data = table.row('#'+$(obj).data('id')).data();
        var form = '#frmedit ';
        var roles = [];
        $.each(data.roles, function(k,v){
            roles.push(v.name);
        });

        $(form)[0].reset();
        $('#editroles').val(null).trigger('change');
        $(form+'#id').val(data.id);
        $(form+'#name').val(data.name);
        $(form+'#userid').val(data.userid);
        $(form+'#email').val(data.email);
        $(form+'#position').val(data.position);
        $(form+'#status_'+data.status).attr('checked',true);
        $(form+'.roles').val(roles);
        $(form+'.roles').trigger('change');

        $('#frmedit_modal').modal();
    }
    function deleteServerside(obj){
        $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
        $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            $.ajax({
                type: 'DELETE',
                url: "{{ route('personnel.list') }}/"+$(obj).data('id'),
            }).done(function(data) {
                $('#delete_modal .close').click();
                showAlert("User "+$(obj).data('label')+" deleted.", "danger", 3000);
                table.draw(false);
            });
            return false;
        });
    }

    function chpw(obj){
        var data = table.row('#'+$(obj).data('id')).data();
        $('#frmpassword #name').val(data.name);
        $('#frmpassword #userid').val(data.id);

        $('#frmpassword_modal').modal();
    }

</script>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2-bootstrap4.min.css') }}">
<style>
.input-group-sm .input-group-append{
    height:31px;
}
</style>
@endsection
