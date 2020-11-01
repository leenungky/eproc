@extends('tender.show')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{__('tender.'.$type)}}</span>
</div>
<div class="card-header-right">
    @if($editable && $canCreate)
    <button id="btn_create_document" class="btn btn-sm btn-success ml-2" data-toggle="modal"
        data-target="#frmaanwijzings_modal" data-backdrop="static"
        data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.new_entry')}}</button>
    @endif
</div>
@endsection

@section('contentbody')
<div class="has-footer" style="padding: 0">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif
    <div class="col-12">
        <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    @if($editable)
                    <th>{{__('purchaserequisition.action')}}</th>
                    @endif
                    @foreach ($tenderData['tender_'.$type]['fields'] as $field)
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

@section('footer')
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __('tender.'.$type),
        'contents'=>'',
        'form_layout'=>'tender.form.form_aanwijzings',
        'form_name'=>'frmaanwijzings',
    ];
    $modal2 = [
        'title'=> __('tender.publish'),
        'contents'=>'',
        'form_layout'=>'tender.form.form_aanwijzings_publish',
        'form_name'=>'frmpublish',
    ];
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_common',$modal2)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
@include('layouts.datatableoption')

<script type="text/javascript">
require(["datatablesb4","dt.plugin.select",'datetimepicker',"bootstrap-fileinput-fas"], function () {
    var table;
    var isEdit=false;
    // var uiDatetimeFormat = 'DD.MM.YYYY HH:mm';
    // var dbDatetimeFormat = 'YYYY-MM-DD HH:mm:ss';
    var frmId = '#frmaanwijzings';
    var jsonStatus = JSON.parse('{!! json_encode(\App\Models\TenderAanwijzings::STATUS) !!}');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
    var fileinputOptions = {'theme': 'fas', 'showUpload':false,showRemove:false, 'previewFileType':'any', initialPreview : [],initialPreviewConfig: [], maxFileSize: maxUploadSize,
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
            language: dtOptions.language,
            'ajax' : "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}",
            columns: [
                @if($editable)
                {
                    data: 'id', name: 'id',"width": 100,
                    "render": function ( data, type, row ) {
                        let tmp = '';
                        @if($canUpdate)
                        tmp += '<a onClick="editRow(this)" class="editRow ml-2" style="cursor:pointer" data-id="'+data+'" data-pr="'+row.tender_number+'"><i class="fa fa-edit"></i></a>';
                        @endif
                        @if($canDelete)
                        tmp += '<a onClick="deleteRow(this)" class="deleteRow ml-2" style="cursor:pointer" data-id="'+data+'" data-pr="'+row.tender_number+'"><i class="fa fa-trash"></i></a>';
                        @endif

                        @can('tender_'.$type.'_update')
                        if(row.public_status.toLowerCase()==jsonStatus[1]){
                            tmp += '<a class="publisRow btn btn-sm btn-warning ml-2" onClick="publishRow(this)" data-id="'+row['id']+'" title="{{__("tender.publish")}}"><i class="fas fa-bullhorn"></i></a>';
                        }else if(row.public_status.toLowerCase()==jsonStatus[2]) {
                            tmp += '<a class="btn btn-sm btn-danger ml-2" onClick="cancelRow(this)" data-id="'+row['id']+'" title="{{__("tender.btn_cancel")}}"><i class="fas fa-times"></i></a>';
                            tmp += '<a class="btn btn-sm btn-success ml-2" onClick="finishRow(this)" data-id="'+row['id']+'" title="{{__("tender.btn_finish")}}""><i class="fas fa-check"></i></button>';
                        }
                        @endcan
                        return tmp;
                    },
                    "className": 'text-left',
                },
                @endif
                {data: 'event_name', name: 'event_name'},
                {data: 'venue', name: 'venue'},
                {data: 'event_start', name: 'event_start'},
                {data: 'event_end', name: 'event_end'},
                {data: 'public_status', name: 'public_status',"width": 100,},
                {data: 'result_attachment', name: 'result_attachment',"width": 200,},
            ],
            columnDefs:[
                {
                    "render": function ( data, type, row, dt ) {
                        var column = dt.settings.aoColumns[dt.col].data;
                        let tmp = data;
                        switch(column){
                            case 'event_start':
                            case 'event_end':
                                return moment(data).format(uiDatetimeFormat);
                            break;
                            case 'result_attachment':
                                if(data && data != ''){
                                    return '<a target="_blank" href="{{$storage}}/'+data+'">'+data+'</a>';
                                }
                                return '';
                            break;
                            case 'public_status':
                                tmp = row.public_status_text;
                                return tmp;
                            default:
                                return data;
                            break;
                        }
                    },
                    "targets": "_all"
                },
            ],
        };

        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);


        $("#result_attachment").fileinput(fileinputOptions);
        $('#btn_next_flow').click(function(){
            onClickNext();
        });

        $(frmId+'_modal').on("hidden.bs.modal", function () {
            try{
                resetForm();
            }catch{}
        });
        @if($editable)
        $('#event_start').datetimepicker({
            sideBySide: true,
            format: uiDatetimeFormat,
        });
        $('#event_end').datetimepicker({
            useCurrent: false,
            sideBySide: true,
            format: uiDatetimeFormat,
        });

        $(frmId+" #event_start").on("change.datetimepicker", function (e) {
            $(frmId+' #event_end').datetimepicker('minDate', e.date);
        });

        $(frmId+" #event_end").on("change.datetimepicker", function (e) {
            $(frmId+' #event_start').datetimepicker('maxDate', e.date);
        });

        $("#btn_create_document").click(function(){
            isEdit = false;
            $('.edit-publish').hide();
            $('#result_attachment').attr('disabled', true);
            $('#result_description').attr('disabled', true);
            $(frmId+' #public_status').val(jsonStatus[1]);
            $(frmId+' #event_start').val(moment().format(uiDatetimeFormat));
            $(frmId+' #event_end').val(moment().format(uiDatetimeFormat));
        });

        $(frmId+'-save').click(function(){
            if ($(frmId+'')[0].checkValidity()) {
                submit(function(){
                    $(frmId+'_modal .close').click();
                    table.ajax.reload();
                    resetForm();
                    $(frmId+'_fieldset').attr("disabled",false);
                });
            }else{
                showAlert("Please complete the form", "danger");
            }
        });
        resetForm = function(){
            $(frmId+'')[0].reset();
            $(frmId + ' #id').val('');
            $(frmId+' .custom-file label').text('{{__('tender.attachment')}}')
            $(frmId+' #attachment').attr('disabled',false);
            $(frmId+' #public_status').val('draft');
            $(frmId+'-save').show();
            $(frmId+'-cancel').show();
            $(frmId+'_fieldset').attr("disabled",false);
            $(frmId+' #event_start').attr("disabled",false);
            $(frmId+' #event_end').attr("disabled",false);
        };
        setForm = function(obj){
            let dtrow = table.row('#'+$(obj).data('id')).data();
            $.each(dtrow,function(key,val){
                let input = $(frmId+' #'+key);
                if(key=='event_start'||key=='event_end'){
                    val = moment(val).format(uiDatetimeFormat);
                }
                if(input.is('input') && input.attr('type')=='file'){
                    if(val && val != ''){
                        let ext = val.split('.').pop();
                        $("#result_attachment").fileinput('destroy');
                        fileinputOptions.initialPreview = [
                            "{{$storage}}/" + val
                        ];
                        fileinputOptions.initialPreviewConfig = [
                            {caption: val,key: 1, type: 'other',filetype: ext, previewAsData: true},
                        ];
                        $("#result_attachment").fileinput(fileinputOptions);
                    }
                }else{
                    $(frmId+' #'+key).val(val);
                }
            });
        };
        editRow = function(obj){
            isEdit = true;
            let dtrow = table.row('#'+$(obj).data('id')).data();
            setForm(obj);

            if( [jsonStatus[2],jsonStatus[3]].includes(dtrow.public_status.toLowerCase())){
                $('.edit-publish').show();
                $('#result_attachment').attr('disabled', false);
                $('#result_description').attr('disabled', false);
            }else{
                $('.edit-publish').hide();
                $('#result_attachment').attr('disabled', true);
                $('#result_description').attr('disabled', true);
            }
            if(dtrow.public_status.toLowerCase() == jsonStatus[4]){
                $(frmId+'-save').hide();
                $(frmId+'-cancel').hide();
                $(frmId+'_fieldset').attr("disabled",true);
                $(frmId+' #event_start').attr("disabled",true);
                $(frmId+' #event_end').attr("disabled",true);
            }else{
                $(frmId+'-save').show();
                $(frmId+'-cancel').show();
                $(frmId+'_fieldset').attr("disabled",false);
                $(frmId+' #event_start').attr("disabled",false);
                $(frmId+' #event_end').attr("disabled",false);
            }
            $(frmId+'_modal').modal();
        };
        deleteRow = function(obj){
            let dtrow = table.row('#'+$(obj).data('id')).data();
            let rowinfo = dtrow['event_name'] + ' @'+dtrow['venue']+' ['+moment(dtrow['event_start']).format(uiDatetimeFormat)+']';
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
                        $('#delete_modal .close').click();
                        showAlert(rowinfo+" deleted", "success", 3000);
                        table.ajax.reload();
                        resetForm();
                    }else{
                        showAlert(rowinfo+" not deleted", "danger", 3000);
                    }
                }).always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });
                return false;
            });
        };
        @can('tender_'.$type.'_update')
            cancelRow = function(obj){
                let dtrow = table.row('#'+$(obj).data('id')).data();
                let rowinfo = dtrow['event_name'] + ' @'+dtrow['venue']+' ['+moment(dtrow['event_start']).format(uiDatetimeFormat)+'] ';
                $('#delete_modal .modal-title').text("{{__('common.confirmation')}}");
                $('#delete_modal .modal-body').text("{{__('common.confirmation_label',['action' => __('tender.btn_cancel')])}} "+rowinfo+"?");
                $('#btn_delete_modal').click();
                $('#delete_modal #btn_confirm').off('click').on('click', function () {
                    setForm(obj);
                    $(frmId+' #public_status').val(jsonStatus[4]);
                    submit(function(){
                        table.ajax.reload();
                        resetForm();
                        $('#delete_modal .close').click();
                    });
                    return false;
                });
            };
            finishRow = function(obj){
                let dtrow = table.row('#'+$(obj).data('id')).data();
                let rowinfo = dtrow['event_name'] + ' @'+dtrow['venue']+' ['+moment(dtrow['event_start']).format(uiDatetimeFormat)+'] ';
                $('#delete_modal .modal-title').text("{{__('common.confirmation')}}");
                $('#delete_modal .modal-body').text("{{__('common.confirmation_label',['action' => __('tender.btn_finish')])}} "+rowinfo+"?");
                $('#btn_delete_modal').click();
                $('#delete_modal #btn_confirm').off('click').on('click', function () {
                    setForm(obj);
                    $(frmId+' #public_status').val(jsonStatus[3]);
                    submit(function(){
                        table.ajax.reload();
                        resetForm();
                        $('#delete_modal .close').click();
                    });
                    return false;
                });
            };
            publishRow = function(obj){
                let dtrow = table.row('#'+$(obj).data('id')).data();
                $('#frmpublish #id').val(dtrow['id']);
                $('#frmpublish #event_name').text(dtrow['event_name']);
                $('#frmpublish #venue').text(dtrow['venue']);
                let eventStart = (dtrow['event_start'] && dtrow['event_start']) != ''
                    ? moment(dtrow['event_start']).format(uiDatetimeFormat) : '';
                let eventEnd = (dtrow['event_end'] && dtrow['event_end']) != ''
                    ? moment(dtrow['event_end']).format(uiDatetimeFormat) : '';
                $('#frmpublish #event_start').text(eventStart);
                $('#frmpublish #event_end').text(eventEnd);

                // set form value
                setForm(obj);
                $('#frmpublish_modal').modal();
            };

            $('#frmpublish_modal .modal-dialog').removeClass('modal-lg');
            $('#frmpublish-save').click(function(){
                $(frmId+' #public_status').val(jsonStatus[2]);
                submit(function(){
                    table.ajax.reload();
                    resetForm();
                    $('#frmpublish_modal .close').click();
                });
            });
            @endcan
        @endif

        submit = function(callback)
        {
            //SUBMIT
            let frmData = new FormData($(frmId+'')[0]);
            $.ajax({
                url : "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}",
                type : 'POST',
                data : frmData,
                cache : false,
                processData: false,
                contentType: false,
                beforeSend: function( xhr ) {
                    $(frmId+'_fieldset').attr("disabled",true);
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    if(typeof callback == 'function'){
                        callback();
                    }
                    showAlert("Document "+response.data.event_name+" saved.", "success", 3000);
                }else{
                    showAlert("Document not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                $(frmId+'_fieldset').attr("disabled",false);
                Loading.Hide();
            });
        }
    });
});
</script>
@endsection
