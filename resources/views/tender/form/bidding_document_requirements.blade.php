@extends('tender.show')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{__('tender.'.$type)}}</span>
</div>

<div class="card-header-right">
    @if($editable && $canCreate)
    <button id="btn_create_document" class="btn btn-sm btn-success ml-2" data-toggle="modal"
        data-target="#frmbiddocument_modal" data-backdrop="static" data-keyboard="false">
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
        'form_layout'=>'tender.form.form_bid_document',
        'form_name'=>'frmbiddocument',
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
var selectedTabId='';
var frmId = '#frmbiddocument';

require(["datatablesb4","dt.plugin.select",'datetimepicker'], function () {
    var rowEdited=null;
    var selectedTable;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
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
                "order": [[ 1, "asc" ]],
                columnDefs:[

                    {
                        "render": function ( data, type, row, dt ) {
                            var column = dt.settings.aoColumns[dt.col].data;
                            switch(column){
                                case 'submission_method':
                                    return row.submission_method_text;
                                case 'stage_type':
                                    return row.stage_type_text;
                                case 'is_required':
                                    return row.is_required_text;
                                default:
                                    return data;
                            }
                        },
                        "targets": "_all"
                    },
                    {
                        "visible": false,
                        "targets": ['tender_number','order'],
                    }
                ],
            };
            //## Initilalize Datatables
            SELF.table = $('#' + elmId).DataTable(options);

            let tabId = elmId.replace('dt-','');
            $('#page_numbers').ready(function () {
                $('#' + elmId +'_paginate').appendTo($('#page_numbers'));
                $('#' + elmId +'_info').css("padding", ".375rem .75rem").appendTo($('#page_numbers'));
            });
        },
        this.sum= function(col, callback){
            if(typeof callback == 'function'){
                $('#' + elmId).on( 'draw.dt', function () {
                    callback(SELF.table.column(col).data().sum());
                });
            }else{
                return SELF.table.column(col).data().sum();
            }
        }
    };
    $(document).ready(function(){

        selectedTable = new DTTable('dt-evaluators');

        $('#btn_next_flow').click(function(){
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
            $(frmId+' select[name="submission_method"]').val(val);
            $(frmId+' #submission_method_text').val(val);
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
            let sum = 0;
            selectedTable.init();
        };
        reloadTable = function(){
            selectedTable.table.ajax.reload();
        };
        resetForm = function(){
            $(frmId+'')[0].reset();
            $(frmId + ' #id').val('');
        };
        setForm = function(dtrow){
            $("#id").val(dtrow.id);
            $("#sequence_done").val(dtrow.sequence_done);
            $("#description").val(dtrow.description);
            $("#stage_type").val(dtrow.stage_type);
            $("#submission_method").val(dtrow.submission_method);
            $("#submission_method_text").val(dtrow.submission_method);
            $("#is_required").val(dtrow.is_required);
            if(dtrow.is_required){
                $("#is_required_yes").prop("checked", true);
            }else{
                $("#is_required_no").prop("checked", true);
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
            let rowinfo = dtrow['description'];
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
                    showAlert("Data "+response.data.description+" saved.", "success", 3000);
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
@endsection
