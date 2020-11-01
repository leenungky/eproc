@extends('layouts.one_column')

@php
$formName = "frmEvaluation";
@endphp

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">
        <i class="fa fa-list mr-2"></i>{{__('navigation.evaluation')}}
    </span>
</div>
<div class="card-header-right">
    <a id="export-excel" class="btn btn-sm btn-success mr-2" href="{{ route('excel.evaluationlist') }}">Export to Excel</a>
    @if($isBuyerActive && auth()->user()->can('vendor_evaluation_modify'))
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
            <div class="col-12">
                <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter">
                    <thead>
                        <tr>
                            {{-- <th>{{__('homepage.action')}}</th> --}}
                            @foreach ($fields as $field)
                            <th id="th-{{$field}}" class="{{$field}}">{{__('homepage.'.$field)}}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
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
        'form_layout'=>'vendor.evaluation.form_evaluation_general',
        'form_name'=>$formName,
    ];
?>
@include('layouts.modal_common',$modal1)
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script>
var table;
var categories={!!json_encode($categories)!!};
require(["jquery","datatablesb4","moment"], function () {
require(["datetimepicker"], function () {
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".date").datetimepicker({
            format:uiDateFormat,
        })
        //$('#start_date').datetimepicker('minDate', moment().format(uiDateFormat));

        @if($isBuyerActive && auth()->user()->can('vendor_evaluation_modify'))
            $("#start_date").off("change.datetimepicker").on("change.datetimepicker", function (e) {
                $('#end_date').datetimepicker('minDate', e.date);
            });
            $('#year').change(function(){
                resetDate();
            })
            $('#category_id').change(function(){
                let category_id = $(this).val();
                let category = null;
                for(let i=0;i<categories.length;i++){
                    if(categories[i].id==category_id) category = categories[i];
                }
                // $('#project_code').attr('readonly',category==null ? true : category.categories_json!='PROJECT');
                $('#project').hide();
                $('#yearly').show();
                $('#project_code').attr('required',false);
                $('#start_date').attr('required',false).attr('readonly',true);
                $('#end_date').attr('required',false).attr('readonly',true);

                if(category) if(category.categories_json=='PROJECT') {
                    $('#project').show();
                    $('#yearly').show();
                    $('#project_code').attr('required',true);
                    $('#start_date').attr('required',true).attr('readonly',false);
                    $('#end_date').attr('required',true).attr('readonly',false);
                }
                resetDate();
            });

        @endif

        resetDate();

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

        @if($isBuyerActive && auth()->user()->can('vendor_evaluation_modify'))
        $('#btn_create').click(function(){
            $('#id').val('');
            $('#name').val('');
            $('#description').val('');
            $('#project_code').val('');
            $('#category_id').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#project').hide();
            $('#yearly').hide();
            $('#status').val('CONCEPT');
        });
        @endif

        options = getDTOptions();
        options.ajax.url = "{{ route('vendor.evaluation.evaluation_data') }}";
        options.select=undefined;
        options.columns=[
            // {data: 'id', name: 'id'},
            @foreach ($fields as $field)
            {data: '{{$field}}', name: '{{$field}}'},
            @endforeach
        ];
        options.columnDefs=[
            // {
            //     "visible": false,
            //     "targets": 0,
            // },
            {
                "width": 220,
                "targets": ['end_date','start_date'],
            },
            {
                "render": function ( data, type, row, dt ) {
                    var column = dt.settings.aoColumns[dt.col].data;
                    switch(column){
                        case 'name':
                            return '<a href="{{route("vendor.evaluation.evaluation_store")}}/'+row.id+'">'+data+'</a>';
                        break;
                        case 'start_date':
                        case 'end_date':
                            return moment(data).format(uiDateFormat);
                        break;
                        case 'status':
                            let color = 'info';
                            switch(data){
                                case 'CONCEPT': color = 'info'; break;
                                case 'SUBMISSION': color = 'warning'; break;
                                case 'REVISE': color = 'warning'; break;
                                case 'APPROVED': color = 'success'; break;
                            }
                            return '<span class="badge badge-'+color+'">'+data+'</span>';
                        break;
                        default:
                            return data;
                        break;
                    }
                },
                "targets": "_all"
            }
        ];
        CustomDTtOptions.FilterColumn = function(_this, id,el){
            var th = $('<th class="th-filter-column"></th>');
            var title = $(_this).text();
            if (id != null) {
                let field = $(el).attr('id');
                switch(field){
                    case 'th-start_date' :
                    case 'th-end_date' :
                        let createdAt = $(
                            '<input type="text" id="f-'+field+'-at" class="form-control form-control-sm datetimepicker-input date"' +
                            ' name="f-'+field+'-at" data-toggle="datetimepicker" data-target="#f-'+field+'-at"  />'
                        );
                        createdAt
                        .appendTo(th)
                        .on("change.datetimepicker", function () {
                            let _date = (this.value && this.value!= '') ? moment(this.value, uiDateFormat).format(dbDateFormat) : ''
                            table.column(id).search(_date).draw();
                        }).datetimepicker({
                            format: uiDateFormat,
                            icons : {
                                clear: 'fa fa-trash',
                            },
                            buttons: {
                                showClear: true,
                            }
                        });
                        break;
                    default :
                        $(document.createElement("input"))
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("keyup", function () {
                                let SELF = this;
                                if(CustomDTtOptions.searchTimeout != undefined) {
                                    clearTimeout(CustomDTtOptions.searchTimeout);
                                }
                                CustomDTtOptions.searchTimeout = setTimeout(function() {
                                    CustomDTtOptions.searchTimeout = undefined;
                                    table.column(id).search(SELF.value).draw();
                                }, 500);
                            }
                        );
                        break;
                }
            }
            return th;
        };
        options.initComplete = CustomDTtOptions.InitComplete;

        table = $('#datatable_serverside').DataTable(options);

        @if($isBuyerActive && auth()->user()->can('vendor_evaluation_modify'))

            $('#{{$formName}}-save').off('click').on('click',function(){
                let frmId = '#{{$formName}}';

                if ($(frmId)[0].checkValidity()) {
                    let frmData = parseFormData('{{$formName}}');
                    $(frmId+'_fieldset').attr("disabled",true);
                    $.ajax({
                        url : "{{ route('vendor.evaluation.evaluation_store') }}",
                        type : 'POST',
                        data : frmData,
                        cache : false,
                        processData: false,
                        contentType: false,
                    }).done(function(response, textStatus, jqXhr) {
                        if(response.success){
                            showAlert("Evaluation Project "+$(frmId+' #name').val()+" saved.", "success", 3000);
                            table.draw(false);
                            setTimeout(() => {
                                $(frmId+'_modal .close').click();
                                $(frmId)[0].reset();
                                $(frmId+'_fieldset').attr("disabled",false);
                                location.href="{{route('vendor.evaluation.evaluation_store')}}/"+response.data.id;
                            }, 1000);

                        }else{
                            showAlert("Evaluation Project not saved.", "danger", 3000);
                            $(frmId+'_fieldset').attr("disabled",false);
                        }

                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        $(frmId+'_fieldset').attr("disabled",false);
                    });
                }else{
                    showAlert("Please complete the form", "danger");
                }
                return false;
            });

        @endif
    });
});
});

function deleteServerside(obj){
    $('#delete_modal .modal-title').text("Delete "+$(obj).data('label'));
    $('#delete_modal .modal-body').text("Are you sure to delete "+$(obj).data('label')+"?");
    $('#btn_delete_modal').click();
    $('#delete_modal #btn_confirm').off('click').on('click', function () {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('vendor.evaluation.criteria_group') }}/"+$(obj).data('id'),
        }).done(function(data) {
            if(data.success==true){
                $('#delete_modal .close').click();
                showAlert("Group "+$(obj).data('label')+" deleted.", "danger", 3000);
                table.draw(false);
            }else{
                showAlert(data.message, "danger", 3000);
            }
        });
        return false;
    });
}
function resetDate(){
    let year = $('#year').val();
    let minDate = '01.01.'+year;
    let maxDate = '31.12.'+year;
    let from = $('#start_date').val();
    from = from==''?minDate:from;
    let thru = $('#end_date').val();
    thru = thru==''?maxDate:thru;

    let category_id = $('#category_id').val();
    let category = null;

    from = from.substring(0,6)+year;
    thru = thru.substring(0,6)+year;
    for(let i=0;i<categories.length;i++){
        if(categories[i].id==category_id) category = categories[i];
    }
    $('#start_date, #end_date').val('');
    $('#start_date, #end_date').datetimepicker('minDate', false);
    $('#start_date, #end_date').datetimepicker('maxDate', false);
    $('#start_date, #end_date').datetimepicker('minDate', moment(minDate,uiDateFormat).format(uiDateFormat));

    if(category!=null && category.categories_json=='YEARLY'){
        let year = $('#year').val();
        $('#start_date, #end_date').datetimepicker('maxDate', moment(maxDate,uiDateFormat).format(uiDateFormat));
        $('#start_date').val(minDate);
        $('#end_date').val(maxDate);
    }else{
        $('#start_date').val(from);
        $('#end_date').val(thru);
    }
}
</script>
@endsection
