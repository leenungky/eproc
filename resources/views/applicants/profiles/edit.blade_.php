@extends('layouts.app')

@include('layouts.navigation')

@section('content')
<div class="card card-menu">
    <div class="card-header">
        <div class="col-sm-12 full-width">
            <div class="row">
                <div class="heading-left">
                    <a href="{{ route('profile.show') }}" class="text-logo"><i class="fas fa-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{ Str::upper(__('homepage.show_profile')) }}</a>         
                    <a href="{{ route('profile.show') }}" class="ico-logo"><i class="fas fa-building icon-heading" aria-hidden="true"></i></a>         
                </div>
            </div>            
        </div>
    </div>
    <div class="card-body">
        <div class="accordion">
            <div class="card">
                <div class="card-header card-header-accordion" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            {{ __('homepage.administration_data') }}
                        </button>
                    </h5>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent=".accordion">
                    <div class="">
                        <ul class="vertical-nav-menu metismenu">
                            <li><a class="btn btn-link mm-active" href="{{ route('profile.edit-detail', 'general')}}">{{ __('homepage.general') }}</a></li>
                            <li><a class="btn btn-link" href="{{ route('profile.edit-detail', 'company-profile')}}">{{ __('homepage.company_profile') }}</a></li>
                            <li><a class="btn btn-link" href="{{ route('profile.edit-detail', 'certificates')}}">{{ __('homepage.akta_certificate') }}</a></li>
                            <li><a class="btn btn-link" href="{{ route('profile.edit-detail', 'shareholders')}}">{{ __('homepage.shareholders') }}</a></li>
                            <li><a class="btn btn-link" href="{{ route('profile.edit-detail', 'company-structure')}}">{{ __('homepage.company_management_structure') }}</a></li>
                            <li><a class="btn btn-link" href="{{ route('profile.edit-detail', 'business-license')}}">{{ __('homepage.business_license') }}</a></li>
                            <li><a class="btn btn-link" href="{{ route('profile.edit-detail', 'pic')}}">{{ __('homepage.person_in_charge') }} (PIC)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header card-header-accordion" id="headingTwo">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            {{ __('homepage.competency_and_workexperience') }}
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent=".accordion">
                    <div class="">
                        <ul class="vertical-nav-menu metismenu">
                            <li><a class="btn btn-link">Alat</a></li>
                            <li><a class="btn btn-link">Tenaga Ahli</a></li>
                            <li><a class="btn btn-link">Sertifikasi</a></li>
                            <li><a class="btn btn-link">Kompetensi</a></li>
                            <li><a class="btn btn-link">Data Pengalaman Kerja</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header card-header-accordion" id="headingThree">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            {{ __('homepage.finance_data') }}
                        </button>
                    </h5>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent=".accordion">
                    <div class="">
                        <ul class="vertical-nav-menu metismenu">
                            <li><a class="btn btn-link">Akun Bank</a></li>
                            <li><a class="btn btn-link">Laporan Keuangan</a></li>
                            <li><a class="btn btn-link">Dokumen Pajak</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card card-content">
    <div class="card-header">
        <div class="card-header-left">
            <span class="heading-title">{{ __('homepage.administration_data') }}: {{ __('homepage.general') }}</span>
        </div>
        <div class="card-header-right">
            <div class="button-group">
                <a class="btn btn-sm btn-primary" href="{{ route('profile.edit') }}">{{ __('homepage.finish') }}</a>
                <a class="btn btn-sm btn-success" href="{{ route('profile.edit') }}"><i class="fas fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;{{ __('homepage.create_new_entry') }}</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-sm table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>{{ __('homepage.detail') }}</th>
                    <th>{{ __('homepage.current_data') }}</th>
                    <th>{{ __('homepage.new_data') }}</th>
                    <th>{{ __('homepage.status') }}</th>
                    <th>{{ __('homepage.action') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="12">1</td>
                    <td>{{ __('homepage.applicant_name') }}</td>
                    <td>{{ $applicant->partner_name }}</td>
                    <td></td>
                    <td>Prepared</td>
                    <td rowspan="12"></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.location_category') }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.country') }}</td>
                    <td>{{ $applicant->country }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.province') }}</td>
                    <td>{{ $applicant->province }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.city') }}</td>
                    <td>{{ $applicant->city }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.sub_district') }}</td>
                    <td>{{ $applicant->sub_district }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.postal_code') }}</td>
                    <td>{{ $applicant->postal_code }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.address') }}</td>
                    <td>{{ $applicant->address_1 }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.phone_number') }}</td>
                    <td>{{ $applicant->phone_number }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.fax_number') }}</td>
                    <td>{{ $applicant->fax_number }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.website') }}</td>
                    <td>{{ $applicant->company_site }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{ __('homepage.company_email') }}</td>
                    <td>{{ $applicant->company_email }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
</script>
@endsection