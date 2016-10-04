<?php
include '../config/config.php';

$command = new Command();
$vehicle = new Vehicle();
$vehicle->addFilter("id",array(405,406,407));
$vehicles = $vehicle->get();

if(isset($_REQUEST["vehicle"]) && isset($_REQUEST["command"])) {

	header('Content-Type: application/json');
	$response = array();

	if($_REQUEST["command"]=="setup") {
		if($command->setup($_REQUEST["vehicle"])) {
			$response["result"] = true;
			$response["data"] = $command->getSetupConfig();
		} else {
			$response["result"] = false;
			$response["message"] = "Command could not be sent: ".$command->getError();
		}
	} else {
		if($command->send($_REQUEST["vehicle"],$_REQUEST["command"],$_REQUEST)) {
			$response["result"] = true;
		} else {
			$response["result"] = false;
			$response["message"] = "Command could not be sent: ".$command->getError();
		}
	}

	echo json_encode($response);
	exit();

} else {
?><!DOCTYPE html>
	<html lang="tr">
		<head>
		<meta charset="utf-8" />
		<script href="https://code.jquery.com/jquery-2.1.4.min.js"></script>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-MfvZlkHCEqatNoGiOXveE8FIwMzZg4W85qfrfIFBfYc= sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
		</head>
		<body>
			<div class="container">
				<form action="" method="get">
					<fieldset>
						<legend>Number</legend>
						<div class="form-group">
							<select name="vehicle" class="form-control" required="required">
								<option>Araç</option>
								<?php
								foreach($vehicles as $v) {
									print '<option value="'.$v["id"].'">'.$v["name"].'</option>';
								}
								?>
							</select>
						</div>
					</fieldset>
					<fieldset>
						<legend>Command</legend>
						<div class="form-group">
						<select name="command" required="required" class="form-control">
							<option value="setup">Setup Config</option>
							<option value="doorStatus">Request Door Status</option>
							<option value="doorUnlock">Unlock Doors</option>
							<option value="doorLock">Lock Doors</option>
							<option value="cardStatus">Request Assigned Cards</option>
							<option value="cardAssign">Assign Card</option>
							<option value="cardEmpty">Remove All Assigned Cards</option>
							<option value="engineStatus">Request Engine Status</option>
							<option value="engineLock">Lock Engine</option>
							<option value="engineUnlock">Unlock Engine</option>
						</select>
						</div>
					</fieldset>
					<fieldset class="required-data required-data-cardAssign">
						<legend>Extra Data (only for Assign Card)</legend>
						<div class="form-group">
							<label>Driver RFID</label>
							<input type="text" name="driver" value="1A2B3C4D" maxlength="16" class="form-control" />
						</div>
						<div class="form-group">
							<label>Slot (1-5)</label>
							<input type="number" name="slot" value="1" min="1" max="5" maxlength="1" class="form-control" />
						</div>
					</fieldset>
					<fieldset>
						<input type="submit" value="Send Command"  class="btn btn-default" />
					</fieldset>
				</form>
			</div>

			<script>
				$(document).ready(function(e) {

				});
			</script>

		</body>
	</html>
<?php 
}
?>

