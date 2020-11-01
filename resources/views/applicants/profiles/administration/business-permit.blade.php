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
    <span class="heading-title">{{ __('homepage.administration_data') }}: {{ __('homepage.akta_header_title') }}</span>              
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
        <button id="btn_create_new_general" class="btn btn-sm btn-success" data-toggle="modal" data-target="#frmbusinesspermit_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{ __('homepage.create_new_entry') }}</button>
    </div>                        
</div>
@endsection

@section('contentbody')
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
        @php ($fields = ['business_permit_type','business_permit_attachment','business_class','business_permit_number','valid_from_date', 'valid_thru_date', 'issued_by'])
        @if( $profiles->count() > 0 )
            <?php $i = 1; ?>
            @foreach ($profiles as $profile)
                @if($profile->is_current_data)
                    <!-- Check has an edited data or not -->
                    @foreach ($profiles as $newdata)
                        @if($profile->id == $newdata->parent_id)
                            <?php $i++; ?>
                            @php ($j=0)
                            @foreach($fields as $field)
                            <tr>
                                @if($j==0) <td rowspan="{{count($fields)}}" class="text-center">{{ $i }}</td> @endif
                                <td>{{ __('homepage.'.$field) }}</td>
                                <td>{{ $profile->$field }}</td>
                                <td>{{ $newdata->$field }}</td>
                                @if($j==0) <td class="text-center">Prepared</td> @else <td></td> @endif
                                @if($j==0)
                                <td rowspan="{{count($fields)}}" class="text-center">
                                    <button data-id="{{ $newdata->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="revertEditedData(this);" class="btn btn-sm btn-link" tabindex="Undo Data Edited"><i class="fas fa-undo" aria-hidden="true"></i></button>
                                </td>
                                @endif
                            </tr>
                            @php ($j++)
                            @endforeach
                            <tr style="background-color: #d8d5d5;" class="row{{ $profile->id }}">
                                <td colspan="6" style="padding: 2px;"></td>
                            </tr>
                            <?php $i++; ?>
                            @break;
                        @else
                            @if ($loop->last)
                                @php ($j=0)
                                @foreach($fields as $field)
                                <tr>
                                    @if($j==0) <td rowspan="{{count($fields)}}" class="text-center">{{ $i }}</td> @endif
                                    <td>{{ __('homepage.'.$field) }}</td>
                                    <td>{{ $profile->$field }}</td>
                                    <td></td>
                                    @if($j==0) <td class="text-center">Prepared</td> @else <td></td> @endif
                                    @if($j==0)
                                    <td rowspan="{{count($fields)}}" class="text-center">
                                        <button data-id="{{ $profile->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="editCurrentData(this);" class="btn btn-sm btn-link"><i class="fas fa-edit" aria-hidden="true"></i></button>
                                    </td>
                                    @endif
                                </tr>
                                @php ($j++)
                                @endforeach
                                <tr style="background-color: #d8d5d5;" class="row{{ $profile->id }}">
                                    <td colspan="6" style="padding: 2px;"></td>
                                </tr>
                                <?php $i++; ?>
                            @endif
                        @endif
                    @endforeach
                @else
                    @if($profile->parent_id == 0)
                        @php ($j=0)
                        @foreach($fields as $field)
                        <tr>
                            @if($j==0) <td rowspan="{{count($fields)}}" class="text-center">{{ $i }}</td> @endif
                            <td>{{ __('homepage.'.$field) }}</td>
                            <td></td>
                            <td>{{ $profile->$field }}</td>
                            @if($j==0) <td class="text-center">Prepared</td> @else <td></td> @endif
                            @if($j==0)
                            <td rowspan="{{count($fields)}}" class="text-center">
                                <div class="button-group">
                                    <button data-id="{{ $profile->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="editAddedData(this);" class="btn btn-sm btn-link" style="padding-left: 0px;"><i class="fas fa-edit" aria-hidden="true"></i></button>
                                    <button data-id="{{ $profile->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="deleteAddedData(this);" class="btn btn-sm btn-link" style="padding: 0px;"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @php ($j++)
                        @endforeach
                        <tr style="background-color: #d8d5d5;" class="row{{ $profile->id }}">
                            <td colspan="6" style="padding: 2px;"></td>
                        </tr>
                        <?php $i++; ?>
                    @endif
                @endif        
            @endforeach        
        @else
            @php ($j=0)
            @foreach($fields as $field)
            <tr>
                @if($j==0) <td rowspan="{{count($fields)}}" class="text-center">1</td> @endif
                <td>{{ __('homepage.'.$field) }}</td>
                <td></td>
                <td></td>
                @if($j==0) <td class="text-center">Prepared</td> @else <td></td> @endif
                @if($j==0)
                <td rowspan="{{count($fields)}}" class="text-center">
                </td>
                @endif
            </tr>
            @php ($j++)
            @endforeach
            <tr style="background-color: #d8d5d5;">
                <td colspan="6" style="padding: 2px;"></td>
            </tr>
        @endif
    </tbody>
</table>
@endsection

@section('modals')
<?php
$modal1 = [
    'title' => __("homepage.create_new_entry"),
    'contents' => '',
    'form_layout' => 'applicants.profiles.form.form_businesspermit',
    'form_name' => 'frmbusinesspermit',
        ]
?>
@include('layouts.modal_common',$modal1)
<!--@include('layouts.modal_delete')-->
@endsection

@section('modules-scripts')
<script type="text/javascript">
    require(["bootstrap-fileinput"], function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#frmbusinesspermit-save').click(function () {
            var frmData = $('#frmbusinesspermit').serializeArray();
            //frmData.push({name: 'items', value: JSON.stringify(selectedData)});
            $('#frmbusinesspermit_fieldset').attr("disabled", true);
            $.ajax({
                url: "{{ route('profile.create-deed') }}",
                type: 'POST',
                data: frmData
            }).done(function (response, textStatus, jqXhr) {
                // console.log(response);
                if (response.success) {
                    $('#frmbusinesspermit_modal .close').click();
                    //showAlert("Draft Tender " + response.data.number + " saved.", "success", 3000);
                    location.href = "{{ route('profile.edit') }}/deeds";
                } else {
    //                    showAlert("Draft Tender message saved.", "danger", 3000);
                    $('#frmbusinesspermit_fieldset').attr("disabled", false);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // console.log("The following error occured: " + textStatus, errorThrown);
                $('#frmbusinesspermit_fieldset').attr("disabled", false);
            });
        });

        let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
        // with plugin options
        $("#business_permit_attachment").fileinput({'showUpload':false, 'previewFileType':'any', maxFileSize: maxUploadSize});

        let buttonFooterLeft = `
            <div class="btn-group btn-pages mr-auto">
                <button id="frmbusinesspermit-previous" type="button" class="btn btn-sm btn-outline-secondary text-center" 
                    style="width: 110px;" disabled><i class="fas fa-angle-double-left mr-2"></i>{{ __('homepage.previous') }}
                </button>
                <button id="frmbusinesspermit-next" type="button" class="btn btn-sm btn-outline-secondary text-center" 
                    style="width: 110px;">{{ __('homepage.next') }}<i class="fas fa-angle-double-right ml-2"></i>
                </button>
            </div>`; 
        $('.modal-footer').prepend(buttonFooterLeft);

        $("#frmbusinesspermit-previous").click(function(){
            $("#frmbusinesspermit-previous").prop("disabled", true);
            $("#frmbusinesspermit-next").prop("disabled", false);
            $(".page1").removeClass("display-none");
            $(".page1").addClass("display-block");
            $(".page2").removeClass("display-block");
            $(".page2").addClass("display-none");
        });

        $("#frmbusinesspermit-next").click(function(){
            $("#frmbusinesspermit-previous").prop("disabled", false);
            $("#frmbusinesspermit-next").prop("disabled", true);
            $(".page1").removeClass("display-block");
            $(".page1").addClass("display-none");
            $(".page2").removeClass("display-none");
            $(".page2").addClass("display-block");
        });
    });    
</script>
@endsection