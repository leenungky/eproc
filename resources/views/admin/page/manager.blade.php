@extends('layouts.one_column')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">
        <i class="fa fa-list mr-2"></i>Page Manager
        {{config('locale')}}
    </span>
</div>
<div class="card-header-right">
    <div class="button-group">
        <button id="btn_create" class="btn btn-sm btn-success" data-toggle="modal" data-target="#frmpage_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('common.new_entry')}}</button>
    </div>
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

            <table id="datatable_serverside" class="table table-bordered table-striped table-vcenter">
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
        'title'=> '',
        'contents'=>'',
        'form_layout'=>'admin.page.form',
        'form_name'=>'frmpage',
    ]
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script>
var table;
require(["datatablesb4","summernote"], function () {
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
        options = getDTOptions();
        options.ajax.url = "{{ route('listpage') }}";
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
                        '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.title+' ['+row.language+']"><i class="fa fa-edit"></i></a>'+
                        '<a onClick="deleteServerside(this)" style="cursor:pointer" data-id="'+data+'" data-label="'+row.title+' ['+row.language+']"><i class="fa fa-trash"></i></a>'+
                    '';
                },
                "className": 'text-center',
                "targets": 0
            },
            {
                "visible": false,
                "targets": 4
            }
        ];
        options.initComplete = CustomDTtOptions.InitComplete;

        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);
        $('#btn_create').click(function(){
            $('#frmpage')[0].reset();
            $('#frmpage #id').val('');
            $('#frmpage_modal .modal-title').text("{{__('common.new_entry')}}");
            $('.summernote').summernote('code','');
        });
        $('.summernote').summernote({dialogsInBody: true});

        $('#frmpage-save').click(function(){
            let frmId = '#frmpage';
            if ($(frmId)[0].checkValidity()) {
                let frmData = new FormData($(frmId+'')[0]);
                $(frmId+'_fieldset').attr("disabled",true);
                $.ajax({
                    url : "{{ route('storepage') }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {

                    if(response.success){
                        showAlert("Page saved.", "success", 3000);
                        setTimeout(() => {
                            table.draw(false);
                            $(frmId+'_modal .close').click();
                            $(frmId)[0].reset();
                            $(frmId+'_fieldset').attr("disabled",false);
                        }, 1000);

                    }else{
                        showAlert("Page not saved.", "danger", 3000);
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
});
function edit(obj){
    var data = table.row('#'+$(obj).data('id')).data();
    var form = '#frmpage';

    $(form)[0].reset();
    $(form+'_modal .modal-title').text($(obj).data('label'));
    $(form+' #id').val(data.id);
    $(form+' #page_id').val(data.page_id);
    $(form+' #language').val(data.language);
    $(form+' #title').val(data.title);
    $(form+' #content').summernote('destroy');
    $(form+' #content').html(data.content);
    $(form+' #content').summernote({dialogsInBody: true});

    $('#frmpage_modal').modal();
}
function deleteServerside(obj){
        $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
        $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            $.ajax({
                type: 'DELETE',
                url: "{{ route('managepage') }}/"+$(obj).data('id'),
            }).done(function(data) {
                $('#delete_modal .close').click();
                showAlert("Page "+$(obj).data('label')+" deleted.", "success", 3000);
                table.draw(false);
            });
            return false;
        });
    }
</script>
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">
<style>
.note-toolbar{background-color:#ccc;}
.note-editable{background-color:#fff;}
.note-icon-caret{display:none;}
.note-editor .note-toolbar .note-dropdown-menu{min-width:200px;}
</style>
@endsection
