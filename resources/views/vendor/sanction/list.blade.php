@extends('layouts.one_column')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">
        <i class="fa fa-list mr-2"></i>{{__('navigation.sanction')}}
    </span>
</div>
<div class="card-header-right">
    <a id="export-excel" class="btn btn-success btn-sm" href="{{ route('excel.sanctions') }}">Export to Excel</a>
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

            <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter table-ellipsis">
                <thead>
                    <tr>
                        {{-- <th>{{__('homepage.action')}}</th> --}}
                        @foreach ($fields as $key=>$field)
                        <th title="{{__('homepage.'.$field)}}" style="width:{{$fieldSizes[$key]}}px" id="th-{{$field}}" class="{{$field}}">{{__('homepage.'.$field)}}</th>
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

@section('modules-scripts')
@include('layouts.datatableoption')
<script>
var table;
require(["jquery","datatablesb4", "bootstrap-fileinput",'moment'], function () {
require(["datetimepicker","bootstrap-fileinput-fas"],function(){
    var sanctionTypes = {!!json_encode($sanctionTypes)!!};
    $(function(){
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
        options.autoWidth = 'false';
        options.ajax.url = "{{ route('vendor.sanction_data_list') }}";
        options.select=undefined;
        options.columns=[
            // {data: 'id', name: 'id'},
            @foreach ($fields as $field)
            {data: '{{$field}}', name: '{{$field}}'},
            @endforeach
        ];
        // options.order = [[1,'asc'],[0,'desc']];
        options.order = [[0,'asc']];
        options.columnDefs=[
            // {
            //     "visible": false,
            //     "targets": 0
            // },
            {
                "render": function ( data, type, row, dt ) {
                    var column = dt.settings.aoColumns[dt.col].data;
                    switch(column){
                        case 'letter_number':
                            return row.attachment ? `<a href="{{$storage}}/${row.profile.vendor_id}/${row.attachment}" target="_blank">${data}</a>` : data;
                        break;
                        case 'sanction_type':
                            let badge = 'badge-primary';
                            if(data=="RED") badge = 'badge-danger';
                            if(data=="YELLOW") badge = 'badge-warning';
                            if(data=="GREEN") badge = 'badge-success';
                            return '<span class="badge '+badge+'">'+data+' ('+sanctionTypes[data]+')</span>';
                        break;
                        case 'status':
                            let color = 'info';
                            switch(data){
                                case 'SUBMITTED': color = 'info'; break;
                                case 'REVISE': color = 'warning'; break;
                                case 'APPROVED': color = 'success'; break;
                            }
                            return '<span class="badge badge-'+color+'">'+data+'</span>';
                        break;
                        case 'vendor_name':
                        case 'vendor_code':
                            let html = '<a href="{{route("vendor.sanction")}}/detail/'+row.profile.vendor_id+'"><i></i>'+data+'</a>';
                            if(dt.row!=0){
                                if(dt.settings.aoData[dt.row-1]._aData[column]==data){
                                    html = '';
                                }
                            }
                            return html;
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
            }
            return th;
        };
        options.initComplete = CustomDTtOptions.InitComplete;

        //## Initilalize Datatables
        table = $('#datatable_serverside').DataTable(options);

    });
});
});
</script>
@endsection
