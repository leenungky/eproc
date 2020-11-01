<html>
    <head>

    </head>
<body>
<pre>
Dear {{ $vendor->vendor_name }},

We acknowledge receipt of your application expressing your desire to be one of our Approved Vendor. Kindly to revise your completed Company Profile with the comment as follows :

Comments: 
{{ $vendor->comments }}

You may contact <b>{{ $vendor->admin_onshore_email }} / {{ $vendor->admin_offshore_email }}</b> if you wish to inquire about the background for the decision, or to address any potential issues. 


Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
