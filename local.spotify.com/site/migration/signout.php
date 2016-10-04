<?php
include '../config/config.php';
$member  = new Member();

$member->signOut();
header("Location: ".V2URL.'member/logout');

//if($member->signOut())
//header("Location: ".ROOTURL);
?>