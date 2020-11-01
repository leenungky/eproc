@extends('layouts.app')

@include('layouts.navigation')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">Outstanding List</h5>
        <button type="button" class="btn btn-md btn-success" onclick="location.href ='{{URL::to('/add-pic')}}';">Verify</button>
        <br />
        <br />
        <table id="datatable_serverside" class="table table-bordered table-striped table-vcenter">
            <thead>
                <tr>
                    <th class="text-center" style="width: 80px;">#</th>
                    <th>Partner Name</th>
                    <th>Partner Type</th>
                    <th>Purchasing Org</th>
                    <th>Province</th>
                    <th>Created Date</th>
                    <th>E-Mail</th>
                    <th>NPWP Number</th>
                    <th>PKP Number</th>
                    <th>Comments</th>
                    <th>Status</th>
                </tr>
            </thead>               
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).on("click", "#deletebutton", function () {
        var myId = $(this).data('todo').id;
        var myValue = $(this).data('todo').value;
        $(".modal-footer #deleted").val(myId);
        $(".modal-header #staticModalLabel").text("Remove " + myValue + " ?");
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $('#datatable_serverside').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: false,
        ajax: {
            url: "{{url('datatable-serverside-applicants')}}",
            type: "POST"
        },
        columns: [
            {data: null, sortable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'partner_name', name: 'partner_name'},
            {data: 'partner_type', name: 'partner_type'},            
            {data: 'purchasing_org', name: 'purchasing_org'},            
            {data: 'province', name: 'province'},            
            {data: 'created_date', name: 'created_date'},            
            {data: 'company_email', name: 'company_email'},            
            {data: 'npwp_number', name: 'npwp_number'},            
            {data: 'pkp_number', name: 'pkp_number'},            
            {data: 'comments', name: 'comments'},            
            {data: 'applicant_status', name: 'applicant_status'},            
        ]
    });
</script>
@endsection