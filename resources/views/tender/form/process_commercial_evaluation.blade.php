@extends('tender.show')

@php
$statusProcess = '';
$docTabEnable = true;
$evalTabEnable = true;
$statusEnums = \App\Enums\TenderSubmissionEnum::FLOW_STATUS;
$WORKFLOW_MAPPING_TYPE = \App\Enums\TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE;
$workflowValues = explode('-', $tender->workflow_values);
$actIndex = 2;
if(!isset($workflowValues[1])){
    $workflowValues[1] = $workflowValues[0] == 'process_technical_evaluation' ? 3 : 4;
}
if(!isset($workflowValues[$actIndex])){
    $workflowValues[$actIndex] = '';
}

$inWorkflowValues = ['process_technical_evaluation', $type];
if($tender->prequalification != 1){
    $inWorkflowValues[] = 'process_registration';
    $workflowValues[1] = ($workflowValues[0] == 'process_registration') ? 3 : $workflowValues[1];
}
$pageType = (count($workflowValues) == 3) ? $workflowValues[1] : '';

if(in_array($workflowValues[0], $inWorkflowValues)){
    // kondisi jika tender submission =='2E'
    if($tender->submission_method == '2E' && in_array($pageType, [$WORKFLOW_MAPPING_TYPE['process_technical_evaluation'], $WORKFLOW_MAPPING_TYPE[$type]]) ){
        if((isset($commercial['isStarted']) && $commercial['isStarted'] === true)){
            $docTabEnable = true;
            $evalTabEnable = true;
            $statusProcess = $editable ? ('started-'.$pageType) : 'started-'.$pageType.'-view';
            if($workflowValues[$actIndex] == $statusEnums[2] || $workflowValues[$actIndex]==$statusEnums[4]){
                $statusProcess = $editable ? ('opened-'.$pageType) : 'opened-'.$pageType.'-view';
            }
        }else{
            // kondisi halaman technical telah selesai dan halaman commercial baru dimulai (submission belum di start)
            $statusProcess = ($editable) ? ('registration'.'-') : 'registration-view';
            $docTabEnable = false;
            $evalTabEnable = false;
        }
    }else
    // kondisi halaman technical telah selesai dan halaman commercial baru dimulai (submission belum di start)
    if($tender->submission_method != '2E' && $workflowValues[0] == 'process_technical_evaluation'){
        // $statusProcess = $editable ? ('registration'.'-'.$pageType) : 'registration-view';
        $statusProcess = $editable ? ('registration'.'-') : 'registration-view';
        $docTabEnable = false;
        $evalTabEnable = false;
    } else
    if ($tender->submission_method != '2E' && (in_array($workflowValues[0], $inWorkflowValues) && empty($workflowValues[$actIndex]))){
        // $statusProcess = $editable ? ('registration'.'-'.$pageType) : 'registration-view';
        $statusProcess = $editable ? ('registration'.'-') : 'registration-view';
        $docTabEnable = false;
        $evalTabEnable = false;
    } else
    // kondisi ketika submission sudah di start atau resubmission
    if(($workflowValues[$actIndex] == $statusEnums[1] || $workflowValues[$actIndex]==$statusEnums[3])){
        $docTabEnable = true;
        $evalTabEnable = true;
        $statusProcess = $editable ? ('started-'.$pageType) : 'started-'.$pageType.'-view';
    } else
    // kondisi ketika submission sudah di open atau reopen
    if(($workflowValues[$actIndex] == $statusEnums[2] || $workflowValues[$actIndex]==$statusEnums[4])){
        $statusProcess = $editable ? ('opened-'.$pageType) : 'opened-'.$pageType.'-view';
        $docTabEnable = true;
        $evalTabEnable = true;
    } else
    // kondisi ketika submission sudah finish
    if($workflowValues[$actIndex]==$statusEnums[5]){
        $statusProcess = $editable ? ('finish-'.$pageType) : 'finish-'.$pageType.'-view';
        $docTabEnable = true;
        $evalTabEnable = true;
    }
}

$enableBtnFinish = $statusProcess == 'opened-4';
// dd($statusProcess);
@endphp

@if($isVendor)
    @include('tender.form.commercial_evaluation.vendor')
@else
    @include('tender.form.commercial_evaluation.admin')
@endif

@section('modals')
@if($isVendor == false)
    @include('tender.form.commercial_evaluation.modal_evaluation_note')
    @include('tender.form.commercial_evaluation.modal_scoring')
    @include('tender.form.commercial_evaluation.modal_approval')
    @include('tender.form.commercial_evaluation.modal_addinfo')
@endif
@include('tender.form.commercial_evaluation.modal_comments')
@include('tender.form.commercial_evaluation.modal_history')
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
                processing: true,
                "paging":   false,
                "ordering": false,
                "info":     false,
                language: dtOptions.language,
                ajax : _url + '&vendor_id='+vendorId,
                responsive: true,
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
