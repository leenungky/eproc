<html>
    <head>

    </head>
<body>
<pre>
Dear {{ $vendor->vendor_name }},

Your updated Company Profile has been successfully submitted. Your registration status has been changed become {{ __("homepage.$vendor->registration_status") }} <b>{{ $vendor->purchasing_org }} - {{ $vendor->purchasing_org_description }}</b>.

You will be notified when your application for Registration requires your attention or if application has been approved.


Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}	        
</pre>
</body>
</html>