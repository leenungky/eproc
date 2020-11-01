<html>
    <head>
        <style>
            body {
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size: 12pt;
                font-style: normal;
            }
        </style>
    </head>
<body>
    Dear {{ $vendor->company_name }},<br/><br/>

    <p>PT Timas Suplindo has invited you to participate in the following event:</p>
    <table>
        <tr>
            <td>Tender No</td><td>:</td><td>{{$tender->tender_number}}</td>
        </tr>
        <tr>
            <td>Tender Title</td><td>:</td><td>{{$tender->title}}</td>
        </tr>
        <tr>
            <td>Purchasing Organization</td><td>:</td><td>{{$tender->purchase_org_text}}</td>
        </tr>
        <tr>
            <td>Tender Method</td><td>:</td><td>{{ __('tender.'.$tender->tender_method_text)}}</td>
        </tr>
        <tr>
            <td>Bid Submission Method</td><td>:</td><td>{{__('tender.'.$tender->submission_method_text)}}</td>
        </tr>
    </table>
    <br/>

    <p>Kindly go to <a href="{{$linkTender}}">Click Here</a> to log in with your username and password. You will then have the option to register / accept the Tender.</p>
    <p>If you do not want to respond to this event, you will then have the option to reject the Tender invitation.</p>
    <p>If you have inquiries, please contact us at +6221 352 2828 for [Purchog_Onshore] / +6221 2992 1828 for [Purchog_Offshore] or email us at eproc@timas.com</p>
    <p>We look forward to working with you!</p><br/>

    <br/>
    <p>
        Regards, <br/>
        E-Procurement - {{ config('eproc.vendor_management.company_description') }}
    </p>
</body>
</html>
