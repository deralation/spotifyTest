<?php
include 'config.php';
//var_dump($_POST);
if(!isset($_POST["key"]) || $_POST["key"]!="master13") {
	echo '<form method="post" action=""><input type="password" name="key" /><input type="submit" value="Start!" /></form>';
	exit();
}

$requiredKeys = array("initializationVector","encryptionKey");

foreach($requiredKeys as $k) {
	$database->select("SELECT * FROM Configs WHERE ConfigKey='".$k."'");
	$c = $database->getOne();
	if(!isset($c["ConfigID"])) {
		$database->run("INSERT INTO Configs (ConfigKey) VALUES ('".$k."')");
	}
}
	

if(isset($_POST["setupMembers"])) {
	$member = new Member();
	$result = $member->setupConfig();
	if($result) echo "Setup successful.";
	else echo "Setup failed: ".$member->getError();
}
?>

<form method="post" action="">
	<input type="hidden" name="key" value="<?php print $_POST["key"]; ?>" />
	<input type="hidden" name="setupMembers" value="now" />
	<input type="submit" value="Setup Members" />
</form>

?>