<?php
include '../config/config.php';
$state = new State();
$states = $state->get();

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
						<th>Vehicle</th>
						<th>SIM Card</th>
						<th>Date</th>
						<th>Latitude</th>
						<th>Longitude</th>
						<th>Speed</th>
						<th>Engine</th>
						<th>Doors</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(count($states)>0) {
						foreach($states as $c) {
							print '<tr>
									<td>'.$c["id"].'</td>
									<td>'.$c["vehicle"]["id"].'</td>
									<td>'.$c["vehicle"]["simCard"].'</td>
									<td>'.$c["date"].'</td>
									<td>'.$c["latitude"].'</td>
									<td>'.$c["longitude"].'</td>
									<td>'.$c["speed"].'</td>
									<td>'.$c["engineStatus"].'</td>
									<td>'.$c["doorStatus"].'</td>
								</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</body>
</html>