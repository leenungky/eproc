@extends("layouts.one_column")

@section('contentheader')
<i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ $title ?? '' }}
@endsection

@section('contentbody')
<div class="has-footer" style="height:calc(100vh - 255px)">
<div class="card-fixed">
        @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
        @endif
        <table id="dt-tender-open"
            class="table table-bordered table-stripedtable-vcenter table-wrap row-border order-column table-sm"
            style="width:100%"
            >
            <thead>
                <tr>
                    <th></th>
                    @foreach ($fields as $field)
                    <th>{{__('tender.'.$field)}}</th>
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
    @php $footerText = config('eproc.footer_text') ?? '' @endphp
    @if($footerText!='')
        @include('layouts.footer_page')
    @endif
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
    require(["datatablesb4","dt.plugin.select"], function () {
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
                // init filter
                $('#' + elmId + ' thead th').each(function (id, el) {
                    var th = $('<th class="col-filter"></th>');
                    var title = $(this).text();
                    if (id == 0) {
                    } else {
                        $(document.createElement("input"))
                            //.attr("placeholder", title)
                            .addClass('form-control form-control-sm')
                            .appendTo(th)
                            .on("change", function () {
                                table.column(id).search(this.value).draw();
                        });
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
                            @if($type == 'tender' || $type == 'tender_followed')
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
                            return '<a class="btn btn-light col-action printRow" target="_blank" href="" title="Print"><i class="fa fa-file-pdf"></i> {{__('common.print')}}</a>';
                            @endif
                        @else
                        return '<a class="btn btn-light col-action printRow" target="_blank" href="" title="Print"><i class="fa fa-file-pdf"></i> {{__('common.print')}}</a>';
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
