<!DOCTYPE html>
<html>

<head>
	<title>Export Laporan Excel Pada Laravel</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>
<body>

	<div class="container">
		<center>
			<h4>Export Laporan Excel Pada Laravel</h4>
			<h5><a target="_blank" href="https://www.malasngoding.com/">www.malasngoding.com</a></h5>
		</center>
		<table class='table table-bordered' border="1">
			<tbody>
		        <td style="background-color: #000000;color:#FFFFFF">{{$title}}</td><td>{{$content}}</td>
            </tbody>
        </table>
		
		<table class='table table-bordered' border="1">
			<thead>
				<tr>
					<th style="border:1px solid #000000">No</th>
					<th>Nama</th>
					<th>NIS</th>
					<th>Alamat</th>
				</tr>
			</thead>
			<tbody>
				
				<tr>
					<td class="cell">a</td>
					<td class="cell">b</td>
					<td class="cell">c</td>
					<td class="cell">d</td>
				</tr>
			</tbody>
		</table>
	</div>

</body>
</html>