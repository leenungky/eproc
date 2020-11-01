@extends('layouts.one_column')

@section('contentheader')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="card-header-left">
    <span class="heading-title">
        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ __('homepage.applicant') }}
    </span>
</div>
<div class="card-header-right">
    <a id="export-excel" class="btn btn-success btn-sm" href="{{ route('excel.applicants') }}">Export to Excel</a>
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
        <table id="datatable_serverside" class="table table-bordered table-sm table-striped table-vcenter table-ellipsis" style="">
            <thead>
                <tr>
                    <th class="text-center" style="width: 20px;">#</th>
                    <th id="th-activity" style="width: 90px;" title="{{__('homepage.status')}}">{{ __('homepage.status') }}</th>
                    <th style="width: 300px;" title="{{__('homepage.partner_name')}}">{{ __('homepage.partner_name') }}</th>
                    <th style="width: 80px;" title="{{__('homepage.vendor_group')}}">{{ __('homepage.vendor_group') }}</th>
                    <th style="width: 80px;" title="{{__('homepage.company_type')}}">{{ __('homepage.company_type') }}</th>
                    <th style="width: 100px;" title="{{__('homepage.purchasing_organization')}}">{{ __('homepage.purchasing_organization') }}</th>
                    <th style="width: 150px;" title="{{__('homepage.province')}}">{{ __('homepage.province') }}</th>
                    <th style="width: 100px;" title="{{__('homepage.created_date')}}">{{ __('homepage.created_date') }}</th>
                    <th style="width: 150px;" title="{{__('homepage.email')}}">{{ __('homepage.email') }}</th>
                    <th style="width: 120px;" title="{{__('homepage.id_card_number')}}">{{ __('homepage.id_card_number') }}</th>
                    <th style="width: 120px;" title="{{__('homepage.npwp_tin_number')}}">{{ __('homepage.npwp_tin_number') }}</th>
                    <th style="width: 120px;" title="{{__('homepage.pkp_number')}}">{{ __('homepage.pkp_number') }}</th>
                    <th style="width: 120px;" title="{{__('homepage.non_pkp_number')}}">{{ __('homepage.non_pkp_number') }}</th>
                    <th style="width: 120px;" title="{{__('homepage.comments')}}">{{ __('homepage.comments') }}</th>
                    <!--<th>{{ __('homepage.action') }}</th>-->
                </tr>
            </thead>
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
            // scrollX: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            // searching: false,
            ajax: {
                url: "{{ route('applicant.data') }}",
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
                {data: 'activity', className: 'text-center',
                    render: function (data, type, row, meta) {
                        if(row.status == 'rejected'){
                            return `<span class="badge badge-danger">`+row.status +`</span>`;
                        } else {
                            return `<span class="badge badge-primary">`+row.activity +`</span>`;
                        }
                    }
                },
                {data: 'vendor_name', sortable: true, className: '',
                    render: function (data, type, row, meta) {
                        // return `
                        //     <div class="btn-group">
                        //         <form action="{{ route('applicant.profile') }}" method="POST">
                        //         @csrf
                        //         <input type="hidden" name="id" value="${row.id}">
                        //         <button type="submit" class="btn btn-sm btn-link" data-toggle="tooltip" style="text-align:justify" title="Show Profile">
                        //             ${row.vendor_name}
                        //         </button>
                        //         </form>
                        //     </div>
                        // `;
                        return `
                                <form action="{{ route('applicant.profile') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="${row.id}">
                                <button type="submit" class="btn btn-sm btn-link" data-toggle="tooltip" style="text-align:justify" title="Show Profile">
                                    ${row.vendor_name}
                                </button>
                                </form>
                        `;
                    }
                },
                {data: 'vendor_group', name: 'vendor_group', className: 'text-center '},
                {data: 'company_type', name: 'company_type', className: 'text-center '},
                {data: 'description', name: 'description'},
                {data: 'province', name: 'province'},
                {data: 'created_at', name: 'created_at', className: 'text-center '},
                {data: 'company_email', name: 'company_email', className: '',
                    render: function (data, type, row, meta) {
                        return data ? `<a href='mailto:${data}'>${data}</a>` : ``;
                    }
                },
                {data: 'idcard_number', name: 'idcard_number', className: 'text-center ',
                    render: function (data, type, row, meta) {
                        if(data) return `<a href="{{$storage}}/${row.id}/${row.idcard_attachment}" target="_blank">${data}</a>`;
                        else return '';
                    }
                },
                {data: 'tin_number', name: 'tin_number', className: 'text-center ',
                    render: function (data, type, row, meta) {
                        if(data) return `<a href="{{$storage}}/${row.id}/${row.tin_attachment}" target="_blank">${data}</a>`;
                        else return '';
                    }
                },
                {data: 'pkp_number', name: 'pkp_number', className: 'text-center ',
                    render: function (data, type, row, meta) {
                        if(data) return `<a href="{{$storage}}/${row.id}/${row.pkp_attachment}" target="_blank">${data}</a>`;
                        else return '';
                    }
                },
                {data: 'non_pkp_number', name: 'non_pkp_number', className: 'text-center '},
                {data: 'status', sortable: false,
                    render: function (data, type, row, meta) {
                        return '';
                    }
                },
            ],
            initComplete : CustomDTtOptions.InitComplete,
        });
    });
</script>
@endsection
