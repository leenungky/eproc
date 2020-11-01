@extends('vendor.evaluation.show')

@php
$formName = "frmForm"
@endphp

@section('contentheader-right')
    <a id="export-excel" class="btn btn-sm btn-success mr-2" href="{{ route('excel.evaluationform', ['id'=>$id]) }}">Export to Excel</a>
@endsection

@section('contentbody')
<div class="has-footer">
    <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter table-ellipsis">
        <thead>
            <tr>
                <th width="50px">{{__('homepage.action')}}</th>
                @foreach ($fields as $key=>$field)
                <th title="{{__('homepage.'.$field)}}" style="width:{{$fieldSizes[$key]}}px">{{__('homepage.'.$field)}}</th>
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
            @if(in_array($general->status,['CONCEPT','REVISE']) && $isBuyerActive)
            @can('vendor_evaluation_modify')
            <ul class="nav">
                <li id="action_group" class="nav-item">
                    <button id="btn_save_flow" class="btn btn-success mr-2">{{__('homepage.submit')}}</button>
                </li>
            </ul>
            @endCan
            @endif
        </div>
    </div>
</div>
@endsection

@section('modals')
<?php
    $modal1 = [
        'title'=> __("homepage.evaluation"),
        'contents'=>'',
        'form_layout'=>'vendor.evaluation.form_evaluation_form',
        'form_name'=>$formName,
    ];
    $modal2 = [
        'title'=> __("homepage.submission"),
        'contents'=>'
            <div id="infoMessage" class="alert alert-info">
            </div>',
        'form_layout'=>'',
        'form_name'=>'frmSubmission',
    ]
?>
@include('layouts.modal_common',$modal1)
@include('layouts.modal_common',$modal2)
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@parent
@include('layouts.datatableoption')
<script>
var table;
var sanctionTypes = {!!json_encode($sanctionTypes)!!};
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

        options = getDTOptions();
        options.autoWidth = false;
        options.ajax.url = "{{ route('vendor.evaluation.evaluation_detail_data',['id'=>$id,'type'=>$type]) }}";
        options.select=undefined;
        options.searching=false;
        options.lengthChange=false;

        options.columns=[
                {data: 'id', name: 'id'},
        @foreach ($fields as $field)
        {data: '{{$field}}', name: '{{$field}}'},
        @endforeach
        ];
        options.columnDefs=[{
                "render": function ( data, type, row, dt ) {
                    @if(in_array($general->status,['CONCEPT','REVISE']) && $samePurchOrg && auth()->user()->can('vendor_evaluation_modify') && $isBuyerActive)
                    if(row.status.toUpperCase()=='CONCEPT'||row.status.toUpperCase()=='REVISE'){
                        return ''+
                        '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.company_name+'"><i class="fa fa-edit"></i></a>'+
                        '<a onClick="deleteServerside(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.company_name+'"><i class="fa fa-trash"></i></a>'+
                        '';
                    }else{
                        return ''+
                            '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.company_name+'"><i class="fa fa-edit"></i></a>'+
                        '';
                    }
                    @else
                        return ''+
                            '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.company_name+'"><i class="fa fa-edit"></i></a>'+
                        '';
                    @endif
                },
                "className": 'text-center',
                "targets": 0,
            },{
                "render": function ( data, type, row, dt ) {
                    var column = dt.settings.aoColumns[dt.col].data;
                    switch(column){
                        case 'start_date':
                        case 'end_date':
                            return moment(data).format(uiDateFormat);
                        break;
                        case 'sanction_type':
                            let badge = 'badge-primary';
                            if(data=="RED" || data=='Blacklisted') badge = 'badge-danger';
                            else if(data=="YELLOW" || data=='Warning') badge = 'badge-warning';
                            else if(data=="GREEN" || data=='No Warning') badge = 'badge-success';
                            return '<span class="badge '+badge+'">'+(sanctionTypes[data] ? data+' ('+sanctionTypes[data]+')' : data)+'</span>';
                        break;
                        case 'company_status':
                            return data==0 ? 'Inactive' : 'Active';
                        break;
                        case 'total_po_value':
                            return accounting.format(accounting.unformat(data,'.'));
                        break;
                        default: 
                            return data; 
                        break;
                    }
                },
                "targets": "_all"
            }
        ];
        options.drawCallback=function(settings){
            if(table.data().length==0){
                $('#btn_save_flow').attr('disabled',true);
            }else{
                $('#btn_save_flow').attr('disabled',false);
            }
        }

        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);

        @if(in_array($general->status,['CONCEPT','REVISE']) && $isBuyerActive)

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
                            showAlert("Evaluation form saved.", "success", 3000);
                            Loading.Hide();
                            setTimeout(() => {
                                table.draw(false);
                                $(frmId+'_modal .close').click();
                                $(frmId)[0].reset();
                                $(frmId+'_fieldset').attr("disabled",false);
                            }, 1000);

                        }else{
                            showAlert("Evaluation form not saved.", "danger", 3000);
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
                $('#infoMessage').text("{{__('homepage.evaluation_submission_confirm_message')}}");
                $('#frmSubmission_modal').modal();
            });
            $('#frmSubmission_modal .modal-dialog').removeClass('modal-lg');
            $('#frmSubmission-save').text("{{__('homepage.submit')}}");
            $('#frmSubmission-save').off('click').on('click',function(){
                Loading.Show();
                $.ajax({
                    url : "{{ route('vendor.evaluation.evaluation_detail_submit',['id'=>$id]) }}",
                    type : 'POST',
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Evaluation submitted.", "success", 3000);
                        $('#frmSubmission-save').attr('hidden',true);
                        Loading.Hide();
                        setTimeout(() => {
                            table.draw(false);
                            $('#frmSubmission_modal .close').click();
                            $('#action_group').closest('ul').attr('hidden',true);
                            $('#frmForm-save').attr('hidden',true);
                        }, 1000);

                    }else{
                        showAlert("Evaluation submission failed.", "danger", 3000);
                        Loading.Hide();
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    showAlert("Evaluation submission failed.", "danger", 3000);
                    Loading.Hide();
                });
            });
            @endCan
        @else
        $('#frmForm-save').attr('hidden',true);
        $('input[name*="score"]').attr('disabled',true);
        @endif
    });
});

async function edit(obj){
    var data = table.row('#'+$(obj).data('id')).data();
    $('#{{$formName}}_modal .modal-title').text('{{$general->name}}: '+$(obj).data('label'));
    $('#{{$formName}}_modal #id').val(data.id);
    $('#{{$formName}}_modal #evaluation_id').val(data.evaluation_id);
    $('#{{$formName}}_modal #vendor_id').val(data.vendor_id);
    $('#{{$formName}}_modal #name').val(data.name);
    $('#{{$formName}}_modal input[name="score[]"]').val(0);

    let getData = new Promise((resolve, reject) => {
        $.ajax({
            url : "{{ route('vendor.evaluation.evaluation_detail_store',['id'=>$id,'type'=>$type]) }}/"+data.vendor_id,
            type : 'GET',
            cache : false,
        }).done(function (resp) {
            resolve(resp);
        });
    });
    let result = await getData;
    if(result.success){
        for(let i=0;i<result.data.length;i++){
            $('#{{$formName}}_modal #score-'+result.data[i].criteria_id).val(result.data[i].score);
        }
    }else{
        showAlert("Fail to get data.", "danger", 3000);
    }
    
    $('#{{$formName}}_modal').modal();
}
@can('vendor_evaluation_modify')
@if(in_array($general->status,['CONCEPT','REVISE']) && $isBuyerActive)
function deleteServerside(obj){
    var data = table.row('#'+$(obj).data('id')).data();
    // console.log(data);return;
    $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
    $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('vendor.evaluation.evaluation_detail_store',['id'=>$id,'type'=>$type]) }}/"+$(obj).data('id'),
        }).done(function(data) {
            $('#delete_modal .close').click();
            showAlert("Score "+$(obj).data('label')+" deleted.", "danger", 3000);
            table.draw(false);
        });
        return false;
    });
}
@endif
@endCan

</script>
@endsection
