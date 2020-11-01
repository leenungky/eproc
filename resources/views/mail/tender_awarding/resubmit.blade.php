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
    Dear {{ $vendor->company_name }},<br/><br/>

    <p>{{$tenderStage}} {{$tender->tender_number}} {{$tender->title}} has been updated.</p>
    <p>For details about this event, please access <a href="{{$linkTender}}">here</a>. After you log on, view the Event details.</p>
    <p>You may contact {{$buyer->email}}. If you wish to inquire about the background for the decision, or to address any potential issues.
    </p>

    <br/>
    <p>
        Regards, <br/>
        E-Procurement - {{ config('eproc.vendor_management.company_description') }}
    </p>
</body>
</html>
