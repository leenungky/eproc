<html>
    <head>
        <style>
            body {
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size: 12pt;
                font-style: normal;
            }
        </style>
    </head>
<body>
    Dear {{ $nextApprover->sign_by }} - {{ $tender->purchase_org_text }},

    <p>{{$submission_method_name}} Approval <b>{{$tender->tender_number}} - {{$tender->title}}</b> has been approved by {{ $approver->sign_by }} - {{ $tender->purchase_org_text }}</p>

    <p>Please click below link to verify the Approval</p>
    <a href="{{$linkTender}}">Click Here</a>

    <br/>
    <p>
        Regards, <br/>
        E-Procurement - {{ config('eproc.vendor_management.company_description') }}
    </p>
</body>
</html>
