@extends(config('eproc.theme')=='style2' ? (Auth::user() ? "layouts.one_column" : "layouts.frontpage") : "layouts.one_column")

@section('contentheader')
<i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ $title ?? '' }}
@endsection
@section('contentbody')
<div class="has-footer">
    <div class="card-fixed">
        @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
        @endif
        <table id="dt-tender-open"
            class="table table-bordered table-striped table-vcenter table-wrap row-border order-column table-sm table-wrap"
            style="width:100%"
            >
            <thead>
                <tr>
                    <th></th>
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
    var elmId = 'dt-tender-open';
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
            $("#dt-tender-open_paginate").appendTo($("#page_numbers"));
            $("#page_numbers").append('<input id="input-page" class="form-control form-control-sm" type="number" min="1" max="1000">')
            $("#dt-tender-open_info").css("padding", ".375rem .75rem").appendTo($("#page_numbers"));
            $('#input-page').keypress(function (event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    table.page($(this).val() * 1 - 1).draw(false);
                }
            });
        });
        let options = Object.assign(getDTOptions(), {
            select : undefined,
            initComplete : function() {
                var tr = document.createElement("tr");
                var api = this.api();
                // init filter
                $('#' + elmId + ' thead th').each(function (id, el) {
                    var th = $('<th class="th-filter-column"></th>');
                    var title = $(this).text();
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
                $(tr).appendTo($('#' + elmId + ' thead'));
            },
            columns : [
                {
                    data: 'id', name: 'id',
                    "render": function ( data, type, row ) {
                        @if($isVendor)
                            @if(!$vendor->profile->is_blacklisted)
                                @if($type == 'tender' || $type == 'tender_followed')
                                    if(row.tender_vendor_status == "{{\App\Models\TenderVendor::STATUS[1]}}"){
                                        return '<a href="" class="btn btn-sm btn-danger rejectRow" title="Register"><i class="fa fa-times"></i> {{__('common.reject')}}</a> '
                                        + '<a href="" class="btn btn-sm btn-success acceptRow" title="Register"><i class="fa fa-check"></i> {{__('common.accept')}}</a>';
                                    }else{
                                        let bClass = row.tender_vendor_status == "{{\App\Models\TenderVendor::STATUS[3]}}" ? 'danger' : 'success';
                                        return '<span class="badge badge-' + bClass + '">' + row.tender_vendor_status_text + '</span>';
                                    }
                                @elseif($type == 'open')
                                    if(row.tender_vendor_status == "{{\App\Models\TenderVendor::STATUS[4]}}"){
                                        return '<span class="badge badge-secondary">' + row.tender_vendor_status_text + '</span>';
                                    }else{
                                        return '<a class="btn btn-sm btn-light col-action registerRow" style="cursor:pointer" title="Register"><i class="fa fa-sign-in-alt"></i> {{__('common.register')}}</a>';
                                    }
                                @else
                                return '<a class="btn btn-sm btn-light col-action printRow" target="_blank" href="" title="Print"><i class="fa fa-file-pdf"></i> {{__('common.print')}}</a>';
                                @endif
                            @else
                            return '<a class="btn btn-sm btn-light col-action printRow" target="_blank" href="" title="Print"><i class="fa fa-file-pdf"></i> {{__('common.print')}}</a>';
                            @endif
                        @else
                        return '<a class="btn btn-sm btn-light col-action printRow" target="_blank" href="" title="Print"><i class="fa fa-file-pdf"></i> {{__('common.print')}}</a>';
                        @endif
                    },
                    "className": 'text-center',
                    'orderable': false,
                },
                @foreach ($fields as $field)
                {data: '{{$field}}', name: '{{$field}}'},
                @endforeach
            ],
            columnDefs : [
                {
                    "render": function ( data, type, row, dt ) {
                        var column = dt.settings.aoColumns[dt.col].data;
                        switch(column){
                            case 'title':
                            case 'tender_number':
                                @if($isVendor)
                                    return '<a class="table-col-link" href="{{ route("tender.list") }}/'+row.id+'">'+data+'</a>';
                                @else
                                    return data
                                @endif
                            case 'status':
                                return row.status_text;
                            case 'workflow_status':
                                return row.workflow_status_text;
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
                },
            ],
        });
        options.ajax.url = "{{ route('announcement.open.datatable', ['type' => $type]) }}";
        options.createdRow = function(row,data,index){
            if(data.submission_status && data.submission_status=='{{\App\Models\TenderVendorSubmission::STATUS[4]}}'){
                $(row).addClass("bg-warning");
            }
        }
        //## Initilalize Datatables
        table = $('#' + elmId).DataTable(options);

        // Action column
        $('#' + elmId + ' tbody').on('click','.acceptRow', function(e){
            e.preventDefault();
            let dtrow = table.row($(this).parents('tr')).data();
            $('#delete_modal .modal-title').text("{{__('common.confirmation')}}");
            $('#delete_modal .modal-body').text("Are you sure to {{__('common.accept')}} "+dtrow['tender_number']+" ?");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                sendAction(
                    "{{ route('announcement.saveVendorAction',['action'=> \App\Models\TenderVendor::STATUS[2]]) }}",
                    {
                        'id' : dtrow['id'],
                        'tender_number' : dtrow['tender_number'],
                        'vendor_id' : dtrow['vendor_id'],
                    }
                );
                return false;
            });
        });
        $('#' + elmId + ' tbody').on('click','.rejectRow', function(e){
            e.preventDefault();
            let dtrow = table.row($(this).parents('tr')).data();
            $('#delete_modal .modal-title').text("{{__('common.confirmation')}}");
            $('#delete_modal .modal-body').text("Are you sure to {{__('common.reject')}} "+dtrow['tender_number']+" ?");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                sendAction(
                    "{{ route('announcement.saveVendorAction',['action'=> \App\Models\TenderVendor::STATUS[3]]) }}",
                    {
                        'id' : dtrow['id'],
                        'tender_number' : dtrow['tender_number'],
                        'vendor_id' : dtrow['vendor_id'],
                    }
                );
                return false;
            });
        });
        $('#' + elmId + ' tbody').on('click','.registerRow', function(e){
            e.preventDefault();
            let dtrow = table.row($(this).parents('tr')).data();
            $('#delete_modal .modal-title').text("{{__('common.confirmation')}}");
            $('#delete_modal .modal-body').text("Are you sure to {{__('common.register')}} "+dtrow['tender_number']+" ?");
            $('#btn_delete_modal').click();
            $('#delete_modal #btn_confirm').off('click').on('click', function () {
                sendAction(
                    "{{ route('announcement.saveVendorAction',['action'=> \App\Models\TenderVendor::STATUS[4]]) }}",
                    {
                        'id' : dtrow['id'],
                        'tender_number' : dtrow['tender_number'],
                        'vendor_id' : {{$isVendor ? $vendor->id : 'null'}},
                    }
                );
                return false;
            });
        });
        $('#' + elmId + ' tbody').on('click','.printRow', function(e){
            let dtrow = table.row($(this).parents('tr')).data();
            let _url = "{{ route('announcement.print-tender', ['id' => 0]) }}";
            _url = _url.replace('0', dtrow.id);
            $(this).attr('href', _url);
        });

        //## Initialize Buttons
        $('#btn_delete_choices').click(function(){
            selectedRows = [];
            selectedData = [];
            table.rows().deselect();
        });

        sendAction = function(_url, params) {
            $.ajax({
                type: 'POST',
                url: _url,
                data: JSON.stringify(params),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function( xhr ) {
                    Loading.Show();
                }
            }).done(function(response) {
                if(response.success){
                    $('#delete_modal .close').click();
                    showAlert(params['tender_number']+" saved", "success", 3000);
                    table.ajax.reload();
                }else{
                    showAlert(params['tender_number']+" failed", "danger", 3000);
                }
            }).fail(defaultAjaxFail)
            .always(function(jqXHR, textStatus, errorThrown) {
                Loading.Hide();
            });
        };

    });
</script>
@endsection
