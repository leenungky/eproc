<html>
    <head>

    </head>
<body>
<pre>
Dear {{ $vendor->vendor_name }},

The following is your company information:

Vendor ID: {{ $vendor->vendor_code }}
Company Name: {{ $vendor->vendor_name }}
Email: {{ $vendor->pic_email }}
Validity Start Date: {{ $vendor->valid_from_date }}
Validity End Date: {{ $vendor->valid_thru_date }}
Sanction Information: 
{{ $vendor->remarks }}

Your sanction type is <b>{{ $vendor->sanction_type }} ({{ $vendor->sanction_type_description }})</b> will expire on {{$vendor->valid_thru_date}}. Please notice your sanction type in eProcurement system.
<a href="{{config('app.url')}}" target="_blank">(Click Here)</a>


Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
