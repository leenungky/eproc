@extends('layouts.two_column')

@include('applicants.profiles.accordion_menu')

@php 
    $taxDocumentTypes = [
        'NPWP', 
        'SPKKP', 
        'SPT', 
        'Report SPT PPN', 
        'E-NOFA', 
        'Surat Pembebasan Pajak',
    ];
    $fields = ['tax_document_type','tax_document_no','tax_document_date','tax_document_attachment'];
    $attachmentList = ['tax_document_attachment'];
    $formName = 'frmtax';
    $formLayout = 'applicants.profiles.form.form_tax';
@endphp

@section('menuheader')
<div class="col-sm-12 full-width">
    <div class="row">
        <div class="heading-left">
            <a href="{{ route('profile.show') }}" class="text-logo"><i class="fas fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{ Str::upper(__('homepage.show_profile')) }}</a>
            <a href="{{ route('profile.show') }}" class="ico-logo"><i class="fas fa-building icon-heading" aria-hidden="true"></i></a>
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
        @if( $profiles->count() > 0 )
            <button data-id="{!! $profiles->count() > 0 ? $profiles[0]->applicant_id : '' !!}" onclick="repeatAllData(this);" class="btn btn-sm btn-link"><i class="fas fa-sync" aria-hidden="true"></i></button>
            <button data-id="{!! $profiles->count() > 0 ? $profiles[0]->applicant_id : '' !!}" onclick="finishAllData(this);" class="btn btn-sm btn-primary mr-2">Finish</button>
        @else
            <button class="btn btn-sm btn-link" disabled=""><i class="fas fa-sync" aria-hidden="true"></i></button>
            <button class="btn btn-sm btn-secondary" disabled="">{{ __('homepage.finish') }}</button>
        @endif
        <button id="btn_create_new_{{$formName}}" class="btn btn-sm btn-success" data-toggle="modal" data-target="#{{$formName}}_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{ __('homepage.create_new_entry') }}</button>
    </div>
</div>
@endsection

@section('contentbody')
<div class="col-sm-12 mt-lg-2 mb-lg-3">
    <label for="partnerName" class="col-form-label lbl-right lbl-field-info">{{ __('homepage.please_complete_tax_document') }}&nbsp;:</label>
</div>     
@include('applicants.profiles.partials.common_data')
@endsection

@section('modals')
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
@endsection

@section('modules-scripts')
@include('applicants.profiles.profile_script')
<script>
require(['jquery','moment'], function () {
require(["datetimepicker"], function () {
    
    $(function(){
        $('input[id*="date"]').datetimepicker({
            format: uiDateFormat,
        });
    });

});
});
</script>
@endsection
