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
    Dear {{ $proposer->sign_by }} - {{ $tender->purchase_org_text }},

    <p>{{$submission_method_name}} Approval <b>{{$tender->tender_number}} - {{$tender->title}}</b> has been fully approved.</p>

    <p>Please click below link to view the Approval</p>
    <a href="{{$linkTender}}">Click Here</a>

    <br/>
    <p>
        Regards, <br/>
        E-Procurement - {{ config('eproc.vendor_management.company_description') }}
    </p>
</body>
</html>
