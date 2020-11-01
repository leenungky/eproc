<html>
    <head>

    </head>
<body>
<pre>
Dear Buyer {{ $vendor->purchasing_organization_code }} - {{ $vendor->purchasing_organization }},

Kindly to revise vendor evaluation application <b>{{ $vendor->vendor_name }} ({{ $vendor->vendor_type }})</b> in eProcurement system.
<a href="{{config('app.url')}}" target="_blank">(Click Here)</a>

Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
