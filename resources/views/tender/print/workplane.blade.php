@extends('tender.print.template_print')

@section('styles')
<style>
    .signature-box .box-header {
        width: 250px;
        border: 1px solid; padding: 5pt 5pt 2pt 5pt;
        height: 60px;
        margin-right: 20px;
    }
    .signature-box .box-body{
        border: 1px solid;border-top: none; padding: 5px;
        height: 100px;
        vertical-align: bottom;
    }
    .page-title{
        text-decoration: underline;
        margin-top: 0;
    }
    .content-title{
        text-decoration: underline;
    }
    @page{
        margin-top: 200px;
    }
    header {
        height: 200px;
        top: -120;
    }
</style>
@endsection
@section('header')
<span>{{$tender->purchase_organization}}</span>
<!-- Title -->
<h3 class="page-title">{{__('tender.tender_title_print')}}</h3>
<br />
<div class="header-right">
    <table class="tbl-detail" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="250px" class="bold">{{__('tender.tender_number_rf')}}</td>
                <td width="10px">:</td>
            <td>{{$tender->tender_number}}</td>
        </tr>
        <tr>
            <td class="bold">{{__('tender.tender_created_at')}}</td>
            <td>:</td>
            <td>{{ $tender->created_at ? \Carbon\Carbon::parse($tender->created_at)->format(\App\Models\BaseModel::DATE_FORMAT) : ''}}</td>
        </tr>
        {{-- <tr>
            <td class="bold">Perkiraan Nilai</td>
            <td>:</td>
            <td>-</td>
        </tr> --}}
        <tr>
            <td class="bold">{{__('tender.method')}}</td>
            <td>:</td>
            <td>{{__('tender.'.$tender->tender_method_value)}}</td>
        </tr>
    </table>
</div>
@endsection

@section('content')
@include('tender.print.workplane_info')
<div class="page-break"></div>
@include('tender.print.workplane_requirement')
<div class="page-break"></div>
@include('tender.print.workplane_vendor')
<div class="page-break"></div>
@include('tender.print.workplane_item')
@endsection
