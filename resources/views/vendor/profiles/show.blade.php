@extends('layouts.one_column')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">{{ __('homepage.company_profile') }}</span>
</div>
<div class="card-header-right">
    @if($checklist->is_submitted)
        @if($checklist->is_approved || $checklist->is_revised)
            <a class="btn btn-sm btn-primary" href="{{ route('profile.edit', 'general') }}"><i class="fas fa-edit mr-2" aria-hidden="true"></i>{{ __('homepage.change_profile') }}</a>
        @else
            <a class="btn btn-sm btn-primary" href="{{ route('profile.edit', 'general') }}"><i class="fas fa-eye mr-2" aria-hidden="true"></i>{{ __('homepage.show_profile_detail') }}</a>
        @endif
    @else
        <a class="btn btn-sm btn-primary" href="{{ route('profile.edit', 'general') }}"><i class="fas fa-edit mr-2" aria-hidden="true"></i>{{ __('homepage.change_profile') }}</a>
    @endif
</div>
@endsection

@section('contentbody')
<div class="has-footer">
    <div class="card-fixed">
        <div class="col-sm-12 mt-lg-3 mb-lg-3">
            <h3>{{ $vendor->company_name }}</h3>
            <p>{{ __('homepage.vendor_code') }} : {{ $vendor->vendor_code }}</p>
        </div>
        <br>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs @if(config('eproc.theme')=='style2') nav-fill @endif">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tabProfile">{{ __('homepage.profile') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabDocStatusExpired">{{ __('homepage.doc_status_expired') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabTenderFollowed">{{ __('homepage.tender_followed') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabCommentsHistory">{{ __('homepage.comments_history') }}</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div id="tabProfile" class="container tab-pane active"><br>
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th class="th-label" width="50%">{{ __('homepage.company_name') }}</th>
                                    <td class="td-value">{{ $vendor->company_name }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.company_type') }}</th>
                                    <td class="td-value">{{ $vendor->company_type }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.vendor_group') }}</th>
                                    <td class="td-value">{{ __('homepage.'.$vendor->vendor_group) }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.purchasing_organization') }}</th>
                                    <td class="td-value">{{ $vendor->purchase_org_code . ' (' . $vendor->purchase_org_description . ')' }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.vendor_code') }}</th>
                                    <td class="td-value">{{ $vendor->vendor_code }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.business_partner_code') }}</th>
                                    <td class="td-value">{{ (is_null($vendor->sap_vendor_code) || $vendor->sap_vendor_code=='') ? '' : $vendor->business_partner_code }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.sap_vendor_code') }}</th>
                                    <td class="td-value">{{ $vendor->sap_vendor_code }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th class="th-label" width="50%">{{ __('homepage.registration_status') }}</th>
                                    <td class="td-value">{{ __('homepage.' . $vendor->registration_status) }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.status_of_update_vendor_data') }}</th>
                                    <td class="td-value">{{ $vendor->update_vendor_data_status }}</td>
                                </tr>
                                <!--<tr>
                                    <th class="th-label">Active No. DPT</th>
                                    <td class="td-value">-</td>
                                </tr>-->
                                <tr>
                                    <th class="th-label">{{__('homepage.print_avl')}}</th>
                                    @if($vendor->registration_status === 'vendor')
                                        <td class="td-value"><a target="_blank" href="{{ route("pdf.report") }}">{{__('homepage.print')}}<i class="fas fa-print ml-2"></i></a></td>
                                    @else
                                        <td class="td-value">-</td>
                                    @endif                                   
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.vendor_status') }}</th>
                                    <td class="td-value"><span class="badge badge-pill badge-{{$vendor->vendor_status == 'active' ? 'success' : 'danger'}}">{{$vendor->vendor_status}}</span></td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.legacy') }}</th>
                                    <td class="td-value">{{$vendor->is_legacy ? __('homepage.yes') : __('homepage.no')}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div><hr /></div>
                <div class="col-sm-12 row">            
                    <div class="col">
                        <ul class="">{{ __('homepage.administration_data') }}
                            <li><a href="{{ route('profile.edit', 'general') }}">{{ __('homepage.general') }}@if($checklist->general_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->general_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->general_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'deeds') }}">{{ __('homepage.deed') }}@if($checklist->deed_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->deed_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->deed_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'shareholders') }}">{{ __('homepage.shareholders') }}@if($checklist->shareholder_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->shareholder_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->shareholder_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'bod-boc') }}">{{ __('homepage.bod_boc') }}@if($checklist->bodboc_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->bodboc_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->bodboc_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'business-permit') }}">{{ __('homepage.business_permit') }}@if($checklist->businesspermit_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->businesspermit_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->businesspermit_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'pic') }}">{{ __('homepage.person_in_charge') }} (PIC)@if($checklist->pic_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->pic_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->pic_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                        </ul>
                    </div>
                    <div class="col">
                        <ul class="">{{ __('homepage.competency_and_workexperience') }}
                            <li><a href="{{ route('profile.edit', 'tools') }}">{{ __('homepage.equipment') }}@if($checklist->equipment_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->equipment_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->equipment_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'expert') }}">{{ __('homepage.experts') }}@if($checklist->expert_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->expert_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->expert_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'certification') }}">{{ __('homepage.certifications') }}@if($checklist->certification_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->certification_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->certification_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i>  @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'competency') }}">{{ __('homepage.scope_of_supply') }}@if($checklist->scopeofsupply_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->scopeofsupply_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->scopeofsupply_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'work-experience') }}">{{ __('homepage.experience') }}@if($checklist->experience_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->experience_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->experience_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                        </ul>
                    </div>
                    <div class="col">
                        <ul class="">{{ __('homepage.finance_data') }}
                            <li><a href="{{ route('profile.edit', 'bank-account') }}">{{ __('homepage.bank_account') }}@if($checklist->bankaccount_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->bankaccount_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->bankaccount_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'financial') }}">{{ __('homepage.financial_statements') }}@if($checklist->financial_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->financial_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->financial_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                            <li><a href="{{ route('profile.edit', 'tax-document') }}">{{ __('homepage.tax_documents') }}@if($checklist->tax_status === 'finish') <i class="fa fa-check font-success ml-2" title="{{ __('homepage.finish_complete_data') }}"></i>@elseif($checklist->tax_status === 'warning') <i class="fa fa-exclamation-triangle font-warning ml-2" title="{{ __('homepage.please_complete_data') }}"></i> @elseif($checklist->tax_status === 'not-finish') <i class="fa fa-question-circle font-warning ml-2" title="{{ __('homepage.please_finish_data') }}"></i> @endif</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="tabDocStatusExpired" class="container tab-pane fade"><br>
            <div class="col-sm-12">
                    <div class="row">
                        <table class="table table-bordered table-sm table-striped full-width">
                            <thead>
                                <tr>
                                    <th>Company Profile Data</th>
                                    <th>Document Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Current Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($doc_expiry))
                                    @foreach($doc_expiry as $value)
                                    <tr>
                                        <td>{{ $value->type}}</td>
                                        <td>{{ $value->document_type}}</td>
                                        <td>{{ $value->valid_from_date }}</td>
                                        <td>{{ $value->valid_thru_date }}</td>
                                        <td>{{ $value->current_date}}</td>
                                        <td>
                                            @if ($value->status=="expired")
                                                <label class="bg-danger text-white" style="padding: 0px 4px;">Expired</label>
                                            @elseif ($value->status=="valid")
                                                <label class="bg-success text-white" style="padding: 0px 4px;">Valid</label>
                                            @else
                                                <label class="bg-warning text-dark" style="padding: 0px 4px;">Expiring</label>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="tabTenderFollowed" class="container tab-pane fade"><br>
                @include('announcements.tender_followed_profile')
            </div>
            <div id="tabCommentsHistory" class="container tab-pane fade"><br>
                <div class="col-sm-12">
                    <div class="row">
                        <table class="table table-bordered table-sm table-striped full-width">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
<div id="common_modal_form" class="modal fade bd-common-form" tabindex="-1" role="dialog" aria-labelledby="Hello" aria-hidden="true">
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
                    <button id="btn-save" type="button" class="btn btn-sm btn-primary">{{__('homepage.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer')
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
        </div>
        <div class="app-footer-right">
            <ul class="nav">
                <li class="nav-item">
                    @if(
                    $checklist->general_status === 'warning' || 
                    $checklist->general_status === 'not-finish' || 
                    $checklist->deed_status === 'warning' ||
                    $checklist->deed_status === 'not-finish' ||
                    $checklist->shareholder_status === 'warning' ||
                    $checklist->shareholder_status === 'not-finish' || 
                    $checklist->bodboc_status === 'warning' ||
                    $checklist->bodboc_status === 'not-finish' || 
                    $checklist->businesspermit_status === 'warning' ||
                    $checklist->businesspermit_status === 'not-finish' || 
                    $checklist->pic_status === 'warning' ||
                    $checklist->pic_status === 'not-finish' ||
                    $checklist->equipment_status === 'warning' ||
                    $checklist->equipment_status === 'not-finish' ||
                    $checklist->certification_status === 'warning' ||
                    $checklist->certification_status === 'not-finish' ||
                    $checklist->scopeofsupply_status === 'warning' ||
                    $checklist->scopeofsupply_status === 'not-finish' ||
                    $checklist->experience_status === 'warning' ||
                    $checklist->experience_status === 'not-finish' ||
                    $checklist->bankaccount_status === 'warning' ||
                    $checklist->bankaccount_status === 'not-finish' ||
                    $checklist->financial_status === 'warning' ||
                    $checklist->financial_status === 'not-finish' ||
                    $checklist->tax_status === 'warning' ||
                    $checklist->tax_status === 'not-finish')
                        <button class="btn btn-sm btn-secondary disabled" disabled="" style="padding-left: 0px;" title="{{ __('homepage.please_complete_data') }}"><i class="fas fa-paper-plane ml-2 mr-2" aria-hidden="true"></i>{{ __('homepage.send_submission') }}</button>
                    @else
                        @if($checklist->is_submitted)
                            <button class="btn btn-sm btn-secondary disabled" disabled="" style="padding-left: 0px;" title="{{ __('homepage.in_submission_process') }}"><i class="fas fa-exclamation-circle ml-2 mr-2" aria-hidden="true"></i>{{ __('homepage.in_submission_process') }}</button>
                        @else
                            @if(
                            $checklist->general_status === 'none' &&
                            $checklist->deed_status === 'none' &&
                            $checklist->shareholder_status === 'none' &&
                            $checklist->bodboc_status === 'none' &&
                            $checklist->businesspermit_status === 'none' &&
                            $checklist->pic_status === 'none' &&
                            $checklist->equipment_status === 'none' &&
                            $checklist->certification_status === 'none' &&
                            $checklist->scopeofsupply_status === 'none' &&
                            $checklist->experience_status === 'none' &&
                            $checklist->bankaccount_status === 'none' &&
                            $checklist->financial_status === 'none' &&
                            $checklist->tax_status === 'none')
                                <button class="btn btn-sm btn-secondary disabled" disabled="" style="padding-left: 0px;" title="{{ __('homepage.please_complete_data') }}"><i class="fas fa-paper-plane ml-2 mr-2" aria-hidden="true"></i>{{ __('homepage.send_submission') }}</button>
                            @else
                                @if($vendor->registration_status === 'vendor' && !$vendor->profile->is_blacklisted)
                                    <button data-id="{{ $vendor->profile->id }}" data-vendorid='{{ $vendor->id }}' data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="sendSubmission(this, 'vendor');" class="btn btn-sm btn-success" style="padding-left: 0px;"><i class="fas fa-paper-plane ml-2 mr-2" aria-hidden="true"></i>{{ __('homepage.send_update_submission') }}</button>
                                @else
                                    <button data-id="{{ $vendor->profile->id }}" data-vendorid='{{ $vendor->id }}' data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="sendSubmission(this, 'candidate');" class="btn btn-sm btn-success" style="padding-left: 0px;"><i class="fas fa-paper-plane ml-2 mr-2" aria-hidden="true"></i>{{ __('homepage.send_submission') }}</button>
                                @endif
                            @endif
                        @endif
                    @endif
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    function sendSubmission(obj, regStatus){
        let id = obj.dataset.id;
        let vendorID = obj.dataset.vendorid;
        $('#common_modal_form .modal-dialog').removeClass("modal-lg");
        $("#common_modal_form form").attr('id', 'frmsubmission');
        $("#common_modal_form .modal-title").html('<i class="fas fa-question-circle mr-2"></i>Send Submission Confirmation');
        $("#common_modal_form .modal-body").html(`Are you sure to send profile submission?`);
        if(regStatus === 'vendor'){
            $("#common_modal_form .modal-title").html('<i class="fas fa-question-circle mr-2"></i>Send Update Submission Confirmation');
            $("#common_modal_form .modal-body").html(`Are you sure to send update vendor data submission?`);
        }
        $('#btn-save').text('Confirm');
        $('#btn-save').off('click').on('click', async function () {
            let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
            $('#btn-save').text('Sending...');
            $('#btn-save').prepend(onProcessSpinner);
            $('#btn-save').prop("disabled", true);
            $('<input>').attr({
                type : 'hidden',
                id   : 'profileId',
                name : 'id',
                value: id
            }).prependTo('#common_modal_form form');
            $('<input>').attr({
                type : 'hidden',
                id   : 'vendorId',
                name : 'vendor_id',
                value: vendorID
            }).prependTo('#common_modal_form form');            
            $('<input>').attr({
                type : 'hidden',
                id   : 'registrationStatus',
                name : 'registration_status',
                value: regStatus
            }).prependTo('#common_modal_form form');            
            let frmData = $('#frmsubmission').serializeArray();
            let saveAction = new Promise((resolve, reject) =>{
                $.ajax({
                    type: 'POST',
                    url: `{{ route('profile.send-submission') }}`,
                    data: frmData
                }).done(function(resp) {
                    resolve(resp);                                 
                });
            });                
            let resSave = await saveAction;
            $('#btn-save').prop("disabled", false);
            if(resSave.success === true && typeof resSave.data.id !== 'undefined'){
                showAlert("The data has been submitted successfully!", "success", 3000);
                setTimeout(function(){
                    location.href = "{{ route('profile.show') }}";
                }, 1000);
            } else {
                showAlert("No data to submit!", "warning", 3000);
            }
            $('#common_modal_form .close').click();
        });
        return false;
    }
</script>
@endsection