@extends('layouts.two_column')

@include('applicants.profiles.accordion_menu')

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
    <span class="heading-title">{{ __('homepage.administration_data') }}: {{ __('homepage.general') }}</span>              
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
        <button id="btn_create_new_general" class="btn btn-sm btn-success" data-toggle="modal" data-target="#frmgeneral_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{ __('homepage.create_new_entry') }}</button>
    </div>                        
</div>
@endsection

@section('contentbody')
<div class="col-sm-12 mt-lg-2 mb-lg-3">
    <div class="form-group-sm row ml-5 mr-5">
        <label for="partnerName" class="col-form-label lbl-right lbl-field-info">{{ __('homepage.company_name') }}&nbsp;:</label>
        <div class="div-sm-info">
            <p class="field-info">{{ $applicant->partner_name }}</p>
        </div>
    </div>
</div>     
<table class="table table-sm table-striped table-bordered">
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th style="width: 150px;">{{ __('homepage.detail') }}</th>
            <th style="width: 214px;">{{ __('homepage.current_data') }}</th>
            <th style="width: 214px;">{{ __('homepage.new_data') }}</th>
            <th style="width: 80px;">{{ __('homepage.status') }}</th>
            <th style="width: 40px;">{{ __('homepage.action') }}</th>
        </tr>
    </thead>
    <tbody>
        @if( $profiles->count() > 0 )
            <?php $i = 1; ?>
            @foreach ($profiles as $profile)
                @if($profile->is_current_data)
                    <!-- Check has an edited data or not -->
                    @foreach ($profiles as $newdata)
                        @if($profile->id == $newdata->parent_id)
                            <tr>
                                <td rowspan="13" class="text-center">{{ $i }}</td>
                                <td>{{ __('homepage.company_name') }}</td>
                                <td>{{ $profile->company_name }}</td>
                                <td>{{ $newdata->company_name }}</td>
                                <td class="text-center">Prepared</td>
                                <td rowspan="13" class="text-center">
                                    <button data-id="{{ $newdata->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="revertEditedData(this);" class="btn btn-sm btn-link" tabindex="Undo Data Edited"><i class="fas fa-undo" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.company_type') }}</td>
                                <td>{{ $profile->company_type }}</td>
                                <td>{{ $newdata->company_type }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.location_category') }}</td>
                                <td>{{ $profile->location_category }}</td>
                                <td>{{ $newdata->location_category }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.country') }}</td>
                                <td>{{ $profile->country }}</td>
                                <td>{{ $newdata->country }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.province') }}</td>
                                <td>{{ $profile->province }}</td>
                                <td>{{ $newdata->province }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.city') }}</td>
                                <td>{{ $profile->city }}</td>
                                <td>{{ $newdata->city }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.sub_district') }}</td>
                                <td>{{ $profile->sub_district }}</td>
                                <td>{{ $newdata->sub_district }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.postal_code') }}</td>
                                <td>{{ $profile->postal_code }}</td>
                                <td>{{ $newdata->postal_code }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.address') }}</td>
                                <td>{{ $profile->address }}</td>
                                <td>{{ $newdata->address }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.phone_number') }}</td>
                                <td>{{ $profile->phone_number }}</td>
                                <td>{{ $newdata->phone_number }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.fax_number') }}</td>
                                <td>{{ $profile->fax_number }}</td>
                                <td>{{ $newdata->fax_number }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.website') }}</td>
                                <td>{{ $profile->website }}</td>
                                <td>{{ $newdata->website }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.company_email') }}</td>
                                <td>{{ $profile->company_email }}</td>
                                <td>{{ $newdata->company_email }}</td>
                                <td></td>
                            </tr>
                            <tr style="background-color: #d8d5d5;" class="row{{ $profile->id }}">
                                <td colspan="4" style="padding: 2px;"></td>
                            </tr>
                            <?php $i++; ?>
                            @break;
                        @else
                            @if ($loop->last)
                                <tr>
                                    <td rowspan="13" class="text-center">{{ $i }}</td>
                                    <td>{{ __('homepage.company_name') }}</td>
                                    <td>{{ $profile->company_name }}</td>
                                    <td></td>
                                    <td class="text-center">Prepared</td>
                                    <td rowspan="13" class="text-center">
                                        <button data-id="{{ $profile->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="editCurrentData(this);" class="btn btn-sm btn-link"><i class="fas fa-edit" aria-hidden="true"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.company_type') }}</td>
                                    <td>{{ $profile->company_type }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.location_category') }}</td>
                                    <td>{{ $profile->location_category }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.country') }}</td>
                                    <td>{{ $profile->country }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.province') }}</td>
                                    <td>{{ $profile->province }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.city') }}</td>
                                    <td>{{ $profile->city }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.sub_district') }}</td>
                                    <td>{{ $profile->sub_district }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.postal_code') }}</td>
                                    <td>{{ $profile->postal_code }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.address') }}</td>
                                    <td>{{ $profile->address }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.phone_number') }}</td>
                                    <td>{{ $profile->phone_number }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.fax_number') }}</td>
                                    <td>{{ $profile->fax_number }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.website') }}</td>
                                    <td>{{ $profile->website }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.company_email') }}</td>
                                    <td>{{ $profile->company_email }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="background-color: #d8d5d5;" class="row{{ $profile->id }}">
                                    <td colspan="4" style="padding: 2px;"></td>
                                </tr>
                                <?php $i++; ?>
                            @endif
                        @endif
                    @endforeach
                @else
                    @if($profile->parent_id == 0)
                        <tr>
                            <td rowspan="13" class="text-center">{{ $i }}</td>
                            <td>{{ __('homepage.company_name') }}</td>
                            <td></td>
                            <td>{{ $profile->company_name }}</td>
                            <td class="text-center">Prepared</td>
                            <td rowspan="13" class="text-center">
                                <div class="button-group">
                                    <button data-id="{{ $profile->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="editAddedData(this);" class="btn btn-sm btn-link" style="padding-left: 0px;"><i class="fas fa-edit" aria-hidden="true"></i></button>                                
                                    <button data-id="{{ $profile->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="deleteAddedData(this);" class="btn btn-sm btn-link" style="padding: 0px;"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>                                
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.company_type') }}</td>
                            <td></td>
                            <td>{{ $profile->company_type }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.location_category') }}</td>
                            <td></td>
                            <td>{{ $profile->location_category }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.country') }}</td>
                            <td></td>
                            <td>{{ $profile->country }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.province') }}</td>
                            <td></td>
                            <td>{{ $profile->province }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.city') }}</td>
                            <td></td>
                            <td>{{ $profile->city }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.sub_district') }}</td>
                            <td></td>
                            <td>{{ $profile->sub_district }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.postal_code') }}</td>
                            <td></td>
                            <td>{{ $profile->postal_code }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.address') }}</td>
                            <td></td>
                            <td>{{ $profile->address }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.phone_number') }}</td>
                            <td></td>
                            <td>{{ $profile->phone_number }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.fax_number') }}</td>
                            <td></td>
                            <td>{{ $profile->fax_number }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.website') }}</td>
                            <td></td>
                            <td>{{ $profile->website }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.company_email') }}</td>
                            <td></td>
                            <td>{{ $profile->company_email }}</td>
                            <td></td>
                        </tr>
                        <tr style="background-color: #d8d5d5;" class="row{{ $profile->id }}">
                            <td colspan="4" style="padding: 2px;"></td>
                        </tr>
                        <?php $i++; ?>
                    @endif
                @endif        
            @endforeach        
        @else
            <tr>
                <td rowspan="13" class="text-center">1</td>
                <td>{{ __('homepage.company_name') }}</td>
                <td></td>
                <td></td>
                <td class="text-center">Prepared</td>
                <td rowspan="13" class="text-center">
                </td>
            </tr>
            <tr>
                <td>{{ __('homepage.company_type') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.location_category') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.country') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.province') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.city') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.sub_district') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.postal_code') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.address') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.phone_number') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.fax_number') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.website') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.company_email') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr style="background-color: #d8d5d5;">
                <td colspan="4" style="padding: 2px;"></td>
            </tr>
        @endif
    </tbody>
</table>
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
<div id="common_modal_info" class="modal fade bd-common-info" tabindex="-1" role="dialog" aria-labelledby="Hello" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{__('homepage.cancel')}}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content Here -->
            </div>
            <div class="modal-footer">
                <button id="btn-cancel" type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{__('homepage.cancel')}}</button>
                <button id="btn-save" type="button" class="btn btn-sm btn-primary">{{__('homepage.save')}}</button>
            </div>
        </div>
    </div>
</div>
<?php
$modal1 = [
    'title' => __("homepage.create_new_entry"),
    'contents' => '',
    'form_layout' => 'applicants.profiles.form.form_general',
    'form_name' => 'frmgeneral',
        ]
?>
@include('layouts.modal_common',$modal1)
<?php
$modal2 = [
    'title' => __("homepage.form_edit_data"),
    'contents' => '',
    'form_layout' => 'applicants.profiles.form.form_general',
    'form_name' => 'frmedit',
    'data_target' => 'bd-form-edit'
]
?>
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
<script type="text/javascript">
    require(["jquery", "bootstrap", "bootstrap-util", "metisMenu"], function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#frmgeneral-save').click(function () {
            let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
            $('#frmgeneral_modal #frmgeneral-save').text('Saving...');
            $('#frmgeneral_modal #frmgeneral-save').prepend(onProcessSpinner);
            let frmData = $('#frmgeneral').serializeArray();
            //frmData.push({name: 'items', value: JSON.stringify(selectedData)});
            $('#frmgeneral_fieldset').attr("disabled", true);
            $.ajax({
                url: "{{ route('profile.create-general') }}",
                type: 'POST',
                data: frmData
            }).done(function (response, textStatus, jqXhr) {
                // console.log(response);
                if (response.success) {
                    $('#frmgeneral_modal .close').click();
                    showAlert("New entry data has been saved successfully!", "success", 3000);
                    setTimeout(function(){
                        location.href = "{{ route('profile.edit') }}/general";
                    }, 1000);
                } else {
                    showAlert("Draft Tender message saved.", "danger", 3000);
                    $('#frmgeneral_fieldset').attr("disabled", false);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // console.log("The following error occured: " + textStatus, errorThrown);
                $('#frmgeneral_fieldset').attr("disabled", false);
            });
        });
    });
    
    async function editCurrentData(obj){        
        let id = obj.dataset.id;
        let getData = new Promise((resolve, reject) => {
            $.ajax({
                "type": "GET",
                "url": "{{ route('profile.find-general-data') }}",
                "data": {
                    "_token": "{{ csrf_token() }}",
                    "id"    : id
                }
            }).done(function (resp) {
                resolve(resp);
            });
        });
        let result = await getData; // wait until the promise resolves (*)
        if(typeof result.id !== 'undefined'){
            $("#common_modal_form form").attr('id', 'frmedit');
            $("#common_modal_form .modal-title").html(`{{ __('homepage.form_edit_data') }}`);
            $("#common_modal_form .modal-body").html(`@include('applicants.profiles.form.form_general')`);
            $("#inputCompanyName").val(result.company_name);
            $("#selCompanyType").val(result.company_type_id);
            $("#inputLocationCategory").val(result.location_category);
            $("#inputAddress").val(result.address);
            $("#selCountry").val(result.country);
            $("#selProvince").val(result.province);
            $("#selCity").val(result.city);
            $("#selSubdistrict").val(result.sub_district);
            $("#inputPostalCode").val(result.postal_code);
            $("#inputPhoneNumber").val(result.phone_number);
            $("#inputFaxNumber").val(result.fax_number);
            $("#inputWebsite").val(result.website);
            $("#inputCompanyEmail").val(result.company_email);
            
            $('#btn-save').off('click').on('click', async function () {
                let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
                $('#btn-save').text('Saving...');
                $('#btn-save').prepend(onProcessSpinner);
                $('#btn-save').prop("disabled", true);
                $('<input>').attr({
                    type : 'hidden',
                    id   : 'profileId',
                    name : 'id',
                    value: result.id
                }).prependTo('#common_modal_form form');
                $('<input>').attr({
                    type : 'hidden',
                    id   : 'editType',
                    name : 'edit_type',
                    value: 'current'
                }).prependTo('#common_modal_form form');
                let frmData = $('#frmedit').serializeArray();
                let saveAction = new Promise((resolve, reject) =>{
                    $.ajax({
                        type: 'POST',
                        url: `{{ route('profile.update-general') }}`,
                        data: frmData
                    }).done(function(resp) {
                        resolve(resp);                                 
                    });
                });                
                let resSave = await saveAction;
                $('#btn-save').prop("disabled", false);
                if(resSave.success === true && typeof resSave.data.id !== 'undefined'){
                    showAlert("The data has been updated successfully!", "success", 3000);
                    setTimeout(function(){
                        location.href = "{{ route('profile.edit') }}/general";
                    }, 1000);
                } else {
                    showAlert("No data to update!", "warning", 3000);
                }
                $('#common_modal_form .close').click();
            });
        } else {
            $("#btn-save").hide();
        }
        
    }
    
    async function editAddedData(obj){        
        let id = obj.dataset.id;
        let getData = new Promise((resolve, reject) => {
            $.ajax({
                "type": "GET",
                "url": "{{ route('profile.find-general-data') }}",
                "data": {
                    "_token": "{{ csrf_token() }}",
                    "id"    : id
                }
            }).done(function (resp) {
                resolve(resp);
            });
        });
        let result = await getData; // wait until the promise resolves (*)
        if(typeof result.id !== 'undefined'){
            $("#common_modal_form form").attr('id', 'frmedit');
            $("#common_modal_form .modal-title").html(`{{ __('homepage.form_edit_data') }}`);
            $("#common_modal_form .modal-body").html(`@include('applicants.profiles.form.form_general')`);
            $("#inputCompanyName").val(result.company_name);
            $("#selCompanyType").val(result.company_type_id);
            $("#inputLocationCategory").val(result.location_category);
            $("#inputAddress").val(result.address);
            $("#selCountry").val(result.country);
            $("#selProvince").val(result.province);
            $("#selCity").val(result.city);
            $("#selSubdistrict").val(result.sub_district);
            $("#inputPostalCode").val(result.postal_code);
            $("#inputPhoneNumber").val(result.phone_number);
            $("#inputFaxNumber").val(result.fax_number);
            $("#inputWebsite").val(result.website);
            $("#inputCompanyEmail").val(result.company_email);
            
            $('#btn-save').off('click').on('click', async function () {
                let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
                $('#btn-save').text('Saving...');
                $('#btn-save').prepend(onProcessSpinner);
                $('#btn-save').prop("disabled", true);
                $('<input>').attr({
                    type : 'hidden',
                    id   : 'profileId',
                    name : 'id',
                    value: result.id
                }).prependTo('#common_modal_form form');
                $('<input>').attr({
                    type : 'hidden',
                    id   : 'editType',
                    name : 'edit_type',
                    value: 'added'
                }).prependTo('#common_modal_form form');
                let frmData = $('#frmedit').serializeArray();
                let saveAction = new Promise((resolve, reject) =>{
                    $.ajax({
                        type: 'POST',
                        url: `{{ route('profile.update-general') }}`,
                        data: frmData
                    }).done(function(resp) {
                        resolve(resp);                                 
                    });
                });                
                let resSave = await saveAction;
                $('#btn-save').prop("disabled", false);
                if(resSave.success === true && typeof resSave.data.id !== 'undefined'){
                    showAlert("The data has been updated successfully!", "success", 3000);
                    setTimeout(function(){
                        location.href = "{{ route('profile.edit') }}/general";
                    }, 1000);
                } else {
                    showAlert("No data to update!", "warning", 3000);
                }
                $('#common_modal_form .close').click();
            });
        } else {
            $("#btn-save").hide();
        }
        
    }
    
    function deleteAddedData(obj){
        let id = obj.dataset.id;
        $('#common_modal_form .modal-dialog').removeClass("modal-lg");
        $("#common_modal_form form").attr('id', 'frmedit');
        $("#common_modal_form .modal-title").html('<i class="fas fa-question-circle mr-2"></i>Delete Data Confirmation');
        $("#common_modal_form .modal-body").html(`Are you sure to delete data added?`);
        $('#btn-save').text('Confirm');
        $('#btn-save').off('click').on('click', async function () {
            let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
            $('#btn-save').text('Deleting...');
            $('#btn-save').prepend(onProcessSpinner);
            $('#btn-save').prop("disabled", true);
            $('<input>').attr({
                type : 'hidden',
                id   : 'profileId',
                name : 'id',
                value: id
            }).prependTo('#common_modal_form form');
            let frmData = $('#frmedit').serializeArray();
            let saveAction = new Promise((resolve, reject) =>{
                $.ajax({
                    type: 'DELETE',
                    url: `{{ route('profile.revert-general') }}`,
                    data: frmData
                }).done(function(resp) {
                    resolve(resp);                                 
                });
            });                
            let resSave = await saveAction;
            $('#btn-save').prop("disabled", false);
            if(resSave.success === true && typeof resSave.data.id !== 'undefined'){
                showAlert("The data has been deleted successfully!", "success", 3000);
                setTimeout(function(){
                    location.href = "{{ route('profile.edit') }}/general";
                }, 1000);
            } else {
                showAlert("No data to update!", "warning", 3000);
            }
            $('#common_modal_form .close').click();
        });
        return false;
    }
    
    function revertEditedData(obj){
        let id = obj.dataset.id;
        $('#common_modal_form .modal-dialog').removeClass("modal-lg");
        $("#common_modal_form form").attr('id', 'frmedit');
        $("#common_modal_form .modal-title").html('<i class="fas fa-question-circle mr-2"></i>Revert Data Confirmation');
        $("#common_modal_form .modal-body").html(`Are you sure to revert data changes?`);
        $('#btn-save').text('Confirm');
        $('#btn-save').off('click').on('click', async function () {
            let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
            $('#btn-save').text('Reverting...');
            $('#btn-save').prepend(onProcessSpinner);
            $('#btn-save').prop("disabled", true);
            $('<input>').attr({
                type : 'hidden',
                id   : 'profileId',
                name : 'id',
                value: id
            }).prependTo('#common_modal_form form');
            let frmData = $('#frmedit').serializeArray();
            let saveAction = new Promise((resolve, reject) =>{
                $.ajax({
                    type: 'DELETE',
                    url: `{{ route('profile.revert-general') }}`,
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
                    location.href = "{{ route('profile.edit') }}/general";
                }, 1000);
            } else {
                showAlert("No data to update!", "warning", 3000);
            }
            $('#common_modal_form .close').click();
        });
        return false;
    }
    
    function deleteData(obj){
        let id = obj.dataset.id;
        $('#common_modal_form .modal-dialog').removeClass("modal-lg");
        $("#common_modal_form form").attr('id', 'frmedit');
        $("#common_modal_form .modal-title").html('<i class="fas fa-question-circle mr-2"></i>Revert Data Confirmation');
        $("#common_modal_form .modal-body").html(`Are you sure to revert data changes?`);
        $('#btn-save').text('Confirm');
        $('#btn-save').off('click').on('click', async function () {
            let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
            $('#btn-save').text('Reverting...');
            $('#btn-save').prepend(onProcessSpinner);
            $('#btn-save').prop("disabled", true);
            $('<input>').attr({
                type : 'hidden',
                id   : 'profileId',
                name : 'id',
                value: id
            }).prependTo('#common_modal_form form');
            let frmData = $('#frmedit').serializeArray();
            let saveAction = new Promise((resolve, reject) =>{
                $.ajax({
                    type: 'DELETE',
                    url: `{{ route('profile.revert-general') }}`,
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
                    location.href = "{{ route('profile.edit') }}/general";
                }, 1000);
            } else {
                showAlert("No data to update!", "warning", 3000);
            }
            $('#common_modal_form .close').click();
        });
        return false;
    }
    
    function repeatAllData(obj){
        let id = obj.dataset.id;
        $('#delete_modal .modal-dialog').removeClass("modal-lg");
//        $('#delete_modal .modal-dialog').addClass("modal-sm");
        $('#delete_modal .modal-title').html('<i class="fas fa-question-circle mr-2"></i>Revert All Confirmation');
        $('#delete_modal .modal-body').text("Are you sure to revert all unfinished data");
        $('#delete_modal').modal({
            keyboard: false,
            backdrop: 'static'
        });
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
            $('#delete_modal #btn_confirm').text('Reverting...');
            $('#delete_modal #btn_confirm').prepend(onProcessSpinner);
            $.ajax({
                type: 'DELETE',
                url: `{{ route('profile.revertall-general', ['id' =>($profiles->count() > 0 ? $profiles[0]->applicant_id : '')]) }}`,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": `{!! $profiles->count() > 0 ? $profiles[0]->applicant_id : '' !!}`
                }
            }).done(function(data) {
                $('#delete_modal .close').click();
                showAlert("All unfinished data has been repeated successfully!", "success", 3000);
                setTimeout(function(){
                    location.href = "{{ route('profile.edit') }}/general";
                }, 1000);         
            });
            return false;
        });        
    }
    
    function finishAllData(obj){
        let id = obj.dataset.id;
        $('#delete_modal .modal-dialog').removeClass("modal-lg");
//        $('#delete_modal .modal-dialog').addClass("modal-sm");
        $('#delete_modal .modal-title').html('<i class="fas fa-question-circle mr-2"></i>Finish Confirmation');
        $('#delete_modal .modal-body').text("Are you sure to finish all data changes?");
        $('#delete_modal').modal({
            keyboard: false,
            backdrop: 'static'
        });
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
            $('#delete_modal #btn_confirm').text('Finishing...');
            $('#delete_modal #btn_confirm').prepend(onProcessSpinner);
            $.ajax({
                type: 'DELETE',
                url: `{{ route('profile.finishall-general', ['id' =>($profiles->count() > 0 ? $profiles[0]->applicant_id : '')]) }}`,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": `{!! $profiles->count() > 0 ? $profiles[0]->applicant_id : '' !!}`
                }
            }).done(function(data) {
                $('#delete_modal .close').click();
                showAlert("All data has been finished successfully!", "success", 3000);
                setTimeout(function(){
                    location.href = "{{ route('profile.edit') }}/general";
                }, 1000);                        
            });
            return false;
        });        
    }
</script>
@endsection
