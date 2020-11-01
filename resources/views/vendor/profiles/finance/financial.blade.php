@extends('layouts.two_column')

@include($accordionMenu)

@php 
    $formName = 'frmfinance';
    $formLayout = 'vendor.profiles.form.form_finance';
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
    <span class="heading-title">{{ __('homepage.finance_data') }}: {{ __('homepage.financial_statements') }}</span>
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
    <label for="partnerName" class="col-form-label lbl-right lbl-field-info">{{ __('homepage.please_complete_financial_statement') }}&nbsp;:</label>
</div>     
@include('vendor.profiles.partials.finance_data')
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
<script type="text/javascript">
require(['moment','accounting'], function(){
    require(['jquery','datetimepicker','jquery-mask',"bootstrap-fileinput"], function () {
        require(["bootstrap-fileinput-fas"],function(){
            $(function(){
        $('input[id*="total"]').attr('readonly',true);
        $('.money').mask('#.##0,00', {reverse: true});
        $('.balance_sheet input:not([readonly]):not([disabled])').change(function(){
            processAccounting();
        })
        $('.date').datetimepicker({
            format: uiDateFormat,
        });
        $('.fs-showhide').click(function(){
            this.dataset.hidden = this.dataset.hidden=='true'?'false':'true';
            $('.fs_detail'+this.dataset.index).attr('hidden',this.dataset.hidden=='true');
            let i = $(this).find('i');
            i.removeClass('fa-plus').removeClass('fa-minus');
            if(this.dataset.hidden=='true'){
                i.addClass('fa-plus');
                $('.row'+this.dataset.index).attr('rowspan',$('.row'+this.dataset.index).attr('rowspan')*1-{{$mergeCnt}})
            }else{
                i.addClass('fa-minus');
                $('.row'+this.dataset.index).attr('rowspan',$('.row'+this.dataset.index).attr('rowspan')*1+{{$mergeCnt}})
            }
        });
        let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
        $("#financial_statement_attachment").fileinput({'showUpload':false, 'previewFileType':'any','theme':'fas', maxFileSize : maxUploadSize});
        $("#{{$formName}}_modal").on('show.bs.modal',function(){
            $.each($('.money'), function(k,el){
                el.value = accounting.format(el.value);
            });
            processAccounting();
            setBusinessClass();
        });
        $('#annual_revenue').keyup(function(){
            setBusinessClass();
        });
    })

    function setBusinessClass(){
        let val = accounting.unformat($('#annual_revenue').val());
        let size = 'Small';
        $.each({!!json_encode($businessClass)!!}, function(k,v){
            if(val>v) size = k;
        })
        $('#business_class').val(size);
    }
    function processAccounting(){
        let lti = accounting.unformat($('#long_term_investments').val());
        let sti = accounting.unformat($('#short_term_investments').val());
        let tr = lti+sti;
        $('#total_receivables').val(accounting.formatNumber(tr));

        let cash = accounting.unformat($('#cash').val());
        let bank = accounting.unformat($('#bank').val());
        let inv = accounting.unformat($('#inventories').val());
        let wip = accounting.unformat($('#work_in_progress').val());
        let tca = cash+bank+inv+wip+tr;
        $('#total_current_assets').val(accounting.formatNumber(tca));

        let eam = accounting.unformat($('#equipments_and_machineries').val());
        let fix = accounting.unformat($('#fixed_inventories').val());
        let build = accounting.unformat($('#buildings').val());
        let land = accounting.unformat($('#lands').val());
        let tfa = eam+fix+build+land;
        $('#total_fixed_assets').val(accounting.formatNumber(tfa));

        let oa = accounting.unformat($('#other_assets').val());
        let ta = tca+tfa+oa;
        $('#total_assets').val(accounting.formatNumber(ta));

        let id = accounting.unformat($('#incoming_debts').val());
        let tp = accounting.unformat($('#taxes_payables').val());
        let op = accounting.unformat($('#other_payables').val());
        let tstb = id+tp+op;
        $('#total_short_term_debts').val(accounting.formatNumber(tstb));
        $('#total_liabilities').val(accounting.formatNumber(ta));
        let ltb = accounting.unformat($('#long_term_payables').val());
        let tnw = ta - tstb - ltb;
        $('#total_net_worth').val(accounting.formatNumber(tnw));
        $('#total_net_worth_with_land_building').val(accounting.formatNumber(tnw));
        $('#total_buildings').val(accounting.formatNumber(build));
        $('#total_lands').val(accounting.formatNumber(land));
        let tnwelb = tnw - build - land;
        $('#total_net_worth_exclude_land_building').val(accounting.formatNumber(tnwelb));
    }
});
});
});
</script>
@endsection

@section('styles')
@parent
<style>
.balance_sheet input[type=number]::-webkit-inner-spin-button, 
.balance_sheet input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
.fs-showhide{cursor:pointer}
</style>
@endsection