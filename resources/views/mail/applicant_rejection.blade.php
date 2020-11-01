<html>
    <head>

    </head>
<body>
<pre>
Dear {{ $vendor->vendor_name }},

We acknowledge receipt of your application expressing your desire to be one of our Approved Vendor. However, we regret to inform you that you did not pass the minimum qualification requirements of the management.

Comments: 
{{ $vendor->comments }}

You may contact {{ $vendor->admin_onshore_email }} if you wish to inquire about the background for the decision, or to address any potential issues. 


Regards,
E-Procurement - {{ config('eproc.vendor_management.company_description') }}
</pre>
</body>
</html>
