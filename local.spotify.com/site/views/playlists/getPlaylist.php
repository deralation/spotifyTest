<?php  
require_once(__DIR__.'/../../config/config.php');

$session = new SpotifyWebAPI\Session(SPOTIFYCLIENTID, SPOTIFYCLIENTSECRET, 'http://local.spotify.com/views/playlists/getPlaylist.php');

$api = new SpotifyWebAPI\SpotifyWebAPI();

echo "<pre>";

$user = new User();
$playlist = new Playlist();

$user->addFilter("id","79");
$me = $user->get();
var_dump($api);

$myPlaylists = $api->getUserPlaylists($me[0]["spotifyID"],array('limit'=>2));

var_dump($myPlaylists); exit();


?>