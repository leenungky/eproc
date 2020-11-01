@extends('tender.show')

@section('contentbody')
@if($editable)
    @include('tender.form.parameters_edit')
@else
    @include('tender.form.parameters_view')
@endif
@endsection

@section('footer')
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
            <ul class="nav">
                <li id="action_group" class="nav-item">
                    @if($editable)
                    <button id="btn_save_flow" class="btn btn-success mr-2" disabled>
                        <i class="fa fa-save"></i> {{__('tender.save')}}</button>
                    @endif
                    <button id="btn_next_flow" class="btn btn-primary">
                        {{__('tender.next')}} <i class="fa fa-arrow-right"></i></button>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('modules-scripts')
@parent
<script type="text/javascript">
require(['jquery','autonumeric'], function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(function(){
        $('#validity_quotation').attr('type', "text");
        $('#validity_quotation').autoNumeric('init', { aSep: ".", aDec: ",", mDec: 0, vMax: '9999999999999', vMin: '0' });
        @if($editable)
        $("input[name='validity_quotation_radio']").click(function(){
            $('#validity_quotation').attr('readonly',true);
            if($(this).val()==-1){
                $('#validity_quotation').attr('readonly',false);
            }else{
                $('#validity_quotation').val($(this).val());
            }
        })
        $("input[name='tkdn_option']").click(function(){
            $('#tkdn').attr('disabled',true);
            if($(this).val()==1){
                $('#tkdn').attr('disabled',false);
            }else{
                $('#tkdn').val('');
            }
        })
        $("input[name='retention']").click(function(){
            $('#retention_percentage').attr('disabled',true);
            if($(this).val()==1){
                $('#retention_percentage').attr('disabled',false);
            }else{
                $('#retention_percentage').val('');
            }
        })
        $("input[name='down_payment']").click(function(){
            $('#down_payment_percentage').attr('disabled',true);
            if($(this).val()==1){
                $('#down_payment_percentage').attr('disabled',false);
            }else{
                $('#down_payment_percentage').val('');
            }
        });
        $("input[name='winning_method']").click(function(){
            if($(this).val()== 'PACKAGE'){
                $('#conditional_typeCT1').attr('disabled',false);
                $('#conditional_typeCT1').prop("checked", false);
                $('#conditional_typeCT2').prop("checked", false);
            }else{
                $('#conditional_typeCT1').attr('disabled',true);
                $('#conditional_typeCT2').prop("checked", true);
            }
        });
        $('#btn_save_flow').click(function(){
            if($('#frmParameter')[0].checkValidity()){
                var form = "#frmParameter";
                var frmData = $(form).serializeArray();
                $.ajax({
                    url : "{{ route('tender.save', ['id'=>$id, 'type'=>$type]) }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    beforeSend: function( xhr ) {
                        Loading.Show();
                    }
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Tender Parameter "+response.data.number+" saved.", "success", 3000);
                        $('#btn_next_flow')
                            .attr('disabled',false)
                            .removeClass('btn-light')
                            .removeClass('btn-primary')
                            .addClass('btn-primary');

                        if(response.next!=''){
                            if(response.next!='' && response.next != null){
                                location.href = response.next;
                            }
                        }else{
                            location.href = "{{ route('tender.save', ['id'=>$id, 'type' => $type]) }}";
                        }
                        $('#btn_save_flow').prop('disabled',true);

                    }else{
                        showAlert("Tender Parameter not saved.", "danger", 3000);
                        $(form+'_fieldset').attr("disabled",false);
                    }

                }).fail(defaultAjaxFail)
                .always(function(jqXHR, textStatus, errorThrown) {
                    Loading.Hide();
                });

            }else{
                showAlert("Please fill all required values.", "warning", 3000);
            }
        });
        $('#frmParameter').on('change','input,select,radio,textarea', function(e){
            $('#btn_save_flow').prop('disabled',false);
        });
        @endif

        $('#btn_next_flow').click(function(){
            onClickNext();
        });

    });

});
</script>
@endsection

@section('styles')
@parent
<style type="text/css">
label.error{
    display:none !important;
}
</style>
@endsection
