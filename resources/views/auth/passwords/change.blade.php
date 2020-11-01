@extends('layouts.one_column')

@include('layouts.navigation')
@php
$blacklisted = $blacklisted ?? false;
@endphp
@section('contentheader')
    <i class="fas fa-user mr-2" aria-hidden="true"></i>
    {{ __('homepage.change_password') }}
@endsection

@section('contentbody')
<div class="row">
    <div class="row col-12">
        <div class="col-6">
            <fieldset id="fieldset"{{$blacklisted ? ' disabled':''}}>
            <form id="changepassword" class="was-validated" method="POST" action="{{route('change-password')}}">
            @csrf
            <div class="form-group row">
                <label for="current_password" class="control-label col-form-label col-sm-4">{{__('homepage.current_password')}}</label>
                <div class="col-sm-8 input-group input-group-sm">
                    <input type="password" id="current_password" name="current_password" class="form-control form-control-sm" required>
                    <div class="input-group-append">
                        <i class="togglepassword input-group-text fas fa-eye"></i>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="new_password" class="control-label col-form-label col-sm-4">{{__('homepage.new_password')}}</label>
                <div class="col-sm-8 input-group input-group-sm">
                    <input type="password" id="new_password" name="new_password" class="form-control form-control-sm" required pattern="^(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*">
                    <div class="input-group-append">
                        <i class="togglepassword input-group-text fas fa-eye"></i>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="repeat_new_password" class="control-label col-form-label col-sm-4">{{__('homepage.repeat_new_password')}}</label>
                <div class="col-sm-8 input-group input-group-sm">
                    <input type="password" id="repeat_new_password" name="repeat_new_password" class="form-control form-control-sm" required>
                    <div class="input-group-append">
                        <i class="togglepassword input-group-text fas fa-eye"></i>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-8 offset-sm-4">
                    <button id="saving" class="btn btn-sm btn-info" hidden><span class="spinner-border spinner-border-sm mr-2" role="status"></span>{{__('homepage.saving')}}</button>
                    <button id="save" class="btn btn-sm btn-info"><i class="fas fa-save mr-1"></i>{{__('homepage.save')}}</button>
                </div>
            </div>
            </form>
            </fieldset>
        </div>
        <div class="col-6">
            {!!__('homepage.password_change_guidelines')!!}
        </div>
    </div>
</div>
@endsection

@section('modules-scripts')
<script type="text/javascript">
require(["jquery", "bootstrap", "bootstrap-util"], function () {
    $('#new_password').change(function(){
        $('#repeat_new_password').attr('pattern',this.value);
    });
    $('.togglepassword').on('click',function(){
        if($(this).hasClass('fa-eye')){
            $(this).closest('.input-group').find('input').attr('type','text');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        }else{
            $(this).closest('.input-group').find('input').attr('type','password');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    $('#save').click(function(e){
        let form = $('#changepassword');
        if(form[0].checkValidity() !== false){
            e.preventDefault();
            let frmData = new FormData(form[0]);
            $('#fieldset').attr("disabled",true);
            $('#save').attr('hidden',true);
            $('#saving').attr('hidden',false);
            $.ajax({
                url : "{{ route('change-password') }}",
                type : 'POST',
                data : frmData,
                cache : false,
                processData: false,
                contentType: false,
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    showAlert(response.message, "success", 3000);
                    form[0].reset();
                }else{
                    showAlert(response.message, "danger", 3000);
                }
                $('#fieldset').attr("disabled",false);
                $('#save').attr('hidden',false);
                $('#saving').attr('hidden',true);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $('#fieldset').attr("disabled",false);
                $('#save').attr('hidden',false);
                $('#saving').attr('hidden',true);
            });
        }
    })
});
</script>
<style>
.input-group-sm .input-group-append{
    height:31px;
}
</style>
@endsection
