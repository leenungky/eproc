@extends('vendor.evaluation.show')

@php
$formName = "frmAssignment"
@endphp

@section('contentheader-right')
<span class="badge badge-secondary mr-4">{{__('homepage.weighting')}}: <span id="total_weight">0</span></span>
@if(in_array($general->status,['CONCEPT','REVISE']) && $isBuyerActive)
@can('vendor_evaluation_modify')
    <button id="btn_create" style="display:none" class="btn btn-sm btn-success" data-toggle="modal" data-target="#{{$formName}}_modal" data-backdrop="static" data-keyboard="false"><i class="fas fa-plus mr-2" aria-hidden="true"></i>{{__('common.new_entry')}}</button>
@endCan
@endif
@endsection

@section('contentbody')
<div class="has-footer">
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
<div class="app-footer">
    <div class="app-footer__inner">
        <div class="app-footer-left">
            <div id="page_numbers" style="display:inherit"></div>
        </div>
        <div class="app-footer-right">
            <ul class="nav">
                @if(in_array($general->status,['CONCEPT','REVISE']) && $isBuyerActive)
                <li id="action_group" class="nav-item" style="display:none">
                    <button id="btn_save_flow" class="btn btn-primary mr-2">{{__('homepage.submit')}}</button>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __("homepage.create_new_entry"),
        'contents'=>'',
        'form_layout'=>'vendor.evaluation.form_evaluation_assignment',
        'form_name'=>$formName,
    ];
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
@include('layouts.datatableoption')
<script>
var table;
require(["jquery","datatablesb4"], function () {
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#page_numbers").ready(function () {
            $("#datatable_serverside_paginate").appendTo($("#page_numbers"));
            $("#datatable_serverside_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
        });

        @if(in_array($general->status,['CONCEPT','REVISE']) && $samePurchOrg && $isBuyerActive)
        $('#btn_create').click(function(){
            let max = 100-parseInt($('#total_weight').text());
            $('#id').val('');
            $('#name').val('');
            $('#criteria_id').val('');
            $('#weighting').attr('max',max).val('0');
            $('#max_weighting').text(max);
            $('#minimum_score').val('0');
            $('#maximum_score').val('0');
        });
        @endif

        options = getDTOptions();
        options.ajax.url = "{{ route('vendor.evaluation.evaluation_detail_data',['id'=>$id,'type'=>$type]) }}";
        options.select=undefined;
        options.searching=false;
        options.lengthChange=false;
        options.order=[[5,'asc']];
        options.drawCallback=function( settings ) {
            let data = table.data();
            let weight = 0;
            let finished = 0;
            for(let i=0;i<data.length;i++){
                weight+=parseInt(data[i].weighting);
                finished = finished + data[i].is_finished;
            }
            $('#total_weight').text(weight);
            if(weight==100){
                $('#btn_create').hide();
                // if(finished==0){
                //     $('#btn_save_flow').parent().show();
                // }else{
                //     $('#btn_save_flow').parent().hide();
                // }
            }else{
                $('#btn_create').show();
                $('#btn_save_flow').parent().hide();
            }

            $('#page_form').removeClass('disabled');
            @if($scoreAssignment)
            if(weight<100) $('#page_form').addClass('disabled');
            @endif
        };

        options.columns=[
                {data: 'id', name: 'id'},
        @foreach ($fields as $field)
        {data: '{{$field}}', name: '{{$field}}'},
        @endforeach
        ];
        options.columnDefs=[{
                "render": function ( data, type, row, dt ) {
                    @if(in_array($general->status,['CONCEPT','REVISE']) && $samePurchOrg && auth()->user()->can('vendor_evaluation_modify') && $isBuyerActive)
                    return ''+
                        (row.is_finished==0 ? 
                            '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.criteria_name+'"><i class="fa fa-edit"></i></a>'+
                            '<a onClick="deleteServerside(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.criteria_name+'"><i class="fa fa-trash"></i></a>'+
                            (dt.row!=0 ? '<a onClick="moveUp(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.criteria_name+'"><i class="fa fa-arrow-up"></i></a>' : '') +
                            (dt.row<table.data().length-1 ? '<a onClick="moveDown(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.criteria_name+'"><i class="fa fa-arrow-down"></i></a>' : '') +
                        '' : '') +
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

        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);

        @if(in_array($general->status,['CONCEPT','REVISE']) && $samePurchOrg && $isBuyerActive)
        @can('vendor_evaluation_modify')
        $('#{{$formName}}-save').off('click').on('click',function(){
            let frmId = '#{{$formName}}';

            if ($(frmId)[0].checkValidity()) {
                let frmData = parseFormData('{{$formName}}');
                $(frmId+'_fieldset').attr("disabled",true);
                Loading.Show();
                $.ajax({
                    url : "{{ route('vendor.evaluation.evaluation_detail_store',['id'=>$id,'type'=>$type]) }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Assignment saved.", "success", 3000);
                        setTimeout(() => {
                            table.draw(false);
                            $(frmId+'_modal .close').click();
                            $(frmId)[0].reset();
                            $(frmId+'_fieldset').attr("disabled",false);
                            Loading.Hide();
                        }, 1000);

                    }else{
                        showAlert("Assignment not saved.", "danger", 3000);
                        $(frmId+'_fieldset').attr("disabled",false);
                        Loading.Hide();
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                    Loading.Hide();
                });
            }else{
                showAlert("Please complete the form", "danger");
            }
            return false;
        });
        $('#btn_save_flow').click(function(){
            let frmId = '#{{$formName}}';
            Loading.Show();
            $.ajax({
                url : "{{ route('vendor.evaluation.evaluation_detail_store_finish',['id'=>$id,'type'=>$type]) }}",
                type : 'POST',
                cache : false,
            }).done(function(response, textStatus, jqXhr) {
                if(response.success){
                    setTimeout(() => {
                        location.reload();
                        Loading.Hide();
                    }, 1000);

                }else{
                    showAlert("Submit Failed.", "danger", 3000);
                    $(frmId+'_fieldset, #btn_save_flow').attr("disabled",false);
                }
                Loading.Hide();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                $(frmId+'_fieldset, #btn_save_flow').attr("disabled",false);
                Loading.Hide();
            });
        });
        @endCan
        @endif
    });
});

@if(in_array($general->status,['CONCEPT','REVISE']) && $samePurchOrg && $isBuyerActive)
@can('vendor_evaluation_modify')
function edit(obj){
    var data = table.row('#'+$(obj).data('id')).data();
    let max = 100-parseInt($('#total_weight').text())+parseInt(data.weighting);

    $('#id').val(data.id);
    $('#name').val(data.name);
    $('#criteria_id').val(data.criteria_id);
    $('#weighting').attr('max',max).val(data.weighting);
    $('#max_weighting').text(max);
    $('#minimum_score').val(data.minimum_score);
    $('#maximum_score').val(data.maximum_score);
    
    $('#{{$formName}}_modal').modal();
}
function deleteServerside(obj){
    $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
    $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('vendor.evaluation.evaluation_detail_store',['id'=>$id,'type'=>$type]) }}/"+$(obj).data('id'),
        }).done(function(data) {
            $('#delete_modal .close').click();
            showAlert("Assignment "+$(obj).data('label')+" deleted.", "danger", 3000);
            table.draw(false);
        });
        return false;
    });
}
function moveUp(obj){
    $.ajax({
        type: 'PATCH',
        url: "{{ route('vendor.evaluation.evaluation_detail_store',['id'=>$id,'type'=>$type]) }}/up/"+$(obj).data('id'),
    }).done(function(data) {
        table.draw(false);
    });
}
function moveDown(obj){
    $.ajax({
        type: 'PATCH',
        url: "{{ route('vendor.evaluation.evaluation_detail_store',['id'=>$id,'type'=>$type]) }}/down/"+$(obj).data('id'),
    }).done(function(data) {
        table.draw(false);
    });
}
@endCan
@endif
</script>
@endsection
