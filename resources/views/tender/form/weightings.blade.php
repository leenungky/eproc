@extends('tender.show')

@section('contentheader')
<ul class="nav nav-tabs" id="weight-tab" role="tablist">
    @if($tender->prequalification == 1)
    <li class="nav-item" id="pre_qualification_weight-li">
        <a class="nav-link" id="pre_qualification_weight-tab" data-toggle="tab" href="#pre_qualification_weight" role="tab"
            aria-controls="pre_qualification_weight" aria-selected="true">{{__('tender.status_stage.pre_qualification')}}</a>
    </li>
    @endif
    @if(in_array($tender->submission_method, ['']))
    <li class="nav-item"  id="offer_weight-li">
        <a class="nav-link" id="offer_weight-tab" data-toggle="tab" href="#offer_weight" role="tab"
            aria-controls="offer_weight" aria-selected="false">{{__('tender.status_stage.offer')}}</a>
    </li>
    @endif
    @if(in_array($tender->submission_method, ['1E','2E','2S','2SSPLIT']))
    <li class="nav-item"  id="technical_offer_weight-li">
        <a class="nav-link" id="technical_offer_weight-tab" data-toggle="tab" href="#technical_offer_weight" role="tab"
            aria-controls="technical_offer_weight" aria-selected="false">{{__('tender.status_stage.technical_offer')}}</a>
    </li>
    <li class="nav-item"  id="commercial_offer_weight-li">
        <a class="nav-link" id="commercial_offer_weight-tab" data-toggle="tab" href="#commercial_offer_weight" role="tab"
            aria-controls="commercial_offer_weight" aria-selected="false">{{__('tender.status_stage.commercial_offer')}}</a>
    </li>
    @endif
</ul>
@endsection
@section('contentbody')
<div class="">
    <div class="row" style="padding: 4px 20px 4px;">
        <div class="col-sm-12">
            <div class="float-left">
                <span class="heading-title" style="text-transform: uppercase;color: rgba(13,27,62,0.7);font-weight: bold;font-size: .88rem;">{{__('tender.'.$type)}}</span>
            </div>
            <div class="float-right">
                <span id="table_sum" class="badge badge-secondary" style="">Total : <b id="col_sum"></b></span>
                @if($editable && $canCreate)
                <button id="btn_create_document" class="btn btn-sm btn-success ml-2"
                    data-toggle="modal" data-target="#frmweightings_modal"
                    data-backdrop="static" data-keyboard="false">
                    <i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('tender.new_entry')}}</button>
                @endif
            </div>
        </div>
    </div>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade" id="pre_qualification_weight" role="tabpanel" aria-labelledby="pre_qualification_weight-tab">
            <div class="tab-body">
                <div class="has-footer has-tab" style="padding: 0">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="col-12">
                        <table id="dt-pre_qualification_weight" class="table table-sm table-bordered table-striped table-vcenter">
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
                            <div class="page_numbers" style="display:inherit"></div>
                        </div>
                        <div class="app-footer-right">
                            <button class="btn_next_flow btn btn-primary">
                                {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="offer_weight" role="tabpanel" aria-labelledby="offer_weight-tab">
            <div class="tab-body">
                <div class="has-footer has-tab" style="padding: 0">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="col-12">
                        <table id="dt-offer_weight" class="table table-sm table-bordered table-striped table-vcenter">
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
                            <div class="page_numbers" style="display:inherit"></div>
                        </div>
                        <div class="app-footer-right">
                            <button class="btn_next_flow btn btn-primary">
                                {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="commercial_offer_weight" role="tabpanel" aria-labelledby="commercial_offer_weight-tab">
            <div class="tab-body">
                <div class="has-footer has-tab" style="padding: 0">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="col-12">
                        <table id="dt-commercial_offer_weight" class="table table-sm table-bordered table-striped table-vcenter">
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
                            <div class="page_numbers" style="display:inherit"></div>
                        </div>
                        <div class="app-footer-right">
                            <button class="btn_next_flow btn btn-primary">
                                {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="technical_offer_weight" role="tabpanel" aria-labelledby="technical_offer_weight-tab">
            <div class="tab-body">
                <div class="has-footer has-tab" style="padding: 0">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="col-12">
                        <table id="dt-technical_offer_weight" class="table table-sm table-bordered table-striped table-vcenter">
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
                            <div class="page_numbers" style="display:inherit"></div>
                        </div>
                        <div class="app-footer-right">
                            <button class="btn_next_flow btn btn-primary">
                                {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
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
        'form_layout'=>'tender.form.form_weightings',
        'form_name'=>'frmweightings',
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
var frmId = '#frmweightings';


require(["datatablesb4","dt.plugin.select",'datetimepicker'], function () {
    var rowEdited=null;
    var selectedTable;
    var colWeightSum = 3;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function(){
        var Tabs = $('#weight-tab li > a.nav-link');
        const modalTitle = $(this).find('.modal-title').html();
        var DTTable = function(elmId, submissionMethod){
            let SELF = this;
            this.table = null;
            this.init = function(){
                let dtOptions = getDTOptions();
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}";
                _url = _url + '?method=' + submissionMethod;
                let options = {
                    deferRender: dtOptions.deferRender,
                    rowId: dtOptions.rowId,
                    lengthChange: false,
                    searching: false,
                    processing: true,
                    language: dtOptions.language,
                    autoWidth: false,
                    rowCallback: function(row, data){
                        if(Object.keys(data).includes("weight")){
                            $('td:eq(3)', row).html(parseFloat(data.weight).formatMoney(2, ".", ","));
                        }
                    },
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
                    "order": [[ 4, "asc" ]],
                    columnDefs:[
                        {
                            "render": function ( data, type, row, dt ) {
                                var column = dt.settings.aoColumns[dt.col].data;
                                switch(column){
                                    case 'submission_method':
                                        return row.submission_method_text;
                                    default:
                                        return data;
                                }
                            },
                            "targets": "_all"
                        },
                        {
                            "visible": false,
                            "targets": ['order'],
                        },
                    ],
                };
                //## Initilalize Datatables
                SELF.table = $('#' + elmId).DataTable(options);

                let tabId = elmId.replace('dt-','');
                $('#' + tabId + ' .page_numbers').ready(function () {
                    $('#' + elmId +'_paginate').appendTo($('#' + tabId + ' .page_numbers'));
                    $('#' + elmId +'_info').css("padding", ".375rem .75rem").appendTo($('#' + tabId + ' .page_numbers'));
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

        var pqTable = new DTTable('dt-pre_qualification_weight',1);
        var owTable = new DTTable('dt-offer_weight',2);
        var cowTable = new DTTable('dt-commercial_offer_weight',4);
        var towTable = new DTTable('dt-technical_offer_weight',3);

        $(frmId+' #attachment').change(function(e){
            $(frmId+' #attachment_label').text('');
            if(e.target.files.length>0){
                $(frmId+' #attachment_label').text(e.target.files[0].name);
            }
        });
        $('.btn_next_flow').click(function(){
            onClickNext();
        });

        $(frmId+'_modal').on("hidden.bs.modal", function () {
            try{
                resetForm();
            }catch{}
        });
        $(frmId+'_modal').on("show.bs.modal", function () {
            let tabActiveTitle = $('#weight-tab a.nav-link.active').html();
            $(this).find('.modal-title').html(modalTitle +' - '+tabActiveTitle);
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let tabId = $(e.target).prop('id');
            loadTable(tabId);
            $("#frmweightings input[name='weight']").attr('type', "text");
            $("#frmweightings input[name='weight']").autoNumeric('init', { aSep: ".", aDec: ",", mDec: 2, vMax: '100', vMin: '0' });
        });

        $("#btn_create_document").click(function(){
            isEdit = false;
            rowEdited = null;
            resetForm();
            $("#submission_method_text").val(submissionMethod);
            $("#submission_method").val(submissionMethod);
        });

        $(frmId+'-save').click(function(){
            if ($(frmId+'')[0].checkValidity()) {
                if(validateForm(selectedTabId)){
                    submit(function(){
                        $(frmId+'_modal .close').click();
                        reloadTable(selectedTabId);
                        resetForm();
                        $(frmId+'_fieldset').attr("disabled",false);
                    });
                }
            }else{
                showAlert("Please complete the form", "warning");
            }
        });
        loadTable = function(tabId){
            let sum = 0;
            selectedTabId = tabId;
            switch(tabId){
                case 'pre_qualification_weight-tab' :
                    submissionMethod=1;
                    if(pqTable.table == null){
                        pqTable.init();
                    }else{ pqTable.table.ajax.reload() };
                    selectedTable = pqTable;
                    break;
                case 'offer_weight-tab' :
                    submissionMethod=2;
                    if(owTable.table == null){
                        owTable.init();
                    }else{ owTable.table.ajax.reload() };
                    selectedTable = owTable;
                    break;
                case 'commercial_offer_weight-tab' :
                    submissionMethod=4;
                    if(cowTable.table == null){
                        cowTable.init();
                    }else{ cowTable.table.ajax.reload() };
                    selectedTable = cowTable;
                    break;
                case 'technical_offer_weight-tab' :
                    submissionMethod=3;
                    if(towTable.table == null){
                        towTable.init();
                    }else{ towTable.table.ajax.reload() };
                    selectedTable = towTable;
                    break;
            }
            selectedTable.sum(colWeightSum, function(_sum){
                $('#col_sum').html(_sum);
            });
        };
        reloadTable = function(tabId){
            selectedTable.table.ajax.reload();
        };
        resetForm = function(){
            $(frmId+'')[0].reset();
            $(frmId + ' #id').val('');
        };
        setForm = function(dtrow){
            $("#id").val(dtrow.id);
            $("#sequence_done").val(dtrow.sequence_done);

            $("#criteria").val(dtrow.criteria);
            $("#order").val(dtrow.order);
            $("#weight").val(formatPercentage(dtrow.weight));
            $("#submission_method_text").val(dtrow.submission_method);
            $("#submission_method").val(dtrow.submission_method);
            $("#submission_method").val(dtrow.submission_method);
            if(dtrow.is_commercial){
                $("#is_commercial_yes").prop("checked", true);
            }else{
                $("#is_commercial_no").prop("checked", true);
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
            let rowinfo = dtrow['criteria'];
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

        validateForm = function(tabId){
            let valid = true;
            let sum = 0;
            let oldValue = 0;
            if(rowEdited){
                oldValue = selectedTable.table.cell(rowEdited.index(),colWeightSum).data();
                selectedTable.table.cell(rowEdited.index(),colWeightSum).data(getAutonumricValue($("#weight")));
                sum = selectedTable.sum(colWeightSum);
            }else{
                sum = selectedTable.sum(colWeightSum);
                sum = parseFloat(sum) + parseFloat(getAutonumricValue($("#weight")));
            }

            if(sum > 100){
                valid = false;
                if(rowEdited){
                    selectedTable.table.cell(rowEdited.index(),colWeightSum).data(oldValue);
                }
                showAlert("max total is 100", "warning");
            }
            return valid;
        };
        submit = function(callback)
        {
            //SUBMIT
            $("#weight").val(getAutonumricValue($("#weight")));
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
                    showAlert("Document "+response.data.criteria+" saved.", "success", 3000);
                }else{
                    showAlert("Document not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                $(frmId+'_fieldset').attr("disabled",false);
                Loading.Hide();
            });
        };

        loadTable('pre_qualification_weight-tab');
        // select first tab
        $(Tabs[0]).tab('show')


    }); //document.ready
});
</script>
@endsection
