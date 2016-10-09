<?php  
require_once(__DIR__.'/../../config/config.php');

$user = new User();
$user->addFilter("id","10");
$me = $user->get();

$playlist = new Playlist();
$playlists = $playlist->get();

foreach ($playlists as $list) {
	echo '<pre>';
	echo '<a href="">'.$list["name"].'<a>';
}

echo "<pre>";
var_dump($playlists); exit();


?>