<html>
    <head>

    </head>
<body>
<pre>
Dear <b>{{ $vendor->vendor_name }}</b>,

We are pleased to advise that you are now a {{ $vendor->registration_status }} of vendor's {{ $vendor->purchasing_org }} - {{ $vendor->purchasing_org_description }}. 
Your account has now been included in the supplier database of {{ $vendor->company_type }} - {{ $vendor->company_description }}  

If you have inquiries, please contact us at at +6221 352 2828 for Purchase Organization Onshore / +6221 2992 1828  for Purchase Organization Offshore or email us at eproc@timas.com.

Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
