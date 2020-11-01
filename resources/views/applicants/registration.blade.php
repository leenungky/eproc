@extends('layouts.one_column')

@include('layouts.navigation')

@section('content')
<div class="fullcards">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ __('homepage.partner_registration') }}
        </div>
        <div class="card-body form-registration">
            <form id="registration-form" novalidate method="POST" action="{{ route('applicant.submission') }}" class="form-dl-horizontal-sm" enctype="multipart/form-data">
                @csrf
                @include('applicants.form.textinput')
            </form>
        </div>
    </div>
</div>
@endsection

<!-- MODAL Section -->
@section('modals')
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
<script type="text/javascript">
    var postalCodes = {!!json_encode($postalCodes)!!};
    require(["jquery", "bootstrap", "bootstrap-util", "metisMenu", "bootstrap-fileinput", "select2", "jquery-mask"], function () {
    require(["bootstrap-fileinput-fas"], function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.fn.select2.defaults.set( "theme", "bootstrap" );

        let txtIdentityNumber = `{{ __('homepage.choose_identification_type') }}`;
        let txtTaxAttachment = `{{ __('homepage.tax_document_attachment') }}`;
        let txtTaxResidenceAttachment = `{{ __('homepage.tax_certified_of_residence_attachment') }}`;
        let txtCardAttachment = `{{ __('homepage.id_card_document_attachment') }}`;
        let txtTIN = `{{ __('homepage.tax_identification_number') }}`;
        let txtDefaultTIN = `{{ config('eproc.vendor_management.tin_foreign') }}`;
        let txtIDCard = `{{ __('homepage.id_card_number') }}`;
        $(".tin-field-area").fadeOut();
        $("#field-address_1").hide();
        $("#field-address_2").hide();
        $("#field-address_3").hide();
        $("input[name=vendor_group]").change(function(){
            let vendorGroup = $("input[name=vendor_group]:checked").val();
            $('.id_type').fadeIn();
            $("#country").val("").trigger("change");
            if(vendorGroup === 'local'){
                // Change opsi address
                $("#street").prop("required", true);
                $("label[for=street]").find(".font-danger").html("*");
                $("#field-street").show();

                $("#address_1").val("");
                $("#address_1").removeAttr("required");
                $("label[for=address_1]").find(".font-danger").html("");
                $("#field-address_1").hide();

                $("#building_name").prop("disabled", false);
                $("#kavling_floor_number").prop("disabled", false);
                $("#field-building_name").show();
                $("#field-kavling_floor_number").show();

                $("#village").prop("required", true);
                $("label[for=village]").find(".font-danger").html("*");
                $("#field-village").show();

                $("#address_2").val("");
                $("#address_2").removeAttr("required");
                $("label[for=address_2]").find(".font-danger").html("");
                $("#field-address_2").hide();

                $("#address_3").val("");
                $("#address_3").removeAttr("required");
                $("label[for=address_3]").find(".font-danger").html("");
                $("#field-address_3").hide();

                $("#rt").prop("disabled", false);
                $("#rt").prop("required", true);
                $("label[for=rt]").find(".font-danger").html("*");
                $("#field-rt").show();
                $("#rw").prop("disabled", false);
                $("#rw").prop("required", true);
                $("label[for=rw]").find(".font-danger").html("*");
                $("#field-rw").show();

//                $("#country").val(null).trigger("change");
                $("label[for=province]").find(".font-danger").html("*");
                $("label[for=city]").find(".font-danger").html("*");
                $("label[for=sub_district]").find(".font-danger").html("*");

                $('#province').prop("required", true);
                $('#city').prop("required", true);
                $('#sub_district').prop("required", true);
                $("#field-province").show();
                $("#field-city").show();
                $("#field-sub_district").show();

                $("#house_number").prop("disabled", false);
                $("#field-house_number").show();

                $("#identification_type_idcard").closest('div').show();
                $("#identification_type_tin").prop("checked", false);
                $("#identification_type_idcard").prop("checked", false).trigger("change");
                $("#identity_number").prop("placeholder", txtIdentityNumber);
                $("label[for=identity_attachment]").text(txtCardAttachment);
                $("#identity_number").prop("readonly", true);
                $(".tin-field-area").fadeOut();
                $("#tax_identification_number").val("");
            } else {
                $("#street").val("");
                $("#street").removeAttr("required");
                $("label[for=street]").find(".font-danger").html("");
                $("#field-street").hide();

                $("#address_1").prop("required", true);
                $("label[for=address_1]").find(".font-danger").html("*");
                $("#field-address_1").show();

                $("#building_name").val("");
                $("#building_name").prop("disabled", true);
                $("#field-building_name").hide();

                $("#kavling_floor_number").val("");
                $("#kavling_floor_number").prop("disabled", true);
                $("#field-kavling_floor_number").hide();

                $("#village").val("");
                $("#village").removeAttr("required");
                $("label[for=village]").find(".font-danger").html("");
                $("#field-village").hide();

                $("#address_2").removeAttr("required");
                $("label[for=address_2]").find(".font-danger").html("");
                $("#field-address_2").show();

                $("#address_3").removeAttr("required");
                $("label[for=address_3]").find(".font-danger").html("");
                $("#field-address_3").show();

                $("#rt").val("");
                $("#rt").prop("disabled", true);
                $("label[for=rt]").find(".font-danger").html("");
                $("#field-rt").hide();

                $("#rw").val("");
                $("#rw").prop("disabled", true);
                $("label[for=rw]").find(".font-danger").html("");
                $("#field-rw").hide();

//                $("#country").val(null).trigger("change");
                $("label[for=province]").find(".font-danger").html("");
                $("label[for=city]").find(".font-danger").html("");
                $("label[for=sub_district]").find(".font-danger").html("");

                $('#province').removeAttr('required');
                $('#city').removeAttr('required');
                $('#sub_district').removeAttr('required');
                $("#field-province").hide();
                $("#field-city").hide();
                $("#field-sub_district").hide();

                $("#house_number").val("");
                $("#house_number").prop("disabled", true);
                $("#field-house_number").hide();

                $("#identification_type_idcard").closest('div').hide();
                $("#identification_type_tin").prop("checked", true).trigger("change");;
                $("#identification_type_idcard").prop("checked", false)
                $("label[for=identity_number]").text(txtTIN);
                $("label[for=identity_attachment]").text(txtTaxResidenceAttachment);
                $("#identity_number").prop("placeholder", txtIDCard);
                $("#identity_number").prop("readonly", false);
                $(".tin-field-area").fadeOut();
                $("#identity_number").prop("placeholder", txtTIN);
                $("#identity_number").val("");
                $("div.identity-field-area").fadeIn();
                $("div.pkp_type").fadeOut();
                $("div.pkp-field-area").fadeOut();
                $("#pkp_type").prop("required", false);
                $("input[name=pkp_type]").prop("required", false);
                //$("#tax_identification_number").val(txtDefaultTIN);
            }
        });
        $("#identification_type_tin").prop("checked", true).trigger("change");;

        $("input[name=identification_type]").change(function(){
            let vendorGroup = $("input[name=vendor_group]:checked").val();
            if($('#identification_type_tin').is(':checked')){
                if(vendorGroup === 'local'){
                    $("label[for=identity_number]").text(txtTIN);
                    $("label[for=identity_attachment]").text(txtTaxAttachment);
                    $("#identity_number").prop("placeholder", txtTIN);
                    $("#identity_number").prop("readonly", false);
                    $("input[name=pkp_type]:checked").prop("checked", false);
                    $("div.identity-field-area").fadeIn();
                    $("div.pkp_type").fadeIn();
                    $("div.has_pkp").fadeOut();
                    $("div.has_non_pkp").fadeOut();
                    $("#pkp_type").prop("required", true);
                    $("input[name=pkp_type]").prop("required", true);
                }
            } else if($('#identification_type_idcard').is(':checked')){
                $("label[for=identity_number]").text(txtIDCard);
                $("label[for=identity_attachment]").text(txtCardAttachment);
                $("#identity_number").prop("placeholder", txtIDCard);
                $("#identity_number").prop("readonly", false);
                $("div.identity-field-area").fadeIn();
                $("div.pkp-field-area").fadeOut();
                $("#pkp_type").prop("required", false);
                $("input[name=pkp_type]").prop("required", false);
                let vendorGroup = $("input[name=vendor_group]:checked").val();
                if (vendorGroup === 'foreign'){
                    $(".tin-field-area").fadeIn();
                } else {
                    $(".tin-field-area").fadeOut();
                }
            } else {
                if(vendorGroup === 'local'){
                    $("#identity_number").val('');
                    $("#identity_number").prop("readonly", true);
                    $("div.identity-field-area").fadeOut();
                    $("div.pkp-field-area").fadeOut();
                    $("#pkp_type").prop("required", false);
                    $("input[name=pkp_type]").prop("required", false);
                }
            }
            $("#pkp_number").prop("required", false);
            $("#pkp_attachment").prop("required", false);
            $("#non_pkp_number").prop("required", false);
        });

        $("input[name=pkp_type]").change(function(){
            if($('#is_pkp').is(':checked')){
                $("#non_pkp_number").val('');
                $("div.has_pkp").fadeIn();
                $("div.has_non_pkp").fadeOut();
                $("#pkp_number").prop("required", true);
                $("#pkp_attachment").prop("required", true);
                $("#non_pkp_number").prop("required", false);
            } else if($('#is_non_pkp').is(':checked')){
                $("#non_pkp_number").val('000000000000000');
                $("div.has_pkp").fadeOut();
                $("div.has_non_pkp").fadeIn();
                $("#pkp_number").prop("required", false);
                $("#pkp_attachment").prop("required", false);
                $("#non_pkp_number").prop("required", true);
            } else {
                $("#non_pkp_number").val('');
                $("#pkp_number").prop("required", false);
                $("#pkp_attachment").prop("required", false);
                $("#non_pkp_number").prop("required", false);
            }
        });

        $("#company_type_id").select2({
            dropdownAutoWidth : true,
            width: 'auto',
            placeholder: "-- Select --"
        }).change(function(){
            let category = $(this).find(":selected").data("category");
            if(category === 'local'){
                // TIN or ID Card
            }
        });
        $("#country").select2({
            dropdownAutoWidth : true,
            width: 'auto',
            placeholder: "-- Select --"
        }).change(function(){
            var val_id = $(this).val();
//            var el = $(".country-attr .font-danger");
            let vendorGroup = $("input[name=vendor_group]:checked").val();
            let countrySymbol = $("#country").val();
            $('#province').val("").trigger("change");
            if(vendorGroup === "local"){
                if(countrySymbol.trim().length){
    //                if(val_id === 'ID'){
                    $("#province").prop("disabled", false);
                    $("#city").prop("disabled", true);
                    $("#sub_district").prop("disabled", true);

                    let required = (val_id === 'ID')
                    $("#province").prop("required", required);
                    $("#city").prop("required", required);
                    $("#sub_district").prop("required", required);
                    $("#village").prop("required", required);
                    $("#rt").prop("required", required);
                    $("#rw").prop("required", required);
                    $("label[for=province]").find(".font-danger").html(required ? "*" : "");
                    $("label[for=city]").find(".font-danger").html(required ? "*" : "");
                    $("label[for=sub_district]").find(".font-danger").html(required ? "*" : "");
                    $("label[for=village]").find(".font-danger").html(required ? "*" : "");
                    $("label[for=rt]").find(".font-danger").html(required ? "*" : "");
                    $("label[for=rw]").find(".font-danger").html(required ? "*" : "");
//                    }
                } else {
                    $("#province").prop("disabled", true);
                    $("#city").prop("disabled", true);
                    $("#sub_district").prop("disabled", true);
                }
//                } else {
//                    $("#province").prop("disabled", true);
//                    $("#city").prop("disabled", true);
//                    $("#sub_district").prop("disabled", true);
//                    $("label[for=province]").find(".font-danger").html("");
//                    $("label[for=city]").find(".font-danger").html("");
//                    $("label[for=sub_district]").find(".font-danger").html("");
//                }
            } else {
                $("#province").prop("disabled", true);
                $("#city").prop("disabled", true);
                $("#sub_district").prop("disabled", true);
            }
            if(countrySymbol.trim().length){
                let postalCodeLength = 20;
                let postalCodeRequired = false;
                if(postalCodes && postalCodes[countrySymbol]){
                    postalCodeLength = postalCodes[countrySymbol].length === 0 ? 20 : postalCodes[countrySymbol].length;
                    postalCodeRequired = postalCodes[countrySymbol].required;
                    $("label[for=postal_code]").find(".font-danger").html(postalCodes[countrySymbol].required ? '*' : '');
                }
                $("#postal_code").prop("required", postalCodeRequired);
                $("#postal_code").attr("maxlength", postalCodeLength);
            }

        });
        $("#province").select2({
            ajax: {
                type: 'POST',
                url: "{{ route('reference.province') }}",
                data: function (params) {
                    let query = {
                        search     : params.term,
                        countrycode: $("#country").val()
                    };
                    // Query parameters will be ?search=[term]&type=public
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data
                    };
                }
            }
        }).change(function(){
            $("#city").val("").trigger("change");
            $("#city").prop("disabled", false);
            $("#sub_district").prop("disabled", true);
        });
        $("#city").select2({
            ajax: {
                type: 'POST',
                url: "{{ route('reference.city') }}",
                data: function (params) {
                    let query = {
                        search     : params.term,
                        countrycode: $("#country").val(),
                        regioncode: $("#province").val(),
                    };
                    // Query parameters will be ?search=[term]&type=public
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data
                    };
                }
            }
        }).change(function(){
            $("#sub_district").val(null).trigger("change");
            $("#sub_district").prop("disabled", false);
        });
        $("#sub_district").select2({
            ajax: {
                type: 'POST',
                url: "{{ route('reference.subdistrict') }}",
                data: function (params) {
                    let query = {
                        search     : params.term,
                        countrycode: $("#country").val(),
                        regioncode: $("#province").val(),
                        citycode: $("#city").val()
                    };
                    // Query parameters will be ?search=[term]&type=public
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data
                    };
                }
            }
        }).change(function(){
            $("#postal_code").val("");
        });

        $(".phone-input").keypress(function (e) {
            var charTyped = String.fromCharCode(e.which);
            var letterRegex = /[^+0-9]/;

            if (!charTyped.match(letterRegex)) {
                return true;
            }
            else {
                return false;
            }
        });

        let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);

        $("#identity_attachment").fileinput({'theme': 'fas', 'showUpload':false, 'previewFileType':'any', 'required':true, allowedFileExtensions : ['jpeg', 'jpg', 'gif', 'pdf'], maxFileSize : maxUploadSize});

        $("#pkp_attachment").fileinput({'theme': 'fas', 'showUpload':false, 'previewFileType':'any', maxFileSize: maxUploadSize});

        $("#npwp_tin_attachment").fileinput({'theme': 'fas', 'showUpload':false, 'previewFileType':'any', maxFileSize: maxUploadSize, allowedFileExtensions : ['jpeg', 'jpg', 'gif', 'pdf']});

        $("#btn-check").click(function(e){
            let forms = $("#registration-form");
            if (forms[0].checkValidity() === true) {
                if($(this).find('i').hasClass('fa-square')){
                    $(this).find('i').removeClass('fa-square');
                    $(this).find('i').addClass('fa-check-square');
                    $("#btn-submit").attr("hidden", false);
                } else {
                    $(this).find('i').removeClass('fa-check-square');
                    $(this).find('i').addClass('fa-square');
                    $("#btn-submit").attr("hidden", true);
                }
            }
            forms[0].classList.add('was-validated');
            if($(".was-validated .form-control:invalid").length > 0){
                $('html, body').animate({
                    scrollTop: $(".was-validated .form-control:invalid").offset().top - 60
                }, 500);
            }
        });

        $("#btn-submit").click(function(){
            let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
            $('#btn-submit').attr("disabled", true);
            $('#btn-submit').text('Submitting...');
            $('#btn-submit').prepend(onProcessSpinner);
            let frmData = new FormData($("#registration-form")[0]);//$("#registration-form").serializeArray();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('applicant.submission') }}",
                type: 'POST',
                data: frmData,
                cache : false,
                processData: false,
                contentType: false
            }).done(function (response, textStatus, jqXhr) {
                if (response.success) {
                    showAlert(response.message, "success", 3000);
                    setTimeout(function(){
                        location.href = "{{ route('main') }}";
                    }, 3000);
                } else {
                    showAlert(response.message, "danger", 3000);
                    $('#btn-submit').html('Submit');
                    $('#btn-submit').attr("disabled", false);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                showAlert("Data not saved. Please try again", "danger", 3000);
                $('#btn-submit').html('Submit');
                $('#btn-submit').attr("disabled", false);
            });
        });
    });
    });
</script>
@endsection
