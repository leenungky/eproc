<script type="text/javascript">
require(["moment"], function(){
require(["jquery", "bootstrap", "bootstrap-util", "metisMenu", "select2", "datetimepicker"], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#btn_create_new_{{$formName}}').click(function(){
        resetForm();
        $('#{{$formName}}_modal .modal-title').text('{{__("homepage.create_new_entry")}}');
        $('#{{$formName}}-save').off('click').on('click',function(){
            saveData("{{ route('profile.create',$type) }}");
        });
    });
    if($("#attachment").length > 0){
        require(["bootstrap-fileinput-fas"], function(){
            let maxUploadSize = parseInt(`{{ config('eproc.upload_file_size') }}`);
            if($("#attachment").length > 0){
                $("#attachment").fileinput({
                    'theme'     : 'fas',
                    'showUpload':false,
                    'required'  : true,
                    'maxFileSize' : maxUploadSize
                });

                {{--@foreach($attachmentList as $attachment)
                $('#{{$attachment}}').change(function(e){
                    $('#{{$attachment}}_label').text('');
                    if(e.target.files.length>0){
                        $('#{{$attachment}}_label').text(e.target.files[0].name);
                    }
                });
                @endforeach
                --}}
            }
        });
    }
    $.fn.select2.defaults.set("theme", "bootstrap" );
    $("#location_category").select2({
        dropdownAutoWidth: true,
        width: 'auto',
        placeholder: "-- Select --"
    });
    $("#country").select2({
        dropdownAutoWidth : true,
        width: 'auto',
        placeholder: "-- Select --"
    }).change(function(){
        var val_id = $(this).val();
        var el = $(".country-attr .font-danger");
        if (val_id!="ID"){
            $(el).html("");
            $(".frm-country-attr").removeAttr("required");
            $('#province').removeAttr('required');
            $('#city').removeAttr('required');
            $("#sub_district").removeAttr('required');
            $("#district").removeAttr('required');
            $("#rt").removeAttr('required');
            $("#rw").removeAttr('required');
            $("#village").removeAttr('required');
        }else{
            $(".frm-country-attr").attr("required","true");
            $('#province').attr('required',"true");
            $('#city').attr('required',"true");
            $("#sub_district").attr('required',"true");
            $("#district").attr('required',"true");
            $('#rt').attr('required',"true");
            $("#rw").attr('required',"true");
            $("#village").attr('required',"true");
            $(el).html("*");
        }
        $("#postal_code").val("");
        $("#province").val(null).trigger("change");
        $("#province").prop("disabled", false);
        $("#city").prop("disabled", true);
        $("#sub_district").prop("disabled", true);
        $("#district").prop("disabled", true);
        if(typeof(postalCodes) !== "undefined"){
            // console.log(postalCodes[val_id]);
            $("#postal_code").attr("required",val_id=='' ? true : postalCodes[val_id].required);
            $("label[for='postal_code'] span").html(val_id=='' ? '*' : (postalCodes[val_id].required ? '*' : ''));
            $("#postal_code").attr("maxlength",val_id=='' ? 20 : (postalCodes[val_id].length==0 ? 20 : postalCodes[val_id].length));
        }else{
            if (val_id=="ID"){
                $("#postal_code").attr("required",true);
                $("label[for='postal_code'] span").html('*');
                $("#postal_code").attr("maxlength",5);
            }else{
                $("#postal_code").attr("required",false);
                $("label[for='postal_code'] span").html('');
                $("#postal_code").attr("maxlength",20);
            }
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
        $("#city").val(null).trigger("change");
        $("#city").prop("disabled", false);
        $("#sub_district").prop("disabled", true);
        $("#district").prop("disabled", true);
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
        $("#district").val(null).trigger("change");
        $("#district").prop("disabled", false);
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
//        $("#postal_code").val("");
    });
    $("#district").select2({
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
//        $("#postal_code").val("");
    });
});
});

function resetForm(){
    $('#{{$formName}}')[0].reset();
   {{-- @foreach($attachmentList as $attachment)
    $('#{{$attachment}}').attr('required',true);
    $('#{{$attachment}}_filename').text('').attr('href','');
    @endforeach
    --}}
    $("#country").val("").trigger("change");
}

function getFormData(){
    return parseFormData('{{$formName}}');
}

function saveData(url){
    let forms = $("#{{$formName}}");
    forms[0].classList.add('was-validated');
    $("input[name='postal_code'").attr("required");
    if ($("input[name='postal_code'").val()==""){
        showAlert("postal code required input", "danger", 3000);
        return;
    }
    if (forms[0].checkValidity() === false) {
        if($(".was-validated .form-control:invalid").length > 0){
            $('html, body').animate({
                scrollTop: $(".was-validated .form-control:invalid").offset().top - 60
            }, 500);
        }
        return false;
    }

    let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
    $('#{{$formName}}_modal #{{$formName}}-save').text('Saving...');
    $('#{{$formName}}_modal #{{$formName}}-save').prepend(onProcessSpinner);
    let frmData = getFormData();
    $('#{{$formName}}_fieldset').attr("disabled", true);
    console.log(url);
    $.ajax({
        url: url,
        type: 'POST',
        data: frmData,
        cache : false,
        processData: false,
        contentType: false,
    }).done(function (response, textStatus, jqXhr) {
        if (response.success) {
            $('#{{$formName}}_modal .close').click();
            showAlert(response.message, "success", 3000);
            let btn = '<button id="frmgeneral-save" type="submit" class="btn btn-sm btn-primary">Save</button>';
            $('#{{$formName}}_fieldset').removeAttr("disabled");
            $('#{{$formName}}_modal #{{$formName}}-save').html(btn);
            setTimeout(function(){
                //window.location.reload(true);
            }, 1000);
        } else {
            showAlert(response.message, "danger", 3000);
            $('#{{$formName}}_modal #{{$formName}}-save').text('Save');
            $('#{{$formName}}_fieldset').attr("disabled", false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        showAlert("Data not saved. Please try again", "danger", 3000);
        $('#{{$formName}}_modal #{{$formName}}-save').text('Save');
        $('#{{$formName}}_fieldset').attr("disabled", false);
    });
}

function parseInput(key,value){
    parseInputData(key,value,'{{$storage}}');
}

async function editData(obj, editType){
    let id = $(obj).val();
    let loc_cat = $("#po-location-category").val();
    let getData = new Promise((resolve, reject) => {
        $.ajax({
            "type": "GET",
            "url": "{{ route('po.find-data', 'detail_address') }}?id=" + $(obj).val() + "&loc-cat=" + loc_cat + "&tender_number={{$tender->tender_number}}&vcode={{$vendor->vendor_code}}&vendor_id={{$vendor->vendor_profiles_id}}",
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
        resetForm();
        $('#{{$formName}}_modal .modal-title').text('{{__("homepage.form_edit_data")}}');
        $.each(result, function(key,value){
            parseInput(key,value);
            switch (key) {
                case 'primary_data':
                    $(`#${key}`).prop("checked", value);
                    break;
                case 'company_head':
                    $(`#${key}`).prop("checked", value);
                    break;
                default:
                    break;
            }
        });
        console.log(result);
        $('#profileId').val(result.vendor_profile_id);

        $("#province").prop('disabled', false);
        $("#city").prop('disabled', false);
        $("#sub_district").prop('disabled', false);
        $("#district").prop('disabled', false);

        $("#location_category").val(result.location_category).trigger("change");

        $("#country").select2().val(result.country).trigger("change");
        if(result.province && result.province!=null) $("#province").append('<option value="'+result.province+'" selected>' + result.region_description + '</option>').trigger("change");
        if(result.city && result.city!=null) $("#city").append('<option value="'+result.city+'" selected>' + result.city_description + '</option>').trigger("change");
        if(result.sub_district && result.sub_district!=null) $("#sub_district").append('<option value="'+result.sub_district+'" selected>' + result.district_description + '</option>').trigger("change");
        if(result.sub_district && result.sub_district!=null) $("#district").append('<option value="'+result.sub_district+'" selected>' + result.district_description + '</option>').trigger("change");
        if(result.postal_code && result.postal_code!=null) $("#postal_code").val(result.postal_code).trigger("change");

        $('#editType').val(editType);
        $('#{{$formName}}_modal').modal();
            $('#{{$formName}}-save').hide();
        $('#{{$formName}}-save').off('click').on('click',function(){
            saveData("{{ route('po.update',$type) }}");
        });
    } else {   
        var idnew = $("input[name='vendor_profile_id']").val();
        $('#profileId').val(idnew);
        $("#province, #city, #sub_district, #district").select2().val("");
        $('#frmgeneral input.form-control').val("");
        $("#location_category").val(loc_cat).trigger("change");
        $('#{{$formName}}_modal').modal();
            $('#{{$formName}}-save').hide();
        $('#{{$formName}}-save').off('click').on('click',function(){
            saveData("{{ route('po.update',$type) }}");
        });
    }
    
}

function editAddedData(obj){
    editData(obj,'added');
}

function editCurrentData(obj){
    editData(obj,'current');
}

function deleteData(obj, option){
    let id = obj.dataset.id;    
    $('#delete_modal .modal-title').html(option.title);
    $('#delete_modal .modal-body').html(option.body);
    $('#delete_modal').modal();
    $('#delete_modal #btn_confirm').off('click').on('click', async function(){
        let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
        $('#delete_modal #btn_confirm').text(option.spinner);
        $('#delete_modal #btn_confirm').prepend(onProcessSpinner);
        $('#delete_modal #btn_confirm').prop("disabled", true);
        let frmData = [
            {'name':'id', 'value':id},
            {'name':'vendor_id', 'value':"{{$vendor['id']}}"},
            {'name':'vendor_profile_id', 'value':"{{ $vendor['id'] }}"},
            {'token': "{{csrf_token()}}"},
        ];
        let saveAction = new Promise((resolve, reject) =>{
            $.ajax({
                type: 'DELETE',
                url: option.url,
                data: frmData,
                cache: false,
            }).done(function(resp) {
                resolve(resp);
            });
        });
        let resSave = await saveAction;
        $('#delete_modal #btn_confirm').prop("disabled", false);
        if(resSave.success === true && typeof resSave.data.id !== 'undefined'){
            showAlert(option.success, "success", 3000);
            setTimeout(function(){
                location.href = "{{ route('profile.show') }}";
            }, 1000);
        } else {
            showAlert(option.warning, "warning", 3000);
        }
        $('#delete_modal #btn_confirm').html(option.button);
        $('#delete_modal #btn_confirm').prop("disabled", false);

        $('#delete_modal .close').click();
    });
    return false;
}

function deleteAddedData(obj){
    deleteData(obj,{
        'title': '<i class="fas fa-question-circle mr-2"></i>Delete Data Confirmation',
        'body': `Are you sure to delete data added?`,
        'spinner': 'Deleting...',
        'success': "The data has been deleted successfully!",
        'warning': "No data to update!",
        'url': `{{ route('profile.revert', $type) }}`,
    })
}

function revertEditedData(obj){
    deleteData(obj,{
        'title': '<i class="fas fa-question-circle mr-2"></i>Revert Data Confirmation',
        'body': `Are you sure to revert data changes?`,
        'spinner': 'Reverting...',
        'success': "The data has been reverted successfully!",
        'warning': "No data to update!",
        'url': `{{ route('profile.revert', $type) }}`,
    })
}

function repeatAllData(obj){
    deleteData(obj,{
        'title': '<i class="fas fa-question-circle mr-2"></i>Revert All Confirmation',
        'body': "Are you sure to revert all unfinished data?",
        'spinner': 'Reverting...',
        'success': "All unfinished data has been repeated successfully!",
        'warning': "No data to update!",
        'url': `{{ route('profile.revertall', ['type'=>$type, 'id'=>$vendor['id']]) }}`,
    })
}

function finishAllData(obj){
    deleteData(obj,{
        'title': '<i class="fas fa-question-circle mr-2"></i>Finish Confirmation',
        'body': "Are you sure to finish all data changes?",
        'button': "Finish",
        'spinner': 'Finishing...',
        'success': "{{ __('homepage.the_data_has_been_finished_successfully') }}",
        'warning': "{{ __('homepage.unavailable_data_to_be_finished') }}",
        'url': `{{ route('profile.finishall', ['type'=>$type, 'id'=>$vendor['id']]) }}`,
    });
}

</script>
