@extends('layouts.app')

@include('layouts.navigation')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{route('admin.applicants')}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ __('homepage.applicant') }}</a>
    </div>
    <div class="card-body">
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
        <form  method="post" action="{{ url('applicants/approval') }}" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            <div class="row">
            <table>
            <tr>
                <td width="45%" valign="top">
                    <table class="table table-borderless table-ellipsis">
                        <tbody>
                            <tr>
                                <th class="th-label" style="width:35%">{{ __('homepage.applicant_name') }}</th>
                                <td class="td-value">{{ $applicant->vendor_name }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.president_director') }}</th>
                                <td class="td-value">{{ $applicant->president_director }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.vendor_group') }}</th>
                                <td class="td-value">{{ __('homepage.'.$applicant->vendor_group) }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.company_type') }}</th>
                                <td class="td-value">{{ $applicant->company_type }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.purchasing_organization') }}</th>
                                <td class="td-value">{{ $applicant->org_code }} ({{ $applicant->description }})</td>
                            </tr>
                            @if($applicant->identification_type == 'tin')
                            <tr>
                                <th class="th-label">{{ __('homepage.tax_identification_number') }}</th>
                                <td class="td-value">{{ $applicant->tin_number }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.tax_document_attachment') }}</th>
                                <td class="td-value"><a href="{{$storage}}/{{$applicant->id}}/{{$applicant->tin_attachment}}" target="_blank">{{ $applicant->tin_attachment }}</a></td>
                            </tr>
                            @elseif($applicant->identification_type == 'id-card')
                            <tr>
                                <th class="th-label">{{ __('homepage.id_card_number') }}</th>
                                <td class="td-value">{{ $applicant->idcard_number }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.id_card_document_attachment') }}</th>
                                <td class="td-value"><a href="{{$storage}}/{{$applicant->id}}/{{$applicant->idcard_attachment}}" target="_blank">{{ $applicant->idcard_attachment }}</a></td>
                            </tr>
                            @endif
                            @if($applicant->vendor_group == 'foreign')
                            <tr>
                                <th class="th-label">{{ __('homepage.tax_identification_number') }}</th>
                                <td class="td-value">{{ $applicant->tin_number }}</td>
                            </tr>
                            @endif
                            @if($applicant->pkp_type == 'pkp')
                            <tr>
                                <th class="th-label">{{ __('homepage.pkp_type') }}</th>
                                <td class="td-value">{{ $applicant->pkp_type }}</td>
                            </tr>                            
                            <tr>
                                <th class="th-label">{{ __('homepage.pkp_number') }}</th>
                                <td class="td-value">{{ $applicant->pkp_number }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.pkp_attachment_file') }}</th>
                                <td class="td-value"><a href="{{$storage}}/{{$applicant->id}}/{{$applicant->pkp_attachment}}" target="_blank">{{ $applicant->pkp_attachment }}</a></td>
                            </tr>
                            @elseif($applicant->pkp_type == 'non-pkp')
                            <tr>
                                <th class="th-label">{{ __('homepage.pkp_type') }}</th>
                                <td class="td-value">{{ $applicant->pkp_type }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.non_pkp_number') }}</th>
                                <td class="td-value">{{ $applicant->non_pkp_number }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.tender_ref_number') }}</th>
                                <td class="td-value">{{ $applicant->tender_ref_number }}</td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.pic_full_name') }}</th>
                                <td class="td-value">{{ $applicant->pic_full_name }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.pic_mobile_number') }}</th>
                                <td class="td-value">{{ $applicant->pic_mobile_number }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.pic_email') }}</th>
                                <td class="td-value">{{ $applicant->pic_email }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td width="55%">
                    <table class="table table-borderless table-ellipsis">
                        <tbody>
                            @if($applicant->vendor_group == 'local')
                                <tr>
                                    <th class="th-label" style="width:35%">{{ __('homepage.street') }}</th>
                                    <td class="td-value">{{ $applicant->street }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.house_number') }}</th>
                                    <td class="td-value">{{ $applicant->house_number }}</td>
                                </tr>                                
                                <tr>
                                    <th class="th-label">{{ __('homepage.building_name') }}</th>
                                    <td class="td-value">{{ $applicant->building_name }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.kavling_floor_number') }}</th>
                                    <td class="td-value">{{ $applicant->kavling_floor_number }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.rt') }}</th>
                                    <td class="td-value">{{ $applicant->rt }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.rw') }}</th>
                                    <td class="td-value">{{ $applicant->rw }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.village') }}</th>
                                    <td class="td-value">{{ $applicant->village }}</td>
                                </tr>
                            @else
                                <tr>
                                    <th class="th-label">{{ __('homepage.address_1') }}</th>
                                    <td class="td-value">{{ $applicant->address_1 }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.address_2') }}</th>
                                    <td class="td-value">{{ $applicant->address_2 }}</td>
                                </tr>
                                <tr>
                                    <th class="th-label">{{ __('homepage.address_3') }}</th>
                                    <td class="td-value">{{ $applicant->address_3 }}</td>
                                </tr>
                            @endif                            
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.phone_number') }}</th>
                                <td class="td-value">{{ $applicant->phone_number }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.fax_number') }}</th>
                                <td class="td-value">{{ $applicant->fax_number }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.company_email') }}</th>
                                <td class="td-value">{{ $applicant->company_email }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.website') }}</th>
                                <td class="td-value">{{ $applicant->company_site }}</td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.country') }}</th>
                                <td class="td-value">{{ $applicant->country }}</td>
                            </tr>
                            @if($applicant->vendor_group == 'local')
                            <tr>
                                <th class="th-label">{{ __('homepage.province') }}</th>
                                <td class="td-value">{{ $applicant->province }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.city') }}</th>
                                <td class="td-value">{{ $applicant->city }}</td>
                            </tr>
                            <tr>
                                <th class="th-label">{{ __('homepage.sub_district') }}</th>
                                <td class="td-value">{{ $applicant->sub_district }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th class="th-label">{{ __('homepage.postal_code') }}</th>
                                <td class="td-value">{{ $applicant->postal_code }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </table>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <fieldset class="fieldset-border" style="width: 100%;">
                        <legend class="legend-border"><i class="fas fas-times-circle-o"></i>{{ __('homepage.comments_history') }}</legend>
                        <div class="control-group">
                            <table class="table table-bordered table-sm table-striped table-ellipsis">
                                <thead>
                                    <tr>
                                        <th style="width:100px">{{__('homepage.userid')}}</th>
                                        <th style="width:300px">{{__('homepage.name')}}</th>
                                        <th style="width:100px">{{__('homepage.activity')}}</th>
                                        <th style="width:100px">{{__('homepage.start_date')}}</th>
                                        <th style="width:100px">{{__('homepage.finish_date')}}</th>
                                        <th style="width:100px">{{__('homepage.comment')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listworkflow as $workflow)
                                        <tr>
                                            <td>{{ $workflow->created_by }}</td>
                                            <td>{{ $applicant->vendor_name }}</td>
                                            <td>{{ $workflow->activity }}</td>
                                            <td>{{ $workflow->started_at }}</td>
                                            <td>{{ $workflow->finished_at }}</td>
                                            <td>{{ $workflow->remarks }}</td>
                                        </tr>
                                    @endforeach
                                    @if(count($listworkflow) === 0)
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
            @can('applicant_approval')
            @if($approvalstatus && $canApprove)
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-warning text-center">                        
                        <strong><i>{{ __('homepage.currently_waiting_registration_approval') }} ({{ __('homepage.since') }} {{ $approvalstatus->started_at }})</i></strong>
                    </div>
                </div>
            </div>
            <div class="row"><hr /></div>
            <div class="btn-group" style="float: right;">
                <button type="button" class="btn mr-2 mb-2 btn-danger" id="btn-reject" 
                        data-key="reject" 
                        data-id="{{ $applicant->id }}" 
                        data-vendorname="{{ $applicant->vendor_name }}" 
                        data-companytype="{{ $applicant->company_type }}" 
                        data-companyemail="{{ $applicant->company_email }}" 
                        data-picemail="{{ $applicant->pic_email }}" 
                        data-npwp="{{ $applicant->npwp_tin_number }}" 
                        onclick="confirm(this);"><i class="fas fa-times"></i>&nbsp;&nbsp;{{ __('homepage.reject') }}</button>
                <button type="button" class="btn mr-2 mb-2 btn-success" id="btn-approve" 
                        data-key="approve" 
                        data-id="{{ $applicant->id }}" 
                        data-vendorname="{{ $applicant->vendor_name }}" 
                        data-companytype="{{ $applicant->company_type }}" 
                        data-companyemail="{{ $applicant->company_email }}" 
                        data-picemail="{{ $applicant->pic_email }}" 
                        data-npwp="{{ $applicant->npwp_tin_number }}" 
                        onclick="confirm(this);"><i class="fas fa-check"></i>&nbsp;&nbsp;{{ __('homepage.approve') }}</button>
            </div>
            @endif
            @endcan
        </form>
    </div>
</div>

@endsection
@section('scripts')
@can('applicant_approval')
@if($approvalstatus && $canApprove)
<script type="text/javascript">
    require(['bootstrap']);
    function confirm(obj) {
        let key = obj.dataset.key,
                refId = obj.dataset.id,
                title = ``,
                statusId = ``,
                status = ``,
                vendorname = obj.dataset.vendorname,
                companytype = obj.dataset.companytype,
                picemail = obj.dataset.picemail,
                npwp = obj.dataset.npwp,
                companyemail = obj.dataset.companyemail;
        $('#btn-reject').prop("disabled", true);
        $('#btn-approve').prop("disabled", true);
        $('body').append(`@include('applicants.modal-verification')`);
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
        $('.was-validated').removeClass('was-validated').addClass('needs-validation');
        $('#modalVerificationLabel').html(title);
        $('input[name=company_name]').val(vendorname);
        $('input[name=company_type]').val(companytype);
        $('input[name=company_email]').val(companyemail);
        $('input[name=pic_email]').val(picemail);
        $('input[name=npwp]').val(npwp);
        $('input[name=vendor_id]').val(refId);
        $('input[name=status]').val(status);

        $('#modalVerification').modal('toggle');
        $('#btn-reject').prop("disabled", false);
        $('#btn-approve').prop("disabled", false);
        $('#btn-submit-approval').click(function(e){
            $('.needs-validation').removeClass('needs-validation').addClass('was-validated');
        });
        
        $("form").submit(function(){
            let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
            $('#btn-submit-approval').attr("disabled", true);
            $('#btn-submit-approval').text('Submitting...');
            $('#btn-submit-approval').prepend(onProcessSpinner);
        });
    }
</script>
@endif
@endcan
@endsection