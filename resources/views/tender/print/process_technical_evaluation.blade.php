@extends('tender.print.template_print')

@php
    // dd($signatures);
    $sign1 = $signatures->count() > 0 ? $signatures->get(0) : null;
    $sign2 = $signatures->count() > 1 ? $signatures->get(1) : null;
@endphp
@section('styles')
<style>
    @page{
        margin-top: 150px;
    }
    header {
        height: 150px;
        top: -150px;
    }
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
    }
    .content-title{
        text-decoration: underline;
    }
</style>
@endsection
@section('header')
<!-- Title -->
<h3 class="page-title">Daftar Usulan Rekanan Pengadaan Barang dan Jasa</h3>
<br />
<div class="header-right">
    <table class="tbl-detail" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="250px" class="bold">No DUR / RF</td>
            <td width="10px">:</td>
        <td>{{$tender->tender_number}}</td>
        </tr>
        <tr>
            <td class="bold">Tanggal DUR dibuat</td>
            <td>:</td>
            <td>{{$tender->created_at}}</td>
        </tr>
        <tr>
            <td class="bold">Perkiraan Nilai</td>
            <td>:</td>
            <td>-</td>
        </tr>
        <tr>
            <td class="bold">Metode Pengadaan</td>
            <td>:</td>
            <td>{{__('tender.'.$tender->submission_method_text)}}</td>
        </tr>
    </table>
</div>
@endsection
<main>
    <div class="content">
        <div>
            <p>Detail barang dan jasa ada dilembar berikutnya</p>
        </div>
        <table class="tbl1" width="100%" cellpadding="0" cellspacing="0" border="0">
            <thead>
                <tr>
                    <th width="10%">No</th>
                    <th>Kode Vendor</th>
                    <th>Nama Vendor</th>
                    <th>Domisili Vendor</th>
                    <th>Vendor Type</th>
                    <th>SOS Code</th>
                    <th>State</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proposedVendors as $k => $v)
                    <tr>
                        <td style="width: 10%; text-align: center;">{{$k+1}}</td>
                        <td>{{$v->vendor_code}}</td>
                        <td>{{$v->vendor_name}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ __('tender.process_status.'.$v->status)}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p></p><br>&nbsp;<br>
        <!-- signature -->
        <table class="no-space" width="auto">
            <tr class="signature-box">
                <td class="box-header">
                    <span>Diusulkan Oleh,</span>
                    <div>&nbsp;</div>
                    <span>Tanggal </span>
                </td>
                <td style="width: 20pt;">&nbsp;</td>
                <td class="box-header">
                    <span>Menyetujui, </span>
                    <div>
                        <input style="margin-left: 0" type="checkbox" name="approved_yes" /> Setuju
                        <input type="checkbox" name="approved_no" /> Tidak Setuju
                    </div>
                    <span>Tanggal </span>
                </td>
            </tr>
            <tr class="signature-box">
                <td class="box-body">
                    <span>{{$sign1 && $sign1->sign_by ? 'Bp ' . $sign1->sign_by : '-'}} </span><br/>
                    <span>{{$sign1 && $sign1->position? $sign1->position : '-'}} </span>
                </td>
                <td style="width: 20pt;">&nbsp;</td>
                <td class="box-body">
                    <span>{{$sign2 && $sign2->sign_by ? 'Bp ' . $sign2->sign_by : '-'}} </span><br/>
                    <span>{{$sign2 && $sign2->position? $sign2->position : '-'}} </span>
                </td>
            </tr>
        </table>

        <br/>
        <div><span> Catatan: </span></div>
        <!-- foot note -->
        <div class="note" style="margin-top: 0; font-size: 9pt;">
            <table width="600px">
                <tr>
                    <td style="vertical-align: top">Persetujuan</td>
                    <td style="vertical-align: top; text-align: right">:</td>
                    <td>
                        <span style="padding-right:2%">Di atas 500 juta oleh direktur utama</span><br/>
                        <span style="padding-right:2%">Di atas 50 juta s/d 500 juta oleh direktur yang membawahi pelaksana pengadaan</span><br/>
                        <span style="padding-right:2%">Di atas 10 juta s/d 50 juta oleh GM yang membawahi pelaksana</span><br/>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</main>
@section('content')
@endsection
