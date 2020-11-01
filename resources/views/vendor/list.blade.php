@extends('layouts.one_column')

@section('contentheader')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="card-header-left">
    <span class="heading-title">
    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ __('homepage.vendor') }}
    </span>
</div>
<div class="card-header-right">
    <a id="export-excel" class="btn btn-success btn-sm" href="{{ route('excel.vendors') }}">Export to Excel</a>
</div>
@endsection

@section('contentbody')
@auth
<div class="has-footer">
    <div class="card-fixed">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        @endif
        <table id="datatable_serverside" class="table table-bordered table-sm table-striped table-vcenter table-ellipsis">
            <thead>
                <tr>
                    <th class="text-center" style="width: 20px;">#</th>
                    <th style="width: 300px;">{{ __('homepage.provider_name') }}</th>
                    <th style="width: 80px;">{{ __('homepage.company_type') }}</th>
                    <th style="width: 150px;">{{ __('homepage.province') }}</th>
                    <th style="width: 40px;">{{ __('homepage.register_number') }}</th>
                    <th style="width: 100px;">{{ __('homepage.created_date') }}</th>
                    <th style="width: 100px;">{{ __('homepage.updated_date') }}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endauth
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

@section('modules-scripts')
<script type="text/javascript">
    var table;
    require(["datatablesb4"], function () {
        //onload.
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

        table = $('#datatable_serverside').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            // searching: false,
            ajax: {
                url: "{{ route('vendor.data') }}",
                type: "POST",
                data : {
                    _token : $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                {data: null, sortable: false, className:'text-center',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'vendor_name', sortable: true, className: '',
                    render: function (data, type, row, meta) {
                        return `
                            <a href="{{ route('vendor.list') }}/profile/${row.id}" type="submit" class="btn btn-sm btn-link" data-toggle="tooltip" style="text-align:justify" title="Show Profile">
                                ${row.vendor_name}
                            </a>
                        `;
                    }
                },
                {data: 'company_type', name: 'company_type', className: 'text-center'},
                {data: 'province', name: 'province'},
                {data: 'vendor_code', name: 'vendor_code'},
                {data: 'created_at', name: 'created_at', className: 'text-center dt-nowrap'},
                {data: 'updated_at', name: 'updated_at', className: 'text-center dt-nowrap'},
            ],
            initComplete : CustomDTtOptions.InitComplete,
        });
    });
</script>
@endsection
