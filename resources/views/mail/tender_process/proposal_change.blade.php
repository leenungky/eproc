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

    <p>Tender Data {{$tender->tender_number.' - '.$tender->title}} has changed.</p>

    <p>To view the event, please access <a href="{{$linkTender}}">Click Here</a>. After you log on, view the Event details.</p>

    <p>You may contact {{ $updatedBy->email ?? '' }}. if you wish to inquire about the background for the decision, or to address any potential issues.</p>
    <br/>

    <br/>
    <p>
        Regards, <br/>
        E-Procurement - {{ config('eproc.vendor_management.company_description') }}
    </p>
</body>
</html>
