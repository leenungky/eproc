<html>
    <head>

    </head>
<body>
<pre>
Dear Procurement Manager {{ $vendor->purchasing_org }} - {{ $vendor->purchasing_org_description }},

You have a sanction application that requires verification in the eProcurement system for <b>{{ $vendor->vendor_name }} ({{ $vendor->vendor_type }})</b>.

Please click below link to verify the sanction application.
<a href="{{config('app.url')}}" target="_blank">(Click Here)</a>

Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
