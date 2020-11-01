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
        <button id="btn_create_new_general" class="btn btn-sm btn-success" data-toggle="modal" data-target="#frmdeeds_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{ __('homepage.create_new_entry') }}</button>
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
        @if( $profiles->count() > 0 )
            <?php $i = 1; ?>
            @foreach ($profiles as $profile)
                @if($profile->is_current_data)
                    <!-- Check has an edited data or not -->
                    @foreach ($profiles as $newdata)
                        @if($profile->id == $newdata->parent_id)
                            <tr>
                                <td rowspan="8" class="text-center">{{ $i }}</td>
                                <td>{{ __('homepage.deeds_type') }}</td>
                                <td>{{ $profile->deed_type }}</td>
                                <td>{{ $newdata->deed_type }}</td>
                                <td class="text-center">Prepared</td>
                                <td rowspan="8" class="text-center">
                                    <button data-id="{{ $newdata->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="revertEditedData(this);" class="btn btn-sm btn-link" tabindex="Undo Data Edited"><i class="fas fa-undo" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.deeds_number') }}</td>
                                <td>{{ $profile->deed_number }}</td>
                                <td>{{ $newdata->deed_number }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.deeds_date') }}</td>
                                <td>{{ $profile->deed_date }}</td>
                                <td>{{ $newdata->deed_date }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.notary_name') }}</td>
                                <td>{{ $profile->notary_name }}</td>
                                <td>{{ $newdata->notary_name }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.sk_menkumham_number') }}</td>
                                <td>{{ $profile->sk_menkumham_number }}</td>
                                <td>{{ $newdata->sk_menkumham_number }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.sk_menkumham_date') }}</td>
                                <td>{{ $profile->sk_menkumham_date }}</td>
                                <td>{{ $newdata->sk_menkumham_date }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{{ __('homepage.attachment') }}</td>
                                <td>{{ $profile->attachment }}</td>
                                <td>{{ $newdata->attachment }}</td>
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
                                    <td rowspan="8" class="text-center">{{ $i }}</td>
                                    <td>{{ __('homepage.deeds_type') }}</td>
                                    <td>{{ $profile->deed_type }}</td>
                                    <td></td>
                                    <td class="text-center">Prepared</td>
                                    <td rowspan="8" class="text-center">
                                        <button data-id="{{ $profile->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="editCurrentData(this);" class="btn btn-sm btn-link"><i class="fas fa-edit" aria-hidden="true"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.deeds_number') }}</td>
                                    <td>{{ $profile->deed_number }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.deeds_date') }}</td>
                                    <td>{{ $profile->deed_date }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.notary_name') }}</td>
                                    <td>{{ $profile->notary_name }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.sk_menkumham_number') }}</td>
                                    <td>{{ $profile->sk_menkumham_number }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.sk_menkumham_date') }}</td>
                                    <td>{{ $profile->sk_menkumham_date }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('homepage.attachment') }}</td>
                                    <td>{{ $profile->attachment }}</td>
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
                            <td>{{ __('homepage.deeds_type') }}</td>
                            <td></td>
                            <td>{{ $profile->deed_type }}</td>
                            <td class="text-center">Prepared</td>
                            <td rowspan="13" class="text-center">
                                <div class="button-group">
                                    <button data-id="{{ $profile->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="editAddedData(this);" class="btn btn-sm btn-link" style="padding-left: 0px;"><i class="fas fa-edit" aria-hidden="true"></i></button>                                
                                    <button data-id="{{ $profile->id }}" data-toggle="modal" data-target=".bd-common-form" data-backdrop="static" data-keyboard="false" onclick="deleteAddedData(this);" class="btn btn-sm btn-link" style="padding: 0px;"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>                                
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.deeds_number') }}</td>
                            <td></td>
                            <td>{{ $profile->deed_number }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.deeds_date') }}</td>
                            <td></td>
                            <td>{{ $profile->deed_date }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.notary_name') }}</td>
                            <td></td>
                            <td>{{ $profile->notary_name }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.sk_menkumham_number') }}</td>
                            <td></td>
                            <td>{{ $profile->sk_menkumham_number }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.sk_menkumham_date') }}</td>
                            <td></td>
                            <td>{{ $profile->sk_menkumham_date }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>{{ __('homepage.attachment') }}</td>
                            <td></td>
                            <td>{{ $profile->attachment }}</td>
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
                <td>{{ __('homepage.deeds_type') }}</td>
                <td></td>
                <td></td>
                <td class="text-center">Prepared</td>
                <td rowspan="13" class="text-center">
                </td>
            </tr>
            <tr>
                <td>{{ __('homepage.deeds_number') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.deeds_date') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.notary_name') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.sk_menkumham_number') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.sk_menkumham_date') }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('homepage.attachment') }}</td>
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
<?php
$modal1 = [
    'title' => __("homepage.create_new_entry"),
    'contents' => '',
    'form_layout' => 'applicants.profiles.form.form_deeds',
    'form_name' => 'frmdeeds',
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
        $('#frmdeeds-save').click(function () {
            var frmData = $('#frmdeeds').serializeArray();
            //frmData.push({name: 'items', value: JSON.stringify(selectedData)});
            $('#frmdeeds_fieldset').attr("disabled", true);
            $.ajax({
                url: "{{ route('profile.create-deed') }}",
                type: 'POST',
                data: frmData
            }).done(function (response, textStatus, jqXhr) {
                // console.log(response);
                if (response.success) {
                    $('#frmdeeds_modal .close').click();
                    //showAlert("Draft Tender " + response.data.number + " saved.", "success", 3000);
                    location.href = "{{ route('profile.edit') }}/deeds";
                } else {
    //                    showAlert("Draft Tender message saved.", "danger", 3000);
                    $('#frmdeeds_fieldset').attr("disabled", false);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // console.log("The following error occured: " + textStatus, errorThrown);
                $('#frmdeeds_fieldset').attr("disabled", false);
            });
        });

        let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
        // with plugin options
        $("#attachment").fileinput({'showUpload':false, 'previewFileType':'any', maxFileSize: maxUploadSize});

        let buttonFooterLeft = `
            <div class="btn-group btn-pages mr-auto">
                <button id="frmdeeds-previous" type="button" class="btn btn-sm btn-outline-secondary text-center" 
                    style="width: 110px;" disabled><i class="fas fa-angle-double-left mr-2"></i>{{ __('homepage.previous') }}
                </button>
                <button id="frmdeeds-next" type="button" class="btn btn-sm btn-outline-secondary text-center" 
                    style="width: 110px;">{{ __('homepage.next') }}<i class="fas fa-angle-double-right ml-2"></i>
                </button>
            </div>`; 
        $('.modal-footer').prepend(buttonFooterLeft);

        $("#frmdeeds-previous").click(function(){
            $("#frmdeeds-previous").prop("disabled", true);
            $("#frmdeeds-next").prop("disabled", false);
            $(".page1").removeClass("display-none");
            $(".page1").addClass("display-block");
            $(".page2").removeClass("display-block");
            $(".page2").addClass("display-none");
        });

        $("#frmdeeds-next").click(function(){
            $("#frmdeeds-previous").prop("disabled", false);
            $("#frmdeeds-next").prop("disabled", true);
            $(".page1").removeClass("display-block");
            $(".page1").addClass("display-none");
            $(".page2").removeClass("display-none");
            $(".page2").addClass("display-block");
        });
    });    
</script>
@endsection