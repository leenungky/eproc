@extends('tender.show')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{__('tender.'.$type)}}</span>
</div>
@if($editable && $canCreate)
<div class="card-header-right">
    <button id="btn_create_document" class="btn btn-sm btn-success ml-2" data-toggle="modal" data-target="#frmdocument_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.new_entry')}}</button>
</div>
@endif
@endsection

@section('contentbody')
<div class="has-footer" style="padding: 0">
        @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
        @endif

        <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th>{{__('purchaserequisition.action')}}</th>
                    @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                        <th>{{__('tender.'.$field)}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            </tbody>
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
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('footer')
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __('tender.'.$type),
        'contents'=>'',
        'form_layout'=>'tender.form.form_documents',
        'form_name'=>'frmdocument',
    ]
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
@include('layouts.datatableoption')
<script type="text/javascript">
var table;
require(["datatablesb4","dt.plugin.select","bootstrap-fileinput-fas"], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
    var fileinputOptions = {'theme': 'fas', 'showUpload':false, 'previewFileType':'any', initialPreview : [],initialPreviewConfig: [], maxFileSize : maxUploadSize,
        layoutTemplates : {
            'actionDelete' : '',
        },
    };
    $(function(){
        $("#page_numbers").ready(function () {
            $("#datatable_serverside_paginate").appendTo($("#page_numbers"));
            $("#datatable_serverside_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
        });
        let dtOptions = getDTOptions();
        let options = {
            deferRender: dtOptions.deferRender,
            rowId: dtOptions.rowId,
            lengthChange: false,
            searching: false,
            processing: true,
            // serverSide: true,
            language: dtOptions.language,
            'ajax' : "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}",
            columns: [
                {
                    data: 'id', name: 'id',"width": 50,
                    'visible' : @if($editable) true @else false @endif,
                    @if($editable)
                    "render": function ( data, type, row ) {
                        let _tpl = '';
                        @if($canUpdate)
                        _tpl += '<a onClick="editRow(this)" class="editRow mr-2" style="cursor:pointer" data-id="'+data+'" data-pr="'+row.tender_number+'"><i class="fa fa-edit"></i></a>';
                        @endif
                        @if($canDelete)
                        _tpl += '<a onClick="deleteRow(this)" class="deleteRow" style="cursor:pointer" data-id="'+data+'" data-pr="'+row.tender_number+'"><i class="fa fa-trash"></i></a>';
                        @endif
                        return _tpl;
                    },
                    @endif
                    "className": 'text-center',
                },
                @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                {data: '{{$field}}', name: '{{$field}}'},
                @endforeach
            ],
            columnDefs:[
                {
                    "render": function ( data, type, row, dt ) {
                        var column = dt.settings.aoColumns[dt.col].data;
                        switch(column){
                            case 'attachment':
                                if(data && data != ''){
                                    return '<a target="_blank" href="{{$storage}}/'+data+'">'+data+'</a>';
                                }
                                return '';
                            break;
                            default:
                                return data;
                            break;
                        }
                    },
                    "targets": "_all"
                },
                {
                    "visible": false,
                    "targets": ['tender_number'],
                }
            ],
        };

        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);

        $("#attachment").fileinput(fileinputOptions);
        $('#btn_next_flow').click(function(){
            onClickNext();
        });

        $("#frmdocument_modal").on("hidden.bs.modal", function () {
            resetForm();
        });

        $('#frmdocument-save').click(function(){
            if ($('#frmdocument')[0].checkValidity()) {
                //SUBMIT
                let file = $('#attachment').val()
                if(!file || file==''){
                    $('#attachment').prop('disabled',true);
                }
                let frmData = new FormData($('#frmdocument')[0]);
                $('#frmdocument_fieldset').attr("disabled",true);
                $.ajax({
                    url : "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Document "+response.data.document_name+' ('+response.data.tender_number+") saved.", "success", 3000);
                        $('#frmdocument_modal .close').click();
                        table.ajax.reload();
                        resetForm();
                        $('#frmdocument_fieldset').attr("disabled",false);

                    }else{
                        showAlert("Document not saved.", "danger", 3000);
                        $('#frmdocument_fieldset').attr("disabled",false);
                    }

                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                    $('#frmdocument_fieldset').attr("disabled",false);
                    $('#frmdocument #attachment').attr('disabled',false);
                });

            }else{
                showAlert("Please complete the form", "danger");
            }
        });
        resetForm = function(){
            $('#id').val('');
            $('#frmdocument')[0].reset();
            $('#frmdocument .custom-file label').text('{{__('tender.attachment')}}')
            $('#frmdocument #attachment').attr('disabled',false);
        };
        editRow = function(obj){
            let dtrow = table.row('#'+$(obj).data('id')).data();
            $.each(dtrow,function(key,val){
                let input = $('#frmdocument #'+key);
                if(input.is('input') && input.attr('type')=='file'){
                    if(val && val != ''){
                        let ext = val.split('.').pop();
                        $("#attachment").fileinput('destroy');
                        fileinputOptions.initialPreview = [
                            "{{$storage}}/" + val
                        ];
                        fileinputOptions.initialPreviewConfig = [
                            {caption: val,key: 1, type: 'other',filetype: ext, previewAsData: true},
                        ];
                        $("#attachment").fileinput(fileinputOptions);
                    }
                }else{
                    $('#frmdocument #'+key).val(val);
                }
            });
            $('#frmdocument_modal').modal();
        };
        deleteRow = function(obj){
            let dtrow = table.row('#'+$(obj).data('id')).data();
            let rowinfo = dtrow.document_name + ' ['+ (dtrow.attachment || '')+']';
            $('#delete_modal .modal-title').text("Delete "+rowinfo);
            $('#delete_modal .modal-body').text("Are you sure to delete "+rowinfo+"?");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                $.ajax({
                    type: 'DELETE',
                    url: "{{ route('tender.show',['id'=>$id,'type'=>$type]) }}/"+dtrow['id'],
                    cache : false,
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response) {
                    if(response.success){
                        showAlert(rowinfo+" deleted", "success", 3000);
                        $('#delete_modal .close').click();
                        table.row('#'+dtrow['id']).remove().draw();
                    }else{
                        showAlert(rowinfo+" not deleted", "danger", 3000);
                    }
                }).always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });
                return false;
            });
        };
    });
});
</script>
@endsection
