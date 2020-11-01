@extends('layouts.one_column')

@section('contentheader')
    <i class="fa fa-list mr-1"></i>
    {{ __('tender.tender_process') }}
@endsection

@section('contentbody')
    <div class="has-footer">
        <div class="card-fixed">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif
            <table id="datatable_serverside" class="table table-bordered table-striped table-vcenter table-wrap table-sm">
                <thead>
                    <tr>
                        <th>{{__('purchaserequisition.action')}}</th>
                        @foreach ($fields as $field)
                        <th id="{{$field}}">{{__('tender.'.$field)}}</th>
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
@include('layouts.modal_delete')
@endsection

@section('modules-scripts')
@include('layouts.datatableoption')
<script type="text/javascript">
    var table;
    var selectedRows = [];
    var selectedData = [];
    var searchTimeout = undefined;
    require(["datatablesb4","dt.plugin.select",'datetimepicker'], function () {
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
        options = getDTOptions();
        options.ajax.url = "{{ route('tender.data') }}";
        options.select=undefined;
        // options.order = [[ 2, "asc" ]];
        options.columns=[
            {data: 'id', name: 'id'},
            @foreach ($fields as $field)
            @if($field == 'pr_number')
            {
                data: '{{$field}}', name: '{{$field}}', className: 'wd-200',
                render : function ( data, type, row, dt ) {
                    return '<a class="text-popup wd-200 text-dark" href="" data-toggle="popover" data-content="'+data+'">'+data+'</a>';
                },
            },
            @else
            {data: '{{$field}}', name: '{{$field}}'},
            @endif

            @endforeach
        ];

        options.columnDefs=[
            {
                "render": function ( data, type, row ) {
                    let _tpl = '';
                    @can('tender_index_delete')
                    _tpl = ['discarded','completed','cancelled'].indexOf(row.status)>-1 ? '' : '<a onClick="deleteServerside(this)" style="cursor:pointer" data-id="'+data+'" data-pr="'+row.number+' - '+row.title+'"><i class="fa fa-trash"></i></a>';
                    @endcan
                    return _tpl;
                },
                "className": 'text-center',
                "targets": 0
            },
            {
                "render": function ( data, type, row, dt ) {
                    var column = dt.settings.aoColumns[dt.col].data;
                    switch(column){
                        case 'title':
                        case 'tender_number':
                            return '<a href="{{ route("tender.list") }}/'+row.id+'">'+data+'</a>';
                        case 'status':
                            return row.status_text
                        case 'workflow_status':
                            return row.workflow_status_text
                        case 'submission_method':
                            return row.submission_method_text;
                        case 'evaluation_method':
                            return row.evaluation_method_text;
                        case 'tender_method':
                            return row.tender_method_text;
                        case 'winning_method':
                            return row.winning_method_text;
                        default:
                            return data;
                        break;
                    }
                },
                "targets": "_all"
            }
        ];
        options.initComplete= function () {
            var tr = document.createElement("tr");
            var api = this.api();
            $('#datatable_serverside thead th').each(function (id, el) {
                var th = document.createElement("th");
                $(th).addClass('th-filter-column');
                var title = $(this).text();
                // if (id == $('#datatable_serverside thead th').length - 1) {
                if (id == 0) {
                } else {
                    let field = $(el).attr('id');
                    switch(field){
                        case 'tender_method' :
                            let tenderMethod = $('<select><option value="">All</option></select>');
                            @foreach ($tenderMethod as $k => $v)
                            tenderMethod.append($("<option/>", {value: '{{$k}}',text: "{{__('tender.'.$v)}}" }));
                            @endforeach

                            tenderMethod
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("change", function () {
                                    table.column(id).search(this.value).draw();
                            });
                            break;
                        case 'winning_method' :
                            let winningMethod = $('<select><option value="">All</option></select>');
                            @foreach ($winningMethod as $k => $v)
                            winningMethod.append($("<option/>", {value: '{{$k}}',text: "{{__('tender.'.$v)}}" }));
                            @endforeach

                            winningMethod
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("change", function () {
                                    table.column(id).search(this.value).draw();
                            });
                            break;
                        case 'submission_method' :
                            let submissionMethod = $('<select><option value="">All</option></select>');
                            @foreach ($submissionMethod as $k => $v)
                            submissionMethod.append($("<option/>", {value: '{{$k}}',text: "{{__('tender.'.$v)}}" }));
                            @endforeach

                            submissionMethod
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("change", function () {
                                    table.column(id).search(this.value).draw();
                            });
                            break;
                        case 'evaluation_method' :
                            let evaluationMethod = $('<select><option value="">All</option></select>');
                            @foreach ($evaluationMethod as $k => $v)
                            evaluationMethod.append($("<option/>", {value: '{{$k}}',text: "{{__('tender.'.$v)}}" }));
                            @endforeach

                            evaluationMethod
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("change", function () {
                                    table.column(id).search(this.value).draw();
                            });
                            break;
                        case 'workflow_status' :
                            let workflowStatus = $('<select><option value="">All</option></select>');
                            @foreach ($workflowStatus as $k => $v)
                            workflowStatus.append($("<option/>", {value: '{{$k}}',text: "{{__($v)}}" }));
                            @endforeach

                            workflowStatus
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("change", function () {
                                    table.column(id).search(this.value).draw();
                            });
                            break;
                        case 'status' :
                            let tenderStatus = $('<select><option value="">All</option></select>');
                            @foreach ($tenderStatus as $k => $v)
                            tenderStatus.append($("<option/>", {value: '{{$k}}',text: "{{__($v)}}" }));
                            @endforeach

                            tenderStatus
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("change", function () {
                                    table.column(id).search(this.value).draw();
                            });
                            break;
                        case 'created_at' :
                            let createdAt = $(
                                '<input type="text" id="f-created-at" class="form-control form-control-sm datetimepicker-input date"' +
                                ' name="f-created-at" data-toggle="datetimepicker" data-target="#f-created-at"  />'
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
                        case 'purchase_organization' :
                            let purchOrgs = $('<select><option value="">All</option></select>');
                            @foreach ($purchOrgs as $k => $v)
                            purchOrgs.append($("<option/>", {value: '{{$k}}',text: "{{__($v)}}" }));
                            @endforeach

                            purchOrgs
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("change", function () {
                                    table.column(id).search(this.value).draw();
                            });
                            break;
                        case 'internal_organization' :
                            let purchGroups = $('<select><option value="">All</option></select>');
                            @foreach ($purchGroups as $k => $v)
                            purchGroups.append($("<option/>", {value: '{{$k}}',text: "{{__($v)}}" }));
                            @endforeach

                            purchGroups
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("change", function () {
                                    table.column(id).search(this.value).draw();
                            });
                            break;
                        default :
                            $(document.createElement("input"))
                                .addClass('form-control form-control-sm')
                                .appendTo(th)
                                // .on("change", function () {
                                //     table.column(id).search(this.value).draw();
                                .on("keyup", function () {
                                    let SELF = this;
                                    if(searchTimeout != undefined) {
                                        clearTimeout(searchTimeout);
                                    }
                                    searchTimeout = setTimeout(function() {
                                        searchTimeout = undefined;
                                        table.column(id).search(SELF.value).draw();
                                    }, 500);

                                }
                            );
                            break;

                    }
                }
                $(th).appendTo($(tr));
            });
            $(tr).appendTo($('#datatable_serverside thead'));
        };
        // options.drawCallback = function( settings ) {
        // };

        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);

        $('#datatable_serverside tbody').on('click','.text-popup', function(e){
            e.preventDefault();
            $('.alert .close').click();
            showAlert($(this).text(), "info", -1);
        });

        //## Initialize Buttons
        $('#btn_delete_choices').click(function(){
            selectedRows = [];
            selectedData = [];
            table.rows().deselect();
        });
    });

    function deleteServerside(obj){
        let dtrow = table.row('#'+$(obj).data('id')).data();
        $('#delete_modal .modal-title').text("Delete "+dtrow['tender_number']);
        $('#delete_modal .modal-body').text("Are you sure to delete "+dtrow['tender_number']+"?");
        $('#btn_delete_modal').click();
        $('#delete_modal #btn_confirm').off('click').on('click', function () {
            $.ajax({
                type: 'DELETE',
                url: "{{ route('tender.list') }}/"+$(obj).data('id'),
            }).done(function(data) {
                showAlert("PR "+dtrow['tender_number']+" deleted.", "success", 3000);
                setTimeout(() => {
                    $('#delete_modal .close').click();
                    table.draw(false);
                }, 1000);
            });
            return false;
        });
    }
</script>
@endsection
