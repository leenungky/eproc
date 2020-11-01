<html>
    <head>

    </head>
<body>
<pre>
Dear Buyer {{ $vendor->vendor_type }}

Vendor performance of {{ $vendor->vendor_name }} has been evaluated. You can view the vendor performance result in E-Procurement system.
<a href="{{config('app.url')}}" target="_blank">(Click Here)</a>

Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
