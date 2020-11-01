@extends('layouts.two_column')

@include($accordionMenu)

@php
    $formName = 'frmtools';
    $formLayout = 'vendor.profiles.form.form_tool';
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
    <span class="heading-title">{{ __('homepage.competency_and_workexperience') }}: {{ __('homepage.tools') }}</span>
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
<script type="text/javascript">
    require(['datetimepicker'], function () {
        $(function(){
            $(".date").datetimepicker({
                format: uiDateFormat
            });
        });
    });        
</script>
@endsection
