@extends('vendor.evaluation.show')

@php
$formName = 'frmGeneral';
$historyFields = ['activity_date','username','role','activity','status','comments'];
$commentHistoryFields = ['userid','name','activity','started_at','finished_at','remarks'];
@endphp

@if($general->status!='CONCEPT')
@if(auth()->user()->can('vendor_evaluation_approval') && $isBuyerActive)
@section('modals')
<?php
    $modal1 = [
        'title'=> __("homepage.approval"),
        'contents'=>'',
        'form_layout'=>'vendor.evaluation.form_approval',
        'form_name'=>'frmApproval',
    ]
?>
@include('layouts.modal_common',$modal1)
@endsection
@endif
@endif

@section('contentbody')
<div class="has-footer p-2">
<form id="{{$formName}}" name="{{$formName}}" class="was-validated">
<fieldset id="{{$formName}}_fieldset">
    @csrf
    @include('vendor.evaluation.form_evaluation_general')
</fieldset>
</form>

<div class="history mt-4">
<h4>{{__('homepage.comments_history')}}</h4>

<table class="table table-sm table-striped table-bordered">
    <thead><tr>
        @foreach($commentHistoryFields as $field)
        <th>{{__('homepage.'.$field)}}</th>
        @endforeach
    </tr></thead>
    <tbody>
        @foreach($commentHistories as $history)
        <tr>
            @foreach($commentHistoryFields as $field)
            <td>{{$history->$field}}</td>
            @endforeach
        </tr>
        @endforeach
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
                @if(in_array($general->status,['CONCEPT','REVISE']))
                    @can('vendor_evaluation_modify')
                        @if($general->is_finished==0 && $samePurchOrg && $isBuyerActive)
                        <li id="action_group" class="nav-item">
                            <button id="btn_save_flow" class="btn btn-primary mr-2">{{__('homepage.save')}}</button>
                        </li>
                        @endif
                    @endCan
                @endif
                @if($general->status=='SUBMISSION')
                    @if(auth()->user()->can('vendor_evaluation_approval') && $samePurchOrg && $isBuyerActive)
                    <li id="revise" class="nav-item">
                        <button id="btn_revise_flow" class="btn btn-warning mr-2">{{__('homepage.revise')}}</button>
                    </li>
                    <li id="approve" class="nav-item">
                        <button id="btn_approve_flow" class="btn btn-success mr-2">{{__('homepage.approve')}}</button>
                    </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>
</div>

@endsection

@section('modules-scripts')
@parent
<script>
var categories={!!json_encode($categories)!!};
require(['datetimepicker'],function(){
$(function(){
    $('#status').closest('.form-group').attr('hidden',false);
    $('#start_date').val('');
    $('#end_date').val('');
    $(".date").datetimepicker({
        useCurrent: false,
        format:uiDateFormat,
    })
    $("#start_date").val(moment("{{$general->start_date ?? date('Y-m-d')}}",dbDateFormat).format(uiDateFormat));
    $("#end_date").val(moment("{{$general->end_date ?? date('Y-m-d')}}",dbDateFormat).format(uiDateFormat));

        $("#start_date").off("change.datetimepicker").on("change.datetimepicker", function (e) {
            $('#end_date').datetimepicker('minDate', e.date);
        });
        $('#year').change(function(){
            resetDate();
        })
        $('#frmGeneral_fieldset').attr('disabled',{{$general->is_finished==1?'true':'false'}});
        $('#category_id').change(function(){
            let category_id = $(this).val();
            let category = null;
            for(let i=0;i<categories.length;i++){
                if(categories[i].id==category_id) category = categories[i];
            }
            // $('#project_code').attr('disabled',category==null ? true : category.categories_json!='PROJECT');
            // if($('#project_code').attr('disabled')){
            //     $('#project_code').val('');
            // }
            $('#project').hide();
            $('#yearly').show();
            $('#project_code').attr('required',false);
            $('#start_date').attr('required',false).attr('readonly',true);
            $('#end_date').attr('required',false).attr('readonly',true);

            if(category) if(category.categories_json=='PROJECT') {
                $('#project').show();
                $('#yearly').show();
                $('#project_code').attr('required',true);
                $('#start_date').attr('required',true).attr('readonly',false);
                $('#end_date').attr('required',true).attr('readonly',false);
            }else{
                $('#yearly').show();
                resetDate();
            }

        });
        // resetDate();
        $('#category_id').change();

    @if(in_array($general->status,['CONCEPT','REVISE']))
        @if($samePurchOrg && auth()->user()->can('vendor_evaluation_modify') && $isBuyerActive)
        $('#btn_save_flow').click(function(){
            let frmId = '#{{$formName}}';

            if ($(frmId)[0].checkValidity()) {
                let frmData = parseFormData('{{$formName}}');
                $(frmId+'_fieldset, #btn_save_flow').attr("disabled",true);
                Loading.Show();
                $.ajax({
                    url : "{{ route('vendor.evaluation.evaluation_store') }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Data "+$(frmId+' #name').val()+" saved.", "success", 3000);
                        setTimeout(() => {
                            $(frmId+'_fieldset, #btn_save_flow').attr("disabled",false);
                            location.reload();
                            Loading.Hide();
                        }, 1000);

                    }else{
                        showAlert("Data not saved.", "danger", 3000);
                        $(frmId+'_fieldset, #btn_save_flow').attr("disabled",false);
                        Loading.Hide();
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset, #btn_save_flow').attr("disabled",false);
                    Loading.Hide();
                });
            }else{
                showAlert("Please complete the form", "danger");
            }
            return false;
        });
        @else
        $('#frmGeneral_fieldset').attr('disabled',true);
        @endIf
    @else
        $('#yearly').show();
        $('#frmGeneral_fieldset').attr('disabled',true);
        @if(auth()->user()->can('vendor_evaluation_approval') && $samePurchOrg && $isBuyerActive)
            $('#frmApproval_modal .modal-dialog').removeClass('modal-lg');
            $('#btn_revise_flow').click(function(){
                approval(false);
            });
            $('#btn_approve_flow').click(function(){
                approval(true);
            });
        @endif
    @endif
});
function resetDate(){
    let year = $('#year').val();
    let minDate = '01.01.'+year;
    let maxDate = '31.12.'+year;
    let from = $('#start_date').val();
    let thru = $('#end_date').val();
    let category_id = $('#category_id').val();
    let category = null;

    from = from.substring(0,6)+year;
    thru = thru.substring(0,6)+year;
    for(let i=0;i<categories.length;i++){
        if(categories[i].id==category_id) category = categories[i];
    }
    $('#start_date, #end_date').val('');
    $('#start_date, #end_date').datetimepicker('minDate', false);
    $('#start_date, #end_date').datetimepicker('maxDate', false);
    $('#start_date, #end_date').datetimepicker('minDate', moment(minDate,uiDateFormat).format(uiDateFormat));
    
    if(category!=null && category.categories_json=='YEARLY'){
        let year = $('#year').val();
        $('#start_date, #end_date').datetimepicker('maxDate', moment(maxDate,uiDateFormat).format(uiDateFormat));
        $('#start_date').val(minDate);
        $('#end_date').val(maxDate);
    }else{
        $('#start_date').val(from);
        $('#end_date').val(thru);
    }
}
@if(in_array($general->status,['CONCEPT','REVISE']) && $samePurchOrg && $isBuyerActive)
@else
    @if(auth()->user()->can('vendor_evaluation_approval') && $samePurchOrg && $isBuyerActive)
    function approval(status){
        let approval = status ? 'Approve' : 'Revise';
        $('#comment').val('');
        $('#frmApproval #approved').val(status);
        $('#frmApproval_modal .modal-title').text(approval+' [{{$general->name}}]');
        $('#frmApproval_modal #message').html('Are you sure to '+approval.toLowerCase()+' [{{$general->name}}] ?');
        $('#frmApproval_modal .modal-dialog').removeClass('modal-lg');
        $('#frmApproval-save').text(approval);
        $('#frmApproval_modal').modal();

        $('#frmApproval-save').off('click').on('click',function(){
            let frmId = '#frmApproval';
            if ($(frmId)[0].checkValidity()) {
                let frmData = parseFormData('frmApproval');
                $(frmId+'_fieldset').attr("disabled",true);
                Loading.Show();
                $.ajax({
                    url : "{{ route('vendor.evaluation.evaluation_detail_approval', $general->id) }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {

                    if(response.success){
                        showAlert("Evaluation saved.", "success", 3000);
                        Loading.Hide();
                        setTimeout(() => {
                            // table.draw(false);
                            $(frmId+'_modal .close').click();
                            $(frmId)[0].reset();
                            $(frmId+'_fieldset').attr("disabled",false);
                            location.reload();
                        }, 1000);

                    }else{
                        showAlert("Evaluation not saved.", "danger", 3000);
                        $(frmId+'_fieldset').attr("disabled",false);
                        Loading.Hide();
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                    Loading.Hide();
                });

            }else{
                showAlert("Please complete the form", "danger");
            }
        });
    }
    @endif
@endif
});
</script>
@endsection