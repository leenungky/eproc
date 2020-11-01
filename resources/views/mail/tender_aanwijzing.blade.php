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

    <p>PT Timas Suplindo has invited you to attend in the following event:</p>
    <table>
        <tr>
            <td>Event Name</td><td>:</td><td>{{$event->event_name}}</td>
        </tr>
        <tr>
            <td>Event Start</td><td>:</td><td>{{\Carbon\Carbon::parse($event->event_start)->format(\App\Models\BaseModel::DATETIME_FORMAT) }}</td>
        </tr>
        <tr>
            <td>Event End</td><td>:</td><td>{{ \Carbon\Carbon::parse($event->event_end)->format(\App\Models\BaseModel::DATETIME_FORMAT) }}</td>
        </tr>
        <tr>
            <td>Venue</td><td>:</td><td>{{$event->venue}}</td>
        </tr>
        <tr>
            <td>Note</td><td>:</td><td>{{$event->note}}</td>
        </tr>
    </table>
    <br/>

    <p>If you have inquiries, please contact us at {{$tender->purchase_org == 1 ? '+6221 352 2828' : '+6221 2992 1828'}} or email us at eproc@timas.com</p>

    <br/>
    <p>
        Regards, <br/>
        E-Procurement - {{ config('eproc.vendor_management.company_description') }}
    </p>
</body>
</html>
