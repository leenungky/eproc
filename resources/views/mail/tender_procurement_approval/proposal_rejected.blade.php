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

    <p>Kindly to revise your Tender Proposal <b>{{$tender->tender_number}} - {{$tender->title}}</b> with the comment as follows :</p>
    <p>Comments: {{ $approver->notes }}</p>

    <p><a href="{{$linkTender}}">Click Here</a> to login in E-Procurement system.</p>

    <br/>
    <p>
        Regards, <br/>
        E-Procurement - {{ config('eproc.vendor_management.company_description') }}
    </p>
</body>
</html>
