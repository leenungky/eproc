@php
    $sign1 = $signatures->count() > 0 ? $signatures->get(0) : null;
    $sign2 = $signatures->count() > 1 ? $signatures->get(1) : null;
@endphp
<main>
    <div class="content">
        <div>
            <p>{{ __('tender.print.detail_item_info')}}</p>
        </div>
        <table class="tbl1" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 9pt;">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">{{ __('homepage.vendor_code')}}</th>
                    <th style="width: 275px;">{{ __('homepage.vendor_name')}}</th>
                    <th>Domisili Vendor</th>
                    <th style="width: 100px;">{{ __('homepage.vendor_type')}}</th>
                    <th>SOS Code</th>
                    {{-- <th>State</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($proposedVendors->data as $k => $v)
                    <tr>
                        <td style="width: 10%; text-align: center;">{{$k+1}}</td>
                        <td>{{$v->vendor_code}}</td>
                        <td>{{$v->vendor_name}}</td>
                        <td>{{$v->country}}</td>
                        <td>{{ucfirst($v->vendor_group)}}</td>
                        <td>{{$v->scope_of_supply}}</td>
                        {{-- <td>{{ __('tender.process_status.'.$v->status)}}</td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br/>
        <br/>
        <br/>
        <!-- signature -->
        <table class="no-space" width="auto" >
            <tr class="signature-box">
                <td class="box-header">
                    <span>Proposed By,</span>
                    <div>&nbsp;</div>
                    <span>Date </span>
                </td>
                <td style="width: 20pt;">&nbsp;</td>
                <td class="box-header">
                    <span>Approved By, </span>
                    <div>&nbsp;
                        {{-- <input style="margin-left: 0" type="checkbox" name="approved_yes" /> Setuju
                        <input type="checkbox" name="approved_no" /> Tidak Setuju --}}
                    </div>
                    <span>Date </span>
                </td>
            </tr>
            <tr class="signature-box">
                <td class="box-body">
                    <span>{{$sign1 && $sign1->sign_by ?  $sign1->sign_by : '-'}} </span><br/>
                    <span>{{$sign1 && $sign1->position? $sign1->position : '-'}} </span>
                </td>
                <td style="width: 20pt;">&nbsp;</td>
                <td class="box-body">
                    <span>{{$sign2 && $sign2->sign_by ? $sign2->sign_by : '-'}} </span><br/>
                    <span>{{$sign2 && $sign2->position? $sign2->position : '-'}} </span>
                </td>
            </tr>
        </table>
        <br/>
        {{-- <div><span>Catatan: </span></div>
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
        </div> --}}
    </div>
</main>
