@extends('layouts.one_column')

@php
$formName = "frmScore";
@endphp

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">
        <i class="fa fa-list mr-2"></i>{{__('navigation.score_categories')}}
    </span>
</div>
<div class="card-header-right">
    @if($isBuyerActive && auth()->user()->can('vendor_evaluation_score_categories_modify'))
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
        'title'=> __("navigation.score_categories"),
        'contents'=>'',
        'form_layout'=>'vendor.evaluation.form_score',
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
require(["jquery","datatablesb4"], function () {
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#categories_json").change(function(){
            let selector = $(this).val();
            $('#yearly').hide();
            $('#po_count, #po_total').attr('required',selector=='YEARLY');
            if(selector=='YEARLY'){
                $('#yearly').show();
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

        @if($isBuyerActive && auth()->user()->can('vendor_evaluation_score_categories_modify'))
        $('#btn_create').click(function(){
            $('#tbl_categories tbody').html('');
            $('#id').val('');
            $('#name').val('');
            $('#po_total').val('0');
            $('#po_count').val('0');
            $('#categories_json').val('').change();
            $('#frmScore').removeClass('was-validated').removeClass('needs-validation').addClass('needs-validation');
        });
        @endif

        options = getDTOptions();
        options.ajax.url = "{{ route('vendor.evaluation.score_data') }}";
        options.select=undefined;
        options.columns=[
                {data: 'id', name: 'id'},
        @foreach ($fields as $field)
        {data: '{{$field}}', name: '{{$field}}'},
        @endforeach
        ];
        options.columnDefs=[{
                "render": function ( data, type, row ) {
                    @if($isBuyerActive && auth()->user()->can('vendor_evaluation_score_categories_modify'))
                    return ''+
                        '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.name+'"><i class="fa fa-edit"></i></a>'+
                        '<a onClick="deleteServerside(this)" style="cursor:pointer" data-id="'+data+'" data-label="'+row.name+'"><i class="fa fa-trash"></i></a>'+
                    '';
                    @else
                    return ''+
                        '<a onClick="edit(this)" class="mr-2" style="cursor:pointer" data-id="'+data+'" data-label="'+row.name+'"><i class="fa fa-edit"></i></a>'+
                    '';
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

        @if($isBuyerActive && auth()->user()->can('vendor_evaluation_score_categories_modify'))
        $('#addRow').click(function(){
            addRow();
        });

        $('#{{$formName}}-save').off('click').on('click',function(){
            let frmId = '#{{$formName}}';
            let categories = $('#tbl_categories tbody').children();

            if ($(frmId)[0].checkValidity() && categories.length>0) {
                let frmData = parseFormData('{{$formName}}');
                $(frmId+'_fieldset').attr("disabled",true);
                $.ajax({
                    url : "{{ route('vendor.evaluation.score_store') }}",
                    type : 'POST',
                    data : frmData,
                    cache : false,
                    processData: false,
                    contentType: false,
                }).done(function(response, textStatus, jqXhr) {
                    if(response.success){
                        showAlert("Score "+$(frmId+' #name').val()+" saved.", "success", 3000);
                        setTimeout(() => {
                            table.draw(false);
                            $(frmId+'_modal .close').click();
                            $(frmId)[0].reset();
                            $(frmId+'_fieldset').attr("disabled",false);
                        }, 1000);

                    }else{
                        showAlert("Score not saved.", "danger", 3000);
                        $(frmId+'_fieldset').attr("disabled",false);
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $(frmId+'_fieldset').attr("disabled",false);
                });
            }else{
                showAlert("Please complete the form", "danger");
                $('#frmScore').removeClass('was-validated').removeClass('needs-validation').addClass('was-validated');
            }
            return false;
        });
        @endif
    });
});

function edit(obj){
    var data = table.row('#'+$(obj).data('id')).data();
    $('#id').val(data.id);
    $('#name').val(data.name);
    $('#po_total').val(data.po_total);
    $('#po_count').val(data.po_count);
    $('#categories_json').val(data.categories_json).change();
    $('#frmScore').removeClass('was-validated').removeClass('needs-validation').addClass('needs-validation');
    $('#tbl_categories tbody').html('');
    $.each(data.scores,function(idx,el){
        addRow(el);
    });
    @if($isBuyerActive && auth()->user()->can('vendor_evaluation_score_categories_modify'))
    @else
    $('#frmScore_fieldset').attr('disabled',true);
    $('#frmScore .modal-footer, #frmScore #addRow').hide();
    @endif
    
    $('#{{$formName}}_modal').modal();
}
@if($isBuyerActive && auth()->user()->can('vendor_evaluation_score_categories_modify'))
function deleteServerside(obj){
    $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
    $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('vendor.evaluation.score') }}/"+$(obj).data('id'),
        }).done(function(data) {
            $('#delete_modal .close').click();
            showAlert("Score "+$(obj).data('label')+" deleted.", "danger", 3000);
            table.draw(false);
        });
        return false;
    });
}
@endif
function addRow(el){
    $('#tbl_categories tbody').append(getTemplate(el));
    @if($isBuyerActive && auth()->user()->can('vendor_evaluation_score_categories_modify'))
    $('.deleteRow').off('click').on('click',function(){
        $(this).closest('tr').remove();
    });
    @endif
}
function getTemplate(data){
    let template = {'name':'','lowest_score':'','lowest_score_operator':'','highest_score':'','highest_score_operator':''}
    if(typeof(data)=='undefined') data=template;
    if(typeof(data.name)=='undefined') data=template;
    return '<tr>'+
            @if($isBuyerActive && auth()->user()->can('vendor_evaluation_score_categories_modify'))
            '<td style="text-align:center;padding-top:.7rem"><a class="deleteRow" href="javascript:void(0)"><i class="fas fa-trash"></i></a>"</td>'+
            @else
            '<td style="text-align:center;padding-top:.7rem"></td>'+
            @endif
            '<td><input class="form-control form-control-sm" name="nm[]" type="text" value="'+data.name+'"></td>'+
            '<td><select class="custom-select custom-select-sm" name="lso[]"><option value="&gt;="'+(data.lowest_score_operator=='&gt;='?' selected':'')+'>&gt;=</option><option value="&gt;"'+(data.lso=='&gt;'?' selected':'')+'>&gt;</option></td>'+
            '<td><input class="form-control form-control-sm" name="ls[]" type="number" min="0" max="100" value="'+data.lowest_score+'"></td>'+
            '<td><select class="custom-select custom-select-sm" name="hso[]"><option value="&lt;="'+(data.highest_score_operator=='&lt;='?' selected':'')+'>&lt;=</option><option value="&lt;"'+(data.hso=='&lt;'?' selected':'')+'>&gt;</option></td>'+
            '<td><input class="form-control form-control-sm" name="hs[]" type="number" min="0" max="100" value="'+data.highest_score+'"></td>'+
        '</tr>';
}
</script>
@endsection
