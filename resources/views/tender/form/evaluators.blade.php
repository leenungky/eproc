@extends('tender.show')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{__('tender.'.$type)}}</span>
</div>
<div class="card-header-right">
    @if($editable && $canCreate)
    <button id="btn_create_document" class="btn btn-sm btn-success ml-2" data-toggle="modal"
        data-target="#frmevaluators_modal" data-backdrop="static" data-keyboard="false">
        <i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.new_entry')}}</button>
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
        <table id="dt-evaluators" class="table table-sm table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th>{{__('purchaserequisition.action')}}</th>
                    @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                        <th class="{{$field}}">{{__('tender.bidding.fields.'.$field)}}</th>
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
            <button id="btn_next_flow" class="btn btn-primary">
                {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
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
        'form_layout'=>'tender.form.form_evaluators',
        'form_name'=>'frmevaluators',
    ];
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
@include('layouts.datatableoption')

<script type="text/javascript">
var isEdit=false;
var submissionMethod=1;
var frmId = '#frmevaluators';

require(["datatablesb4",'select2'], function () {
    var rowEdited=null;
    var selectedTable;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function(){
        var DTTable = function(elmId){
            let SELF = this;
            this.table = null;
            this.init = function(){
                let dtOptions = getDTOptions();
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
                let options = {
                    deferRender: dtOptions.deferRender,
                    rowId: dtOptions.rowId,
                    lengthChange: false,
                    searching: false,
                    processing: true,
                    language: dtOptions.language,
                    autoWidth: false,
                    ajax : {
                        url : _url,
                    },
                    columns: [
                        {
                            data: 'id', name: 'id',"width": 50,"className": 'text-center',
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
                        },
                        @foreach ($tenderData['tender_'.$type]['fields'] as $field)
                        {data: '{{$field}}', name: '{{$field}}'},
                        @endforeach
                    ],
                    columnDefs:[
                        {
                            'targets': 0,
                            'orderable':false,
                        },
                        {
                            "render": function ( data, type, row, dt ) {
                                var column = dt.settings.aoColumns[dt.col].data;
                                switch(column){
                                    case 'stage_type':
                                        if(data==2) return "{{__('tender.'.$submissionMethod[$tender->submission_method])}}"; 
                                        return row.submission_method_text;
                                    case 'submission_method':
                                        return row.submission_method_text;
                                    case 'buyer_type_name':
                                        return row.buyer_type_name_text;
                                    default:
                                        return data;
                                }
                            },
                            "targets": "_all"
                        },
                    ],
                };
                //## Initilalize Datatables
                SELF.table = $('#' + elmId).DataTable(options);

                $('#page_numbers').ready(function () {
                    $('#' + elmId +'_paginate').appendTo($('#page_numbers'));
                    $('#' + elmId +'_info').css("padding", ".375rem .75rem").appendTo($('#page_numbers'));
                });
            };
        };
        selectedTable = new DTTable('dt-evaluators');
        $('#btn_next_flow').click(function(e){
            onClickNext();
        });
        $(frmId+'_modal').on("hidden.bs.modal", function () {
            try{
                resetForm();
            }catch{}
        });
        $("#btn_create_document").click(function(){
            isEdit = false;
            rowEdited = null;
            resetForm();
            $("#submission_method_text").val(submissionMethod);
            $("#submission_method").val(submissionMethod);
        });
        $(frmId+' select[name="stage_type"]').on("change", function (e) {
            let val = $(this).val();
            if(val==2) $(frmId+' select[name="submission_method"]').val(3);
            else $(frmId+' select[name="submission_method"]').val(val);
            $('#buyer_type_ids option').attr('disabled','disabled');
            if(val==1){
                $('#buyer_type_ids option[value="1"]').removeAttr('disabled');
                $('#buyer_type_ids option[value="2"]').removeAttr('disabled');
                $('#buyer_type_ids option[value="3"]').removeAttr('disabled');
            }else if(val==2){
                @if($tender->submission_method=='1E')
                $('#buyer_type_ids option[value="4"]').removeAttr('disabled');
                $('#buyer_type_ids option[value="8"]').removeAttr('disabled');
                $('#buyer_type_ids option[value="11"]').removeAttr('disabled');
                @endif
                @if($tender->submission_method=='2E')
                $('#buyer_type_ids option[value="5"]').removeAttr('disabled');
                @endif
            }else if(val==3){
                @if($tender->submission_method=='2S')
                $('#buyer_type_ids option[value="6"]').removeAttr('disabled');
                @endif
                $('#buyer_type_ids option[value="9"]').removeAttr('disabled');
                $('#buyer_type_ids option[value="12"]').removeAttr('disabled');
            }else if(val==4){
                @if($tender->submission_method=='2S')
                $('#buyer_type_ids option[value="7"]').removeAttr('disabled');
                @endif
                $('#buyer_type_ids option[value="10"]').removeAttr('disabled');
                $('#buyer_type_ids option[value="13"]').removeAttr('disabled');
            }else if(val==6){
                $('#buyer_type_ids option[value="14"]').removeAttr('disabled');
                $('#buyer_type_ids option[value="15"]').removeAttr('disabled');
                $('#buyer_type_ids option[value="16"]').removeAttr('disabled');
            }
            $('#buyer_type_ids').val(null).trigger('change');
        });
        $('#buyer_type_ids').select2({
            theme: 'bootstrap4',
            // minimumInputLength: 1
        });

        $(frmId+'-save').click(function(){
            if ($(frmId+'')[0].checkValidity()) {
                if(validateForm()){
                    submit(function(){
                        $(frmId+'_modal .close').click();
                        reloadTable();
                        resetForm();
                        $(frmId+'_fieldset').attr("disabled",false);
                    });
                }
            }else{
                showAlert("Please complete the form", "warning");
            }
        });
        loadTable = function(){
            selectedTable.init();
        };
        reloadTable = function(){
            selectedTable.table.ajax.reload();
        };
        resetForm = function(){
            $(frmId+'')[0].reset();
            $(frmId + ' #id').val('');
            $('#buyer_type_ids').val(null).trigger('change');
        };
        setForm = function(dtrow){
            $("#id").val(dtrow.id);
            $("#sequence_done").val(dtrow.sequence_done);
            $("#buyer_user_id").val(dtrow.buyer_user_id);
            $("#stage_type").val(dtrow.stage_type).trigger('change');
            $("#submission_method").val(dtrow.submission_method);
            if(dtrow.permission_ids && dtrow.permission_ids!= ''){
                $('#buyer_type_ids').val(dtrow.permission_ids.split(',')).trigger('change');
            }
        };
        editRow = function(obj){
            isEdit = true;
            rowEdited = selectedTable.table.row('#'+$(obj).data('id'));
            let dtrow = selectedTable.table.row('#'+$(obj).data('id')).data();
            setForm(dtrow);
            $(frmId+'_modal').modal();
        };
        deleteRow = function(obj){
            rowEdited = selectedTable.table.row('#'+$(obj).data('id'));
            let dtrow = selectedTable.table.row('#'+$(obj).data('id')).data();
            let rowinfo = dtrow['buyer_name'];
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
                        selectedTable.table.ajax.reload();
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
        validateForm = function(){
            let valid = true;
            return valid;
        };
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
                    showAlert("Data saved.", "success", 3000);
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                $(frmId+'_fieldset').attr("disabled",false);
                Loading.Hide();
            });
        };

        loadTable();
    }); //document.ready
});
</script>
<style>
.select2-results__option[aria-selected=true],
.select2-results__option[aria-disabled=true] {
    display: none;
}
</style>
@endsection
