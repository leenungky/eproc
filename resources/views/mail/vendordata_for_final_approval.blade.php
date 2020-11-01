<html>
    <head>

    </head>
<body>
<pre>
Dear QMR {{ $vendor->purchasing_org }} - {{ $vendor->purchasing_org_description }},

Updated application <b>{{ $vendor->vendor_name }}</b> has been approved by Admin Vendor {{ $vendor->purchasing_org }} - {{ $vendor->purchasing_org_description }}

Kindly to verify that updated company profile data.
<a href="{{config('app.url')}}" target="_blank">(Click Here)</a>

Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
