<?php
include '../config/config.php';
$command = new Command();
$commands = $command->get();

?><!DOCTYPE html>
<html lang="tr">
	<head>
	<meta charset="utf-8" />
	<script href="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-MfvZlkHCEqatNoGiOXveE8FIwMzZg4W85qfrfIFBfYc= sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
	</head>
	<body>
		<div class="container">
			<table class="table table-responsive table-striped table-bordered">
				<thead>
					<tr>
						<th>ID</th>
						<th>Araç</th>
						<th>SIM Kart</th>
						<th>Tarih</th>
						<th>Komut</th>
						<th>Veri</th>
						<th>Durum</th>
						<th>Yanıt</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(count($commands)>0) {
						foreach($commands as $c) {
							print '<tr>
									<td>'.$c["id"].'</td>
									<td>'.$c["vehicle"]["id"].'</td>
									<td>'.$c["vehicle"]["simCard"].'</td>
									<td>'.$c["requestDate"].'</td>
									<td>'.$c["name"].'</td>
									<td>'.json_encode($c["requestData"]).'</td>
									<td>'.$c["status"].'</td>
									<td>'.json_encode($c["responseData"]).'</td>
								</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</body>
</html>