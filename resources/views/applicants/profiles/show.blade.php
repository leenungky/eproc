@extends('layouts.app')

@include('layouts.navigation')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-header-left">
            <span class="heading-title">{{ __('homepage.company_profile') }}</span>
        </div>
        <div class="card-header-right">
            <a class="btn btn-sm btn-primary" href="{{ route('profile.edit-detail', 'general') }}"><i class="fas fa-edit mr-2" aria-hidden="true"></i>{{ __('homepage.change_profile') }}</a>
        </div>
    </div>
    <div class="card-body">
        <div class="col-sm-12 mt-lg-3 mb-lg-3">
            <h3>{{ $applicant->partner_name }}</h3>
            <p>{{ __('homepage.register_number') }} : {{ $applicant->register_number }}</p>
        </div>
        <br>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
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
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group-sm row">
                                    <label for="partnerName" class="col-form-label col-sm-5 lbl-right lbl-field-info">{{ __('homepage.applicant_name') }}&nbsp;:</label>
                                    <div class="col-sm-7 div-sm-info">
                                        <p class="field-info">{{ $applicant->partner_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group-sm row">
                                    <label for="companyType" class="col-form-label col-sm-5 lbl-right lbl-field-info">{{ __('homepage.company_type') }}&nbsp;:</label>
                                    <div class="col-sm-7 div-sm-info">
                                        <p class="field-info">{{ $applicant->company_type }}</p>
                                    </div>
                                </div>                        
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group-sm row">
                                    <label for="companyCategory" class="col-form-label col-sm-5 lbl-right lbl-field-info">{{ __('homepage.company_category') }}&nbsp;:</label>
                                    <div class="col-sm-7 div-sm-info">
                                        <p class="field-info">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group-sm row">
                                    <label for="companyStatus" class="col-form-label col-sm-5 lbl-right lbl-field-info">{{ __('homepage.company_status') }}&nbsp;:</label>
                                    <div class="col-sm-7 div-sm-info">
                                        <p class="field-info">{{ __('homepage.active') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group-sm row">
                                    <label for="partnerName" class="col-form-label col-sm-5 lbl-right lbl-field-info">{{ __('homepage.active_skl_no') }}&nbsp;:</label>
                                    <div class="col-sm-7 div-sm-info">
                                        <p class="field-info"><a href="javascript:void(0);">{{ __('homepage.print') }}&nbsp;<i class="fas fa-print"></i></a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group-sm row">
                                    <label for="" class="col-form-label col-sm-5 lbl-right lbl-field-info">&nbsp;</label>
                                    <div class="col-sm-7 div-sm-info">
                                        <p class="field-info">&nbsp;</p>
                                    </div>
                                </div>                        
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group-sm row">
                                    <label for="" class="col-form-label col-sm-5 lbl-right lbl-field-info">&nbsp;</label>
                                    <div class="col-sm-7 div-sm-info">
                                        <p class="field-info">&nbsp;</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group-sm row">
                                    <label for="companyWarning" class="col-form-label col-sm-5 lbl-right lbl-field-info">{{ __('homepage.company_warning') }}&nbsp;:</label>
                                    <div class="col-sm-7 div-sm-info">
                                        <p class="field-info"><span class="badge badge-pill badge-success">No. Warning</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tabDocStatusExpired" class="container tab-pane fade"><br>
                Document Status Expired List
            </div>
            <div id="tabTenderFollowed" class="container tab-pane fade"><br>
                Tender List
            </div>
            <div id="tabCommentsHistory" class="container tab-pane fade"><br>
                Comments History List
            </div>
        </div>
        <div><hr /></div>
        <div class="col-sm-12 row">            
            <div class="col">
                <ul class="">{{ __('homepage.administration_data') }}
                    <li><a href="{{ route('profile.edit-detail', 'general') }}">{{ __('homepage.general') }}@if($checklist->general_has_finish > 0 and $checklist->general_not_finish == 0) <i class="fa fa-check font-success ml-2"></i> @endif</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'deeds') }}">{{ __('homepage.deed') }}@if($checklist->deed_has_finish ?? 1 > 0 and $checklist->deed_not_finish ?? 1 == 0) <i class="fa fa-check font-success ml-2"></i> @endif</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'shareholders') }}">{{ __('homepage.shareholders') }}@if($checklist->shareholder_has_finish ?? 1 > 0 and $checklist->shareholder_not_finish ?? 1  == 0) <i class="fa fa-check font-success ml-2"></i> @endif</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'bod_boc') }}">{{ __('homepage.bod_boc') }}@if($checklist->bodboc_has_finish ?? 1 > 0 and $checklist->bodboc_not_finish ?? 1 == 0) <i class="fa fa-check font-success ml-2"></i> @endif</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'business-license') }}">{{ __('homepage.business_license') }}</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'pic') }}">{{ __('homepage.person_in_charge') }} (PIC)</a></li>
                </ul>
            </div>
            <div class="col">
                <ul class="">{{ __('homepage.competency_and_workexperience') }}
                    <li><a href="{{ route('profile.edit-detail', 'tools') }}">{{ __('homepage.tools') }}</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'experts') }}">{{ __('homepage.experts') }}</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'certifications') }}">{{ __('homepage.certifications') }}</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'competency') }}">{{ __('homepage.competency') }}</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'experience') }}">{{ __('homepage.experience') }}</a></li>
                </ul>
            </div>
            <div class="col">
                <ul class="">{{ __('homepage.finance_data') }}
                    <li><a href="{{ route('profile.edit-detail', 'bank-account') }}">{{ __('homepage.bank_account') }}</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'financial') }}">{{ __('homepage.financial_statements') }}</a></li>
                    <li><a href="{{ route('profile.edit-detail', 'tax-document') }}">{{ __('homepage.tax_documents') }}</a></li>
                </ul>
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
                    <button data-id="{{ $applicant->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="sendSubmission(this);" class="btn btn-sm btn-success" style="padding-left: 0px;"><i class="fas fa-paper-plane ml-2 mr-2" aria-hidden="true"></i>{{ __('homepage.send_submission') }}</button>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@extends('layouts.footer')

@section('scripts')
<script type="text/javascript">
    function sendSubmission(obj){
        let id = obj.dataset.id;
        $('#common_modal_form .modal-dialog').removeClass("modal-lg");
        $("#common_modal_form form").attr('id', 'frmsubmission');
        $("#common_modal_form .modal-title").html('<i class="fas fa-question-circle mr-2"></i>Send Submission Confirmation');
        $("#common_modal_form .modal-body").html(`Are you sure to send profile submission?`);
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
                showAlert("The data has been reverted successfully!", "success", 3000);
                setTimeout(function(){
                    location.href = "{{ route('profile.show') }}";
                }, 1000);
            } else {
                showAlert("No data to submit!", "warning", 3000);
                setTimeout(function(){
                    location.href = "{{ route('profile.show') }}";
                }, 1000);
            }
            $('#common_modal_form .close').click();
        });
        return false;
    }
</script>
@endsection