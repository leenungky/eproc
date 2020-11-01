<html>
    <head>

    </head>
<body>
<pre>
Dear {{ $vendor->vendor_name }},

Thank you for registering in our E-Procurement system. Your registration application has been successfully submitted. Our team will review your company registration. Your current registration status is {{ $vendor->registration_status }} in <b>{{ $vendor->purchasing_org }} - {{ $vendor->purchasing_org_description }}</b>.

You will be notified when your application for Registration requires your attention or if application has been approved. 


Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}	        
</pre>
</body>
</html>
