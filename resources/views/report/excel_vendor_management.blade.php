<!DOCTYPE html>
<html>

<head>
	<title>{{$title}}</title>
</head>
<body>
<div class="container">
    <table>
        <tr><td style="font-size:15px;font-weight:bold">{{$title}}</td></tr>
        <tr><td></td></tr>
        <tr><th style="font-weight:bold">Date:</th><td>{{date('d.m.Y H:i:s')}}</td></tr>
        <tr><th style="font-weight:bold">Prepared By:</th><td>{{$user}}</td></tr>
        <tr><td></td></tr>
    </table>
    <table style="border: 1px solid black;">
        <thead>
            <tr>
            @foreach($fields as $field)
                <th style="font-weight:bold; border: 1px solid black;width:20px;background-color:#1e3c72;color:#ffffff">{{__('homepage.'.$field)}}</th>
            @endforeach
            </tr>
        </thead>
        <tbody>
            @php $odd = true; @endphp
            @foreach($rows as $row)
            @php $odd = !$odd; @endphp
            <tr>
                @foreach($fields as $field)
                <td style="border: 1px solid black;background-color:{{$odd?'#eeeeee':'#ffffff'}}">{{$row->$field}}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>