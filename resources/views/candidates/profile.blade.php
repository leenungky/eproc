@extends('layouts.two_column')

@include($accordionMenu)

@section('menuheader')
<div class="col-sm-12 full-width">
    <div class="row">
        <div class="heading-left">
            <a href="{{ route('admin.'.Request::segment(1)) }}" class="text-logo"><i class="fas fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{ Str::upper(__('homepage.company_profile')) }}</a>
            <a href="{{ route('admin.'.Request::segment(1)) }}" class="ico-logo"><i class="fas fa-building icon-heading" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
@endsection

@section('menubody')
@yield('accordionmenu')
@endsection

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{ __('homepage.company_profile') }}</span>              
</div>
<div class="card-header-right">                       
</div>
@endsection

@section('contentbody')
<?php $errors; ?>
@if ($errors->any())
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form  method="post" action="{{ url('candidates/approval') }}" class="form-horizontal" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-sm-6">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <th class="th-label" width="50%">{{ __('homepage.company_name') }}</th>
                        <td class="td-value">{{ $candidate_vendor->company_name }}</td>
                    </tr>
                    <tr>
                        <th class="th-label">{{ __('homepage.company_type') }}</th>
                        <td class="td-value">{{ $candidate_vendor->company_type }}</td>
                    </tr>
                    <tr>
                        <th class="th-label">{{ __('homepage.vendor_group') }}</th>
                        <td class="td-value">{{ __('homepage.'.$candidate_vendor->vendor_group) }}</td>
                    </tr>
                    <tr>
                        <th class="th-label">{{ __('homepage.purchasing_organization') }}</th>
                        <td class="td-value">{{ $candidate_vendor->purchase_org_code . ' (' . $candidate_vendor->purchase_org_description . ')' }}</td>
                    </tr>
                    <tr>
                        <th class="th-label">{{ __('homepage.vendor_code') }}</th>
                        <td class="td-value">{{ $candidate_vendor->vendor_code }}</td>
                    </tr>
                    <tr>
                        <th class="th-label">{{ __('homepage.business_partner_code') }}</th>
                        <td class="td-value">{{ (is_null($candidate_vendor->sap_vendor_code) || $candidate_vendor->sap_vendor_code=='') ? '' : $candidate_vendor->business_partner_code }}</td>
                    </tr>
                    <tr>
                        <th class="th-label">{{ __('homepage.sap_vendor_code') }}</th>
                        <td class="td-value">{{ $candidate_vendor->sap_vendor_code }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <th class="th-label" width="50%">{{ __('homepage.registration_status') }}</th>
                        <td class="td-value">{{ __('homepage.' . $candidate_vendor->registration_status) }}</td>
                    </tr>
                    <tr>
                        <th class="th-label">{{ __('homepage.status_of_update_vendor_data') }}</th>
                        <td class="td-value">{{ $candidate_vendor->update_vendor_data_status }}</td>
                    </tr>
                    <!--<tr>
                        <th class="th-label">Active No. DPT</th>
                        <td class="td-value">-</td>
                    </tr>-->
                    <tr>
                        <th class="th-label">{{__('homepage.print_avl')}}</th>
                        @if($candidate_vendor->registration_status === 'vendor')
                            <td class="td-value"><a target="_blank" href="{{ route("pdf.report",['id'=>$candidate_vendor->id]) }}">{{__('homepage.print')}}<i class="fas fa-print ml-2"></i></a></td>
                        @else
                            <td class="td-value">-</td>
                        @endif                                   
                    </tr>
                    <tr>
                        <th class="th-label">{{ __('homepage.vendor_status') }}</th>
                        <td class="td-value"><span class="badge badge-pill badge-{{$candidate_vendor->vendor_status == 'active' ? 'success' : 'danger'}}">{{$candidate_vendor->vendor_status}}</span></td>
                    </tr>
                    <tr>
                        <th class="th-label">{{ __('homepage.legacy') }}</th>
                        <td class="td-value">{{$candidate_vendor->is_legacy ? __('homepage.yes') : __('homepage.no')}}</td>
                    </tr>
                    @if($candidate_vendor->is_legacy && !is_null($candidate_vendor->purchase_org_code_1))
                    <tr>
                        <th class="th-label">{{ __('homepage.purchasing_organization') }}</th>
                        <td class="td-value">{{ $candidate_vendor->purchase_org_code_1 . ' (' . $candidate_vendor->purchase_org_description_1 . ')' }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="row">
            <fieldset class="fieldset-border" style="width: 100%;">
                <legend class="legend-border"><i class="fas fas-times-circle-o"></i>{{ __('homepage.document_history') }}</legend>
                <div class="control-group">
                    <table class="table table-bordered table-sm table-striped">
                        <thead>
                            <tr>
                                <th>{{__('homepage.attachment')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">No data available in table</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>                
    </div>
    <div class="col-sm-12">
        <div class="row">
            <fieldset class="fieldset-border" style="width: 100%;">
                <legend class="legend-border"><i class="fas fas-times-circle-o"></i>{{ __('homepage.comments_history') }}</legend>
                <div class="control-group">
                    <table class="table table-bordered table-sm table-striped">
                        <thead>
                            <tr>
                                <th>{{__('homepage.userid')}}</th>
                                <th>{{__('homepage.name')}}</th>
                                <th>{{__('homepage.activity')}}</th>
                                <th>{{__('homepage.start_date')}}</th>
                                <th>{{__('homepage.finish_date')}}</th>
                                <th>{{__('homepage.comment')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($commentsHistory) > 0)
                                @foreach($commentsHistory as $history)
                                <tr>
                                    <td>{{ $history->created_by }}</td>
                                    <td>{{ $history->name }}</td>
                                    <td>{{ $history->activity }}</td>
                                    <td>{{ $history->started_at }}</td>
                                    <td>{{ $history->finished_at }}</td>
                                    <td>{{ $history->remarks }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">No data available in table</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>                
    </div>
    @if(!$profilesubmission)
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info text-center">                        
                <strong><i>{{ __('homepage.there_is_no_new_profile_data_or_profile_data_changes_from_the_vendor') }}</i></strong>                        
            </div>
        </div>
    </div>
    @endif
    @if($canProcess)
    <div class="row"><hr /></div>
    <div class="btn-group" style="float: right;">
        <select class="form-control" id='select-action' name="action">
            <option value=""> -- {{ __('homepage.action_list') }} -- </option>
            <option value="next">{{ __('homepage.next') }}</option>
            <option value="revise">{{ __('homepage.revise') }}</option>
        </select>
    </div>    
    @endif
</form>
@endsection

@section('modals')
<div id="modalVerification" class="modal fade bd-common-form" tabindex="-1" role="dialog" aria-labelledby="modalVerificationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{__('homepage.cancel')}}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Content Here -->
                </div>
                <div class="modal-footer">
                    <button id="btn-cancel" type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{__('homepage.cancel')}}</button>
                    <button id="btn-save" type="button" class="btn btn-sm btn-primary">{{__('homepage.submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    require(["jquery", "bootstrap", "bootstrap-util", "metisMenu", "bootstrap-fileinput", "select2"], function () {
        let isSubmitted = '{{ $profilesubmission }}',
            title = '',
            status = '',
            statusKey = '',
            finalApprover = `{{ $finalapprover }}`,
            vendorGroup = `{{ $candidate->vendor_group }}`,
            companyType = `{{ $candidate->company_type }}`,
            purchaseOrg = `{{ $candidate->purc_org_code }}`,
            purchaseOrgDesc = `{{ $candidate->purc_org_description }}`,
            businessPartnerCode = `{{ $candidate_vendor->business_partner_code }}`,
            sapVendorCode = `{{ $candidate_vendor->sap_vendor_code }}`,
            alreadyExistSAP = `{{ $candidate_vendor->already_exist_sap }}`,
            registrationStatus = `{{ $candidate_vendor->registration_status }}`,
            statusVendor = registrationStatus === 'vendor' ? 'Vendor' : 'Candidate',
            additionalContent = ``;
        $('#select-action').change(function(){
            if(isSubmitted === '1'){
                $('#modalVerification').html(`@include('candidates.modal-verification')`);
                statusKey = this.value;
                if (this.value === 'next') {
                    title = '<i class="fas fa-question-circle mr-2"></i>Approve Confirmation';
                    if(finalApprover == 'true'){
                        status = `${statusVendor} Approved by QMR`;
                    } else {
                        status = `${statusVendor} Approved by Admin Vendor`;
                    }
                    let displaySelection = 'display-block';
                    let isExistSelectedYes = '';
                    let isExistSelectedNo = '';
                    if(registrationStatus !== 'vendor'){
                        if(alreadyExistSAP === '1'){
                            isExistSelectedYes = 'checked';
                            isExistSelectedNo = '';
                        } else {
                            displaySelection = 'display-none';
                            isExistSelectedYes = '';
                            isExistSelectedNo = 'checked';
                        }
                        additionalContent = `
                            <div class="form-group">
                                <label class="control-label col-form-label">Vendor has already exist from SAP ?</label><br />
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="already_exist_sap" id="existing_sap_0" type="radio" class="custom-control-input form-control" value="1" required="" ${isExistSelectedYes}> 
                                    <label for="existing_sap_0" class="custom-control-label">{{__('homepage.yes')}}</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="already_exist_sap" id="existing_sap_1" type="radio" class="custom-control-input form-control" value="0" required="" ${isExistSelectedNo}>
                                    <label for="existing_sap_1" class="custom-control-label">{{__('homepage.no')}}</label>
                                </div>
                            </div>
                            <div class="form-group sap-code ${displaySelection}">
                                <label class="control-label col-form-label">{{__('homepage.business_partner_code')}}</label>
                                <input type="text" name="business_partner_code" class="form-control" maxlength="10" value="${businessPartnerCode}" autocomplete="off"/>
                            </div>
                            <div class="form-group sap-code ${displaySelection}">
                                <label class="control-label col-form-label">{{__('homepage.sap_vendor_code')}}</label>
                                <input type="text" name="sap_vendor_code" class="form-control" value="${sapVendorCode}" maxlength="10" autocomplete="off"/>
                            </div>
                        `;
                    }
                } else {
                    title = '<i class="fas fa-question-circle mr-2"></i>Revise Confirmation';
                    if(finalApprover == 'true'){
                        status = `QMR ask ${statusVendor} to revise Company Profile data`;
                    }else{
                        status = `Admin Vendor ask ${statusVendor} to revise Company Profile data`;
                    }
                    additionalContent = ``;
                }
                // Set HTML & input value
                $('#modalVerificationLabel').html(title);
                $('input[name=vendor_id]').val('{{ $candidate->vendor_id }}');
                $('input[name=vendor_profile_id]').val('{{ $candidate->id }}');
                $('input[name=status_key]').val(statusKey);
                $('input[name=status]').val(status);
                $('input[name=vendor_group]').val(vendorGroup);
                $('input[name=company_type]').val(companyType);
                $('input[name=purchase_org]').val(purchaseOrg);
                $('input[name=purchase_org_description]').val(purchaseOrgDesc);
                $('#additional-content').html(additionalContent);

                $('#modalVerification').modal();
                
                $("input[name=already_exist_sap]").change(function(){
                    if($('#existing_sap_0').is(':checked')){
                        $(".sap-code").fadeIn();
                        $(".sap-code input[name=business_partner_code]").attr("required", true);
                    } else {
                        $(".sap-code").fadeOut();
                        $(".sap-code input").removeAttr("required");
                    }
                });
                
                $("#modalVerification").on("hidden.bs.modal", function () {
                    // put your default event here
                    $('#select-action').val("");
                });
                
                $("#btn-confirm-approval").click(function(){
                    let forms = $("#form-candidate-approval");
                    if (forms[0].checkValidity() === true) {
                        
                    }
                    forms[0].classList.add('was-validated');     
                    if($(".was-validated .form-control:invalid").length > 0){
                        $('html, body').animate({
                            scrollTop: $(".was-validated .form-control:invalid").offset().top - 60
                        }, 500);
                        return;
                    }
                    let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
                    $('#btn-confirm-approval').attr("disabled", true);
                    $('#btn-confirm-approval').text('Submitting...');
                    $('#btn-confirm-approval').prepend(onProcessSpinner);
                    let frmData = new FormData($("#form-candidate-approval")[0]);//$("#registration-form").serializeArray();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('candidate.approval') }}",
                        type: 'POST',
                        data: frmData,
                        cache : false,
                        processData: false,
                        contentType: false
                    }).done(function (response, textStatus, jqXhr) {
                        if (response.success) {
                            showAlert(response.message, "success", 3000);
                            if(registrationStatus === 'vendor'){
                                setTimeout(function(){
                                    location.href = "{{ route('admin.vendors') }}";
                                }, 3000);
                            } else {
                                setTimeout(function(){
                                    location.href = "{{ route('admin.candidates') }}";
                                }, 3000);
                            }
                        } else {
                            showAlert(response.message, "danger", 3000);
                            $('#btn-confirm-approval').html('Submit');
                            $('#btn-confirm-approval').attr("disabled", false);
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        showAlert("Data not saved. Please try again", "danger", 3000);
                        $('#btn-confirm-approval').html('Submit');
                        $('#btn-confirm-approval').attr("disabled", false);
                    });
                });
            } else {
                $('body').append(`@include('candidates.modal-common')`);
                title = '<i class="fas fa-info-circle mr-2"></i>Information';
                // Set HTML & input value
                $('#modalVerificationLabel').html(title);
                let content = `
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert text-center">                        
                                <strong><i>{{ __('homepage.there_is_no_new_profile_data_or_profile_data_changes_from_the_vendor') }}</i></strong>                        
                            </div>
                        </div>
                    </div>`;
                $('#modalVerification .modal-body').html(content);
                $('#modalVerification').modal('toggle');
            }
        });
    });
    function confirm(obj) {
        let key = obj.dataset.key,
                refId = obj.dataset.id,
                title = ``,
                statusId = ``,
                status = ``;
        $('#btn-reject').prop("disabled", true);
        $('#btn-approve').prop("disabled", true);
        $('body').append(`@include('candidates.modal-verification')`);
        if (key === 'reject') {
            title = 'Reject Confirmation';
            statusId = 4;
            status = `Applicant Rejected by Admin`;
        } else {
            title = 'Approve Confirmation';
            statusId = 3;
            status = `Applicant Verified by Admin`;
        }
        // Set HTML & input value
        $('#modalVerificationLabel').html(title);
        $('input[name=ref_id]').val(refId);
        $('input[name=status_id]').val(statusId);
        $('input[name=status]').val(status);

        $('#modalVerification').modal('toggle');
        $('#btn-reject').prop("disabled", false);
        $('#btn-approve').prop("disabled", false);
    }
    
</script>
@endsection