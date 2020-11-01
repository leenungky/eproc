<html>
    <head>

    </head>
<body>
<pre>
Dear Admin Vendor {{ $vendor->purchasing_org }} - {{ $vendor->purchasing_org_description }},

Kindly to revise sanction application <b>{{ $vendor->vendor_name }} ({{ $vendor->vendor_type }})</b> in eProcurement system.
<a href="{{config('app.url')}}" target="_blank">(Click Here)</a>


Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
