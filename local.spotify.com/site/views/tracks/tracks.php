<?php  
require_once(__DIR__.'/../../config/config.php');

$user = new User();
$user->addFilter("id","10");
$me = $user->get();

$track = new Track();
$tracks = $track->get();

foreach ($tracks as $songs) {
	echo '<pre>';
	echo $songs["trackName"];
}

echo "<pre>";
var_dump($tracks); exit();

?>