<html>
    <head>

    </head>
<body>
<pre>
Dear Admin Vendor {{ $vendor->purchasing_org }} - {{ $vendor->purchasing_org_description }},

You have a company profile data that requires verification in the eProcurement system for
<b>{{ $vendor->vendor_name }}</b>

Please click below link to verify the company profile data.
<a href="{{config('app.url')}}" target="_blank">(Click Here)</a>

Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>