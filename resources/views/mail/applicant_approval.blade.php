<html>
    <head>

    </head>
<body>
<pre>
Dear {{ $vendor->vendor_name }},

We are pleased to advise that you are now a {{ $vendor->registration_status }} of vendor's <b>{{ $vendor->purchasing_org }} - {{ $vendor->purchasing_org_description }}</b>. Herewith the login information :

<span style="margin-right: 20px;">User ID</span>: {{ $vendor->username }}
<span style="margin-right: 13px;">Password</span>: {{ $vendor->password }}

Kindly go to {{ route('main') }} to change your own password and complete your Company Profile. If you have inquiries, please contact us at at +6221 352 2828 for Purchase Organization Onshore / +6221 2992 1828  for Purchase Organization Offshore or email us at eproc@timas.com.


Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
