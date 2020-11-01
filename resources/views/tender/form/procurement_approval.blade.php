@extends('tender.show')

@section('contentbody')
@if($approver && $hasPendingApproval)
    @include('tender.form.procurement_approval_edit')
@else
    @include('tender.form.procurement_approval_view')
@endif
@endsection


@section('modals')
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
<script type="text/javascript">
require(['datetimepicker'], function(datetimepicker){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    @if($approver && $approver->status == 'draft')
    $('#frmApproval .btn.btn-rejected').click(function(e){
        if($('#frmApproval')[0].checkValidity()){
            let message = "{{__('tender.schedule.modal_message', ['status' => __('tender.schedule_status.rejected')])}}";
            save('rejected', message);
        }
    });
    $('#frmApproval .btn.btn-approved').click(function(e){
        if($('#frmApproval')[0].checkValidity()){
            let message = "{{__('tender.schedule.modal_message', ['status' => __('tender.schedule_status.approved')])}}";
            save('approved', message);
        }
    });
    function save(_status, message){
        $('#delete_modal .modal-title').text("{{__('tender.schedule.modal_title')}}");
        $('#delete_modal .modal-body').text(message);
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let params = {
                id : $('#frmApproval input[name="id"]').val(),
                notes : $('#frmApproval textarea[name="notes"]').val(),
                status : _status,
            };
            $('#frmApproval .btn.btn-rejected').prop('disabled', true);
            $('#frmApproval .btn.btn-approved').prop('disabled', true);

            $.ajax({
                url : "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}",
                type : 'POST',
                data : JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    $('#frmApproval .btn.btn-rejected').hide();
                    $('#frmApproval .btn.btn-approved').hide();
                    showAlert("Data saved.", "success", 3000);
                    $('#delete_modal .close').click();
                    if(response.next!='' && response.next != null){
                        location.href = response.next;
                    }else{
                        location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
                    }
                }else{
                    showAlert("Data not saved.", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                $('#frmApproval .btn.btn-rejected').prop('disabled', false);
                $('#frmApproval .btn.btn-approved').prop('disabled', false);
                Loading.Hide();
            });
            return false;
        });
    }
    @endif

    $('#btn_next_flow').click(function(){
        onClickNext();
    });
});
</script>
@endsection

