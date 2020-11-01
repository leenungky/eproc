@extends('layouts.one_column')

@php
$formName = "frmCriteria";
@endphp

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">
        <i class="fa fa-list mr-2"></i>{{__('navigation.criteria')}}
    </span>
</div>
<div class="card-header-right">
    @if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_modify'))
    <button id="btn_create" class="btn btn-sm btn-success" data-toggle="modal" data-target="#{{$formName}}_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('common.new_entry')}}</button>
    @endif
</div>
@endsection

@section('contentbody')
<div class="has-footer">
        <div class="card-fixed">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th>{{__('homepage.action')}}</th>
                        @foreach ($fields as $field)
                        <th>{{__('homepage.'.$field)}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('footer')
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
        </div>
    </div>
</div>
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __("homepage.create_new_entry"),
        'contents'=>'',
        'form_layout'=>'vendor.evaluation.form_criteria',
        'form_name'=>$formName,
    ];
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script>
var table;
@if($scoreAssignment)
var criteriaGroups={!!json_encode($criteriaGroups)!!};
@endif
require(["jquery","datatablesb4"], function () {
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#page_numbers").ready(function () {
            $("#datatable_serverside_paginate").appendTo($("#page_numbers"));
            $("#page_numbers").append('<input id="input-page" class="form-control form-control-sm" type="number" min="1" max="1000">')
            $("#datatable_serverside_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
            $('#input-page').keypress(function (event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    table.page($(this).val() * 1 - 1).draw(false);
                }
            });
        });

        @if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_modify'))
        $('#btn_create').click(function(){
            $('#id').val('');
            $('#name').val('');
            $('#description').val('');
            $('#criteria_group_id').val('');
            $('#weighting').val('0');
            $('#max_weighting').text('100');
            $('#oldweight').val('0');
            $('#oldcriteria').val('');
        });
        $('#criteria_group_id').change(function(){

            options = {
                criteria_group_id : $(this).val(),
                weighting : 0,
                minimum_score : 0,
                maximum_score : 100
            };
            changeCriteriaGroup(options);
        })
        @endif

        options = getDTOptions();
        options.ajax.url = "{{ route('vendor.evaluation.criteria_data') }}";
        options.select=undefined;
        options.columns=[
                {data: 'id', name: 'id'},
        @foreach ($fields as $field)
        {data: '{{$field}}', name: '{{$field}}'},
        @endforeach
        ];
        options.columnDefs=[{
                "render": function ( data, type, row ) {
                    let label = row.name+' ('+row.criteria_group_name+')';
                    @if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_modify'))
                    return ''+
                        '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+label+'"><i class="fa fa-edit"></i></a>'+
                        '<a onClick="deleteServerside(this)" style="cursor:pointer" data-id="'+data+'" data-label="'+label+'"><i class="fa fa-trash"></i></a>'+
                    '';
                    @else
                    return '';
                    @endif
                },
                "className": 'text-center',
                "targets": 0,
            },{
                "render": function ( data, type, row, dt ) {
                    var column = dt.settings.aoColumns[dt.col].data;
                    switch(column){
                        case 'created_at':
                            return moment(data).format(uiDatetimeFormat);
                        break;
                        default:
                            return data;
                        break;
                    }
                },
                "targets": "_all"
            }
        ];
        options.initComplete = CustomDTtOptions.InitComplete;
        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);

        @if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_modify'))
        $('#{{$formName}}-save').off('click').on('click',function(){
            let frmId = '#{{$formName}}';

            if ($(frmId)[0].checkValidity()) {
                let frmData = parseFormData('{{$formName}}');
                $(frmId+'_fieldset').attr("disabled",true);
                $.ajax({
                    url : "{{ route('vendor.evaluation.criteria_store') }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Criteria "+$(frmId+' #name').val()+" saved.", "success", 3000);
                        setTimeout(() => {
                            table.draw(false);
                            $(frmId+'_modal .close').click();
                            $(frmId)[0].reset();
                            $(frmId+'_fieldset').attr("disabled",false);
                            @if($scoreAssignment)refreshCriteriaGroup();@endif
                        }, 1000);

                    }else{
                        showAlert("Criteria not saved.", "danger", 3000);
                        $(frmId+'_fieldset').attr("disabled",false);
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                });
            }else{
                showAlert("Please complete the form and make sure the value is valid", "danger");
            }
            return false;
        });
        @endif
    });
});

@if($isBuyerActive && auth()->user()->can('vendor_evaluation_criteria_modify'))
function edit(obj){
    var data = table.row('#'+$(obj).data('id')).data();

    $('#id').val(data.id);
    $('#name').val(data.name);
    $('#criteria_group_id').val(data.criteria_group_id);
    $('#description').val(data.description);
    $('#oldweight').val(data.weighting);
    $('#oldcriteria').val(data.criteria_group_id);
    @if($scoreAssignment)
        changeCriteriaGroup(data);
        // for(i=0;i<criteriaGroups.length;i++){
        //     if(criteriaGroups[i].id==data.criteria_group_id){
        //         let totalWeighting = parseInt(criteriaGroups[i].total_weighting) || 0;
        //         let currentWeighting = parseInt(data.weighting) || 0;
        //         let max = 100 - totalWeighting + currentWeighting;
        //         $('#weighting').attr('max',max).val('0');
        //         $('#max_weighting').text(max);
        //     }
        // }
        // $('#weighting').val(data.weighting);
        // $('#minimum_score').val(data.minimum_score);
        // $('#maximum_score').val(data.maximum_score);
    @endif

    $('#{{$formName}}_modal').modal();
}
function deleteServerside(obj){
    $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
    $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('vendor.evaluation.criteria') }}/"+$(obj).data('id'),
        }).done(function(data) {
            $('#delete_modal .close').click();
            showAlert("Criteria "+$(obj).data('label')+" deleted.", "danger", 3000);
            table.draw(false);
            @if($scoreAssignment)refreshCriteriaGroup();@endif
        });
        return false;
    });
}
@if($scoreAssignment)
function changeCriteriaGroup(data){
    let max = 100;
    let oldweight = $('#oldweight').val();
    let oldcriteria = $('#oldcriteria').val();
    for(i=0;i<criteriaGroups.length;i++){
        if(criteriaGroups[i].id==data.criteria_group_id){
            let totalWeighting = parseInt(criteriaGroups[i].total_weighting) || 0;
            let currentWeighting = parseInt(oldweight) || 0;
            max = 100 - totalWeighting;
            if(oldcriteria==data.criteria_group_id) max += currentWeighting;
        }
    }
    $('#weighting').attr('max',max).val('0');
    $('#max_weighting').text(max);
    $('#weighting').val(data.weighting);
    $('#minimum_score').val(data.minimum_score);
    $('#maximum_score').val(data.maximum_score);
}
async function refreshCriteriaGroup(){
    let getData = new Promise((resolve, reject) => {
        $.ajax({
            type: "GET",
            url: "{{ route('vendor.evaluation.criteria_group_json') }}",
            cache : false,
            data: {_token: "{{ csrf_token() }}"}
        }).done(function (resp) {
            resolve(resp);
        });
    }).catch(error => {
        location.reload();
    });
    let result = await getData;

    if(result.success){
        criteriaGroups = result.data;
    }else{
        location.reload();
    }

}
@endif
@endif
</script>
@endsection
