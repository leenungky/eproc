@extends('tender.show')

<?php
$statusProcess = ''; //start on table negotiation
$negotiationTabEnable = true;
$statusEnums = \App\Enums\TenderSubmissionEnum::FLOW_STATUS;
$workflowValues = explode('-',$tender->workflow_values);
$pageType = (count($workflowValues) == 3) ? $workflowValues[1] : '';
$actIndex = count($workflowValues) > 0 ? count($workflowValues) - 1 : 0;
$enableBtnFinish = false;

$statusProcess = $editable ? ('registration'.'-'.$pageType) : 'registration-view';

// $evalTabEnable = $evalTabEnable ?? true;

?>

@if($isVendor)
@include('tender.form.negotiation.vendor')
@else
@include('tender.form.negotiation.admin')
@endif

@section('modals')
@include('tender.form.negotiation.modal_comments')
@include('tender.form.negotiation.modal_history')
@include('layouts.modal_delete')

@include('tender.form.tender_process.modal_common', [
'title'=> '',
'contents'=>'',
'form_layout'=>'tender.form.tender_process.form_item_detail',
'form_name'=>'formItemDetail',
'modal_class' => 'modal-xl'
])
@include('tender.form.tender_process.modal_common', [
'title'=> __('tender.item_cost.title'),
'contents'=>'',
'form_layout'=>'tender.form.tender_process.form_item_add_cost',
'form_name'=>'formAddcost',
'modal_class' => 'modal-lg'
])

@endsection

@section('modules-scripts')
@parent
<script type="text/javascript">
    var TableHistory = {
        table : null,
        initTable : function(vendorId, hUrl){
            let SELF = this;
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-history"
            if(hUrl){
                _url = hUrl;
            }
            let options = {
                deferRender: dtOptions.deferRender,
                rowId: dtOptions.rowId,
                lengthChange: false,
                searching: false,
                "paging":   false,
                "ordering": false,
                "info":     false,
                language: dtOptions.language,
                ajax : _url + '&vendor_id='+vendorId,
                responsive: true,
                initComplete: function(settings, json){
                    $.each(json.data, function(i, item) {
                        var properties = JSON.parse(item.properties.replace(/&quot;/g,'"'));
                        //handle duplicat history negotiation
                        // if(["start", "draft", "open", "open_resubmission"].includes(item.activity)){
                        //     if(properties.ref_vendor_id && properties.ref_vendor_id !== vendorId){
                        //         SELF.table.rows(i).remove().draw();
                        //     }
                        // }
                    });
                },
                columns: [
                    {
                        data: 'id', name: 'id',"width": 50,"className": 'text-center',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {data: 'user_id', name: 'user_id',"width": 100},
                    {data: 'role', name: 'role',"width": 100},
                    {
                        data: 'activity', name: 'activity',
                        render: function (data, type, row, meta) {
                            return row.activity_text;
                        }
                    },
                    {
                        data: 'activity_date', name: 'activity_date',"width": 200,
                        render: function (data, type, row, meta) {
                            return moment(data).format(uiDatetimeFormat);
                        }
                    },
                ],
            };
            if(SELF.table != null){
                SELF.table.destroy();
            }
            SELF.table = $('#dt-submission-history').DataTable(options);
        },
    };
    var TenderComments = {
        data : {},
        selector: '#btn_comment',
        loadData : function(vendorCode, stageType, forceReload){
            let SELF = this;
            if(forceReload || (SELF.data[vendorCode] == null || SELF.data[vendorCode].length == 0)){
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=comments"
                if(!stageType){
                    stageType = 1;
                }
                $.ajax({
                    url : _url + '&vendor_code='+vendorCode+'&stage_type='+stageType,
                    type : 'GET',
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        Loading.Show(SELF.selector);
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        SELF.data[vendorCode] = response.data;
                        SELF.renderData(response.data, vendorCode);
                        // console.log(SELF.data);
                    }
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide(SELF.selector);
                });
            }else{
                SELF.renderData(SELF.data[vendorCode], vendorCode);
            }
        },
        renderData : function(data, vendorCode){
            let SELF = this;
            $(SELF.selector + ' span').text('(0)');
            $('#popup-comments div.message-list').html('');
            if(data && data.length > 0){
                let tpl = '';
                $(SELF.selector + ' span').text('('+data.length+')');
                for(let ix in data){
                    $('#popup-comments div.message-list').append(SELF.template(data[ix], vendorCode));
                }
            }
        },
        template : function(data, vendorCode){
            if(data.user_id_from != vendorCode){
                return '<div class="alert alert-info col-10" role="alert">'+
                    '<dt>'+data.from_name+' <small>'+data.updated_at+'</small></dt>'+
                    '<dd>'+data.comments+'</dd>'+
                '</div><div class="col-2">&nbsp;</div>';
            }else{
                return '<div class="col-2">&nbsp;</div><div class="alert alert-success col-10" role="alert">'+
                    '<dt>'+data.from_name+' <small>'+data.updated_at+'</small></dt>'+
                    '<dd>'+data.comments+'</dd>'+
                '</div>';
            }
        }
    };
</script>
@endsection
