@extends('tender.show')

@php
$statusProcess = '';
$docTabEnable = true;
$evalTabEnable = true;
$statusEnums = \App\Enums\TenderSubmissionEnum::FLOW_STATUS;
$workflowValues = explode('-',$tender->workflow_values);
$actIndex = 2;
$pageType = (count($workflowValues) == 3) ? $workflowValues[1] : '';

if(!isset($workflowValues[$actIndex])){
    $workflowValues[$actIndex] = '';
}

if(in_array($workflowValues[0], ['process_registration',$type])){

    // kondisi halaman prequalification baru dimulai (submission belum di start)
    if((in_array($workflowValues[0], ['process_registration',$type]) && empty($workflowValues[$actIndex]))){
        $docTabEnable = false;
        $evalTabEnable = false;
        $statusProcess = $editable ? 'registration' : 'registration-view';
    // kondisi halaman prequalification submission sudah di start atau resubmission
    } else if(($workflowValues[$actIndex] == $statusEnums[1] || $workflowValues[$actIndex]==$statusEnums[3])){
        $docTabEnable = true;
        $evalTabEnable = true;
        $statusProcess = $editable ? 'started-pq' : 'started-pq-view';
    // kondisi halaman prequalification submission sudah di open atau reopen
    } else if(($workflowValues[$actIndex] == $statusEnums[2] || $workflowValues[$actIndex]==$statusEnums[4])){
        $docTabEnable = true;
        $evalTabEnable = true;
        $statusProcess = $editable ? 'opened-pq' : 'opened-pq-view';
    }
}
// dd($workflowValues);
@endphp

@if($isVendor)
    @include('tender.form.prequalification.vendor')
@else
    @include('tender.form.prequalification.admin')
@endif

@section('modals')
@if($isVendor == false)
    @include('tender.form.prequalification.modal_evaluation_note')
    @include('tender.form.prequalification.modal_scoring')
@endif
@include('tender.form.prequalification.modal_comments')
@include('tender.form.prequalification.modal_history')
@include('layouts.modal_delete')
@endsection


@section('modules-scripts')
@parent
<script type="text/javascript">
    var TableHistory = {
        table : null,
        initTable : function(vendorId){
            let SELF = this;
            let dtOptions = getDTOptions();
            let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=submission-history"
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
        loadData : function(vendorCode){
            let SELF = this;
            if(SELF.data[vendorCode] == null || SELF.data[vendorCode].length == 0){
                let selector = '#btn_comment';
                let _url = "{{ route('tender.dataItem', ['id' => $id, 'type' => $type]) }}?action_type=comments"
                $.ajax({
                    url : _url + '&vendor_code='+vendorCode+'&stage_type=1',
                    type : 'GET',
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        Loading.Show(selector);
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        SELF.data[vendorCode] = response.data;
                        SELF.renderData(response.data, vendorCode);
                        // console.log(SELF.data);
                    }
                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide(selector);
                });
            }else{
                SELF.renderData(SELF.data[vendorCode], vendorCode);
            }
        },
        renderData : function(data, vendorCode){
            let SELF = this;
            $('#btn_comment span').text('(0)');
            $('#popup-comments div.message-list').html('');
            if(data && data.length > 0){
                let tpl = '';
                $('#btn_comment span').text('('+data.length+')');
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
