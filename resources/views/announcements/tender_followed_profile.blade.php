<table id="dt-tender-open"
    class="table table-bordered table-stripedtable-vcenter table-wrap row-border order-column table-sm"
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

@include('layouts.datatableoption')
<script type="text/javascript">
    var elmId = 'dt-tender-open';
    var table;
    require(["datatablesb4","dt.plugin.select","datetimepicker"], function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        let options = Object.assign(getDTOptions(), {
            select : undefined,
            initComplete : function() {
                var tr = document.createElement("tr");
                var api = this.api();
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
                            @if($type == 'tender')
                                if(row.tender_vendor_status == "{{\App\Models\TenderVendor::STATUS[1]}}"){
                                    return '<a href="" class="btn btn-danger rejectRow" title="Register"><i class="fa fa-times"></i> {{__('common.reject')}}</a> '
                                    + '<a href="" class="btn btn-success acceptRow" title="Register"><i class="fa fa-check"></i> {{__('common.accept')}}</a>';
                                }else{
                                    let bClass = row.tender_vendor_status == "{{\App\Models\TenderVendor::STATUS[3]}}" ? 'danger' : 'success';
                                    return '<span class="badge badge-' + bClass + '">' + row.tender_vendor_status_text + '</span>';
                                }
                            @elseif($type == 'open')
                                if(row.tender_vendor_status == "{{\App\Models\TenderVendor::STATUS[4]}}"){
                                    return '<span class="badge badge-secondary">' + row.tender_vendor_status_text + '</span>';
                                }else{
                                    return '<a class="btn btn-light col-action registerRow" style="cursor:pointer" title="Register"><i class="fa fa-sign-in-alt"></i> {{__('common.register')}}</a>';
                                }
                            @else
                            return '<a class="btn btn-light col-action printRow" style="cursor:pointer" title="Print"><i class="fa fa-print"></i> {{__('common.print')}}</a>';
                            @endif
                        @else
                        return '<a class="btn btn-light col-action printRow" style="cursor:pointer" title="Print"><i class="fa fa-print"></i> {{__('common.print')}}</a>';
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
                            break;
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
    });

</script>
