<?php
if(isset($_REQUEST["e"]) && isset($_REQUEST["p"])) {

	include '../config/config.php';
	$member = new Member();

	if($member->signIn($_REQUEST["e"],$_REQUEST["p"])) {

		if(strtotime(date("Y-m-d H:i:s"))>strtotime("2015-12-30 00:00:00") && strtotime(date("Y-m-d H:i:s"))<strtotime("2015-12-31 23:59:59")) {
			$campaign = new Campaign();
			if(!$campaign->grantBenefits(12,$member->active["id"]))
				throw new ExpcetionLogger("Yeni yıl kampanyası tanımlanırken bir sorun oluştu.");
		}

		if(isset($_REQUEST["r"])) $r = urldecode($_REQUEST["r"]);
		else $r = "http://driveyoyo.com/";
		header("Location: ".$r);
		
	} else {
	
		header("Location: https://driveyoyo.com/member/login");
	
	}

} else {
	
	header("Location: https://driveyoyo.com/member/login");
	
}

exit();


?>