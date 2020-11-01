@extends('layouts.two_column')

@include($accordionMenu)

@php 
    $formName = 'frmtax';
    $formLayout = 'vendor.profiles.form.form_tax';
@endphp

@section('menuheader')
<div class="col-sm-12 full-width">
    <div class="row">
        <div class="heading-left">
            <a href="{{ $profileUrl }}" class="text-logo"><i class="fas fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{ Str::upper(__('homepage.show_profile')) }}</a>
            <a href="{{ $profileUrl }}" class="ico-logo"><i class="fas fa-building icon-heading" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
@endsection

@section('menubody')
@yield('accordionmenu')
@endsection

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{ __('homepage.finance_data') }}: {{ __('homepage.tax_documents') }}</span>
</div>
<div class="card-header-right">
    <div class="button-group">
    @if(auth()->user()->user_type=='vendor' && !$blacklisted)
        @if($checklist->is_submitted)
            @if($checklist->is_approved || $checklist->is_revised)
                <button data-id="{!! $profiles->count() > 0 ? $profiles[0]->vendor_profile_id : '' !!}" onclick="repeatAllData(this);" class="btn btn-sm btn-link"><i class="fas fa-sync" aria-hidden="true"></i></button>
                <button data-id="{!! $profiles->count() > 0 ? $profiles[0]->vendor_profile_id : '' !!}" onclick="finishAllData(this);" class="btn btn-sm btn-primary mr-2">Finish</button>
                <button id="btn_create_new_{{$formName}}" class="btn btn-sm btn-success" data-toggle="modal" data-target="#{{$formName}}_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{ __('homepage.create_new_entry') }}</button>
            @else
                <button class="btn btn-sm btn-link" disabled=""><i class="fas fa-sync" aria-hidden="true"></i></button>
                <button class="btn btn-sm btn-secondary" disabled="">{{ __('homepage.finish') }}</button>
                <button class="btn btn-sm btn-secondary" disabled=""><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{ __('homepage.create_new_entry') }}</button>
            @endif
        @else
            <button data-id="{!! $profiles->count() > 0 ? $profiles[0]->vendor_profile_id : '' !!}" onclick="repeatAllData(this);" class="btn btn-sm btn-link"><i class="fas fa-sync" aria-hidden="true"></i></button>
            <button data-id="{!! $profiles->count() > 0 ? $profiles[0]->vendor_profile_id : '' !!}" onclick="finishAllData(this);" class="btn btn-sm btn-primary mr-2">Finish</button>
            <button id="btn_create_new_{{$formName}}" class="btn btn-sm btn-success" data-toggle="modal" data-target="#{{$formName}}_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{ __('homepage.create_new_entry') }}</button>
        @endif
    @endif
    </div>
</div>
@endsection

@section('contentbody')
<div class="col-sm-12 mt-lg-2 mb-lg-3">
    <label for="partnerName" class="col-form-label lbl-right lbl-field-info">{{ __('homepage.please_complete_tax_document') }}&nbsp;:</label>
</div>     
@include('vendor.profiles.partials.common_data')
@endsection

@section('modals')
@if(auth()->user()->user_type=='vendor' && !$blacklisted)
    <?php
    $modal1 = [
        'title' => __("homepage.create_new_entry"),
        'contents' => '',
        'form_layout' => $formLayout,
        'form_name' => $formName,
    ];
    ?>
    @include('layouts.modal_common',$modal1)
    @include('layouts.modal_delete')
@endif
@endsection

@section('modules-scripts')
@include('vendor.profiles.profile_script')
@include('vendor.profiles.profile_modal_twopage')
<script>
require(['moment'], function(){
    require(['jquery','datetimepicker',"bootstrap-fileinput"], function () {
require(["bootstrap-fileinput-fas"],function(){
    
    $('.date').datetimepicker({
        format: uiDateFormat,
    });
    let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
    $("#tax_document_attachment").fileinput({'showUpload':false, 'previewFileType':'any','theme':'fas', maxFileSize: maxUploadSize, allowedFileExtensions : ['jpeg', 'jpg', 'gif', 'pdf']});
    $("#tax_document_type").change(function(){
        if($(this).val()=='ID1'){
            $("#tax_document_number").attr("maxlength",16);
            $("#tax_document_number").val($("#tax_document_number").val().substr(0,16));
        }else{
            $("#tax_document_number").removeAttr("maxlength");
        }
    })

});
});
});
</script>
@endsection
