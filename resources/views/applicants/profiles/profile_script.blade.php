<script type="text/javascript">
require(["jquery", "bootstrap", "bootstrap-util", "metisMenu", "bootstrap-fileinput"], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    @foreach($attachmentList as $attachment)
    $('#{{$attachment}}').change(function(e){
        $('#{{$attachment}}_label').text('');
        if(e.target.files.length>0){
            $('#{{$attachment}}_label').text(e.target.files[0].name);
        }
    });
    @endforeach
    $('#btn_create_new_{{$formName}}').click(function(){
        resetForm();
        $('#{{$formName}}_modal .modal-tile').text('{{__("homepage.create_new_entry")}}');
        $('#{{$formName}}-save').off('click').on('click',function(){
            saveData("{{ route('profile.create',$type) }}");
        });
    });
});

function resetForm(){
    $('#{{$formName}}')[0].reset();
    @foreach($attachmentList as $attachment)
    $('#{{$attachment}}').attr('required',true);
    $('#{{$attachment}}_filename').text('').attr('href','');
    @endforeach
}

function getFormData(){
    let formData = new FormData($('#{{$formName}}')[0]);

    //iterating form data
    for(var pair of formData.entries()) {
        //date
        if($('#{{$formName}} input[name="'+pair[0]+'"]').hasClass('date')){
            formData.set(pair[0], moment(pair[1], uiDateFormat).format(dbDateFormat));
        }
        //datetime
        if($('#{{$formName}} input[name="'+pair[0]+'"]').hasClass('datetime')){
            formData.set(pair[0], moment(pair[1], uiDatetimeFormat).format(dbDatetimeFormat));
        }
        //money
        if($('#{{$formName}} input[name="'+pair[0]+'"]').hasClass('money')){
            formData.set(pair[0], accounting.unformat(pair[1]));
        }
    }

    return formData;
}

function saveData(url){
    let onProcessSpinner = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>';
    $('#{{$formName}}_modal #{{$formName}}-save').text('Saving...');
    $('#{{$formName}}_modal #{{$formName}}-save').prepend(onProcessSpinner);
    let frmData = getFormData();
    $('#{{$formName}}_fieldset').attr("disabled", true);
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
            showAlert("Data has been saved successfully!", "success", 3000);
            setTimeout(function(){
                location.href = "{{ route('profile.edit') }}/{{$type}}";
            }, 1000);
        } else {
            showAlert("Data not saved. Please try again", "danger", 3000);
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
    let element = $('#'+key);
    if(element.attr('type')==='file'){
        element.attr('required',false);
        $('#'+key+'_filename').text(value).attr('href',"{{$storage.'/'}}"+value);

    }else if(element.hasClass('money')){
        element.val(value);

    }else if(element.hasClass('date')){
        element.val(moment(value, dbDateFormat).format(uiDateFormat));

    }else if(element.hasClass('datetime')){
        element.val(moment(value, dbDatetimeFormat).format(uiDatetimeFormat));

    }else{
        element.val(value);
    }
}

async function editData(obj, editType){
    let id = obj.dataset.id;
    let getData = new Promise((resolve, reject) => {
        $.ajax({
            "type": "GET",
            "url": "{{ route('profile.find-data',$type) }}",
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
        });
        $('#profileId').val(result.id);
        $('#editType').val(editType);
        $('#{{$formName}}_modal').modal();
        $('#{{$formName}}-save').off('click').on('click',function(){
            saveData("{{ route('profile.update',$type) }}");
        })

    } else {
        $("#frmedit-save").hide();
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
            {'name':'applicant_id', 'value':"{{$applicant['id']}}"},
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
                location.href = "{{ route('profile.edit') }}/{{$type}}";
            }, 1000);
        } else {
            showAlert(option.warning, "warning", 3000);
        }
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
        'url': `{{ route('profile.revertall', ['type'=>$type, 'id'=>$applicant['id']]) }}`,
    })
}

function finishAllData(obj){
    deleteData(obj,{
        'title': '<i class="fas fa-question-circle mr-2"></i>Finish Confirmation',
        'body': "Are you sure to finish all data changes?",
        'spinner': 'Finishing...',
        'success': "All data has been finished successfully!",
        'warning': "No data to update!",
        'url': `{{ route('profile.finishall', ['type'=>$type, 'id'=>$applicant['id']]) }}`,
    })
}
</script>
