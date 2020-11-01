@extends('layouts.one_column')

@section('contentheader')
<div class="card-header-left">
    <span class="heading-title">
        <i class="fa fa-list mr-2"></i>Legacy
    </span>
</div>
<div class="card-header-right">
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

        <div style="padding:2rem">
        <form name="frmLegacy" id="frmLegacy">
            <p>
            <b>SAP VendorCode:</b> <br>
            From:
            <input id="vendor_from" name="vendor_from" type="number" oninput="this.value=this.value.replace(/[^0-9]/g, '')">
            To:
            <input id="vendor_to" name="vendor_to" type="number" oninput="this.value=this.value.replace(/[^0-9]/g, '')"><br>
            </p>
            <p>
            <b>Purchasing Organization:</b><br>
            From:
            <input id="purchase_org_code_from" name="purchase_org_code_from" type="number" oninput="this.value=this.value.replace(/[^0-9]/g, '')">
            To:
            <input id="purchase_org_code_to" name="purchase_org_code_to" type="number" oninput="this.value=this.value.replace(/[^0-9]/g, '')"><br>
            </p>
            <p>
            <b>Number of migrated vendor:</b> <br>
            <input id="maxData" name="maxData" type="number" min="0" value="10"> <small>insert 0 to migrate all (Warning: choose 0 might takes a long time. Check the log file)</small><br>
            </p>
        </form>
        <button id="getLegacy">Get Legacy</button>
        </div>
        <table id="datatable_serverside" class="table table-sm table-bordered table-striped table-vcenter full-width">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">#</th>
                    <th>Business Partner</th>
                    <th>SAP Vendor</th>
                    <th>Candidate Code</th>
                    <th>Company Name</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection


@section('modules-scripts')
<script>
var table;
require(["jquery"], function () {
require(["datatablesb4"], function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#getLegacy').click(function(){
        var maxData = parseInt($('#maxData').val());
        $('#getLegacy').attr('disabled',true);
        $('#datatable_serverside tbody').html('');
        Loading.Show();
        $.ajax({
            type: 'GET',
            url: "{{ route('testlegacy') }}",
            data: $('#frmLegacy').serialize()
        }).done(function(data) {
            var res = JSON.parse(data);
            $('#getLegacy').attr('disabled',false);
            Loading.Hide();
            if(res.success){
                html = '';
                $.each(res.data, function(k,v){
                    html += `
                    <tr>
                        <td>${k+1}</td>
                        <td>${v.vendor.business_partner_code}</td>
                        <td>${v.vendor.sap_vendor_code}</td>
                        <td><a target="_blank" href="{{ route('candidate.list') }}/profile/${v.vendor.id}">${v.vendor.vendor_code}</a></td>
                        <td><a target="_blank" href="{{ route('candidate.list') }}/profile/${v.vendor.id}">${v.vendor.vendor_name}</a></td>
                    </tr>`;
                    $('#datatable_serverside tbody').html(html);
                });
                showAlert(res.message,'success',2000);
            }else{
                showAlert(res.message,'warning',2000);
            }
        }).fail(function() {
            $('#getLegacy').attr('disabled',false);
            Loading.Hide();
            alert("Error! Please contact system administrator.");
        });
    });
});
});
</script>
@endsection
@section('styles')
<style>
p{margin-bottom:.5rem}
</style>
@endsection
