<html>
    <head>

    </head>
<body>
<pre>
Dear {{$vendor->company_name}},

{{$vendor->document_name}} {{$vendor->description}} on {{$vendor->document_validityend}}

Please click below link to update the document.
<a href="{{config('app.url')}}" target="_blank">(Click Here)</a>


Regards,
E-Procurement {{$vendor->companycode_description}}

</pre>
</body>
</html>
