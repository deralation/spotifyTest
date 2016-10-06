<?php
require_once(__DIR__.'/../../config/config.php');

$session = new SpotifyWebAPI\Session(SPOTIFYCLIENTID, SPOTIFYCLIENTSECRET, 'http://local.spotify.com/integrations/spotify/gettoken.php');

$api = new SpotifyWebAPI\SpotifyWebAPI();

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $accessToken = $session->getAccessToken();
    $api->setAccessToken($session->getAccessToken());
    var_dump($api); exit();
} else {
    header('Location: ' . $session->getAuthorizeUrl(array(
        'scope' => array(
            'user-follow-modify',
            'user-follow-read',
            'user-read-email',
            'user-read-private',
        )
    )));
    die();
}



/*$tracksRequest = $api->getUserPlaylistTracks($array["id"], $key["id"]);
$playlistTracks = json_decode(json_encode($tracksRequest), True);

$tracks = array();

foreach ($tracksRequest->items as $value) {
$value = $value->track;

$tracks["trackName"] = '<a href="' . $value->external_urls->spotify . '">' . $value->name . '</a> <br>';
$tracks["playlistID"] = $key["id"];

if($track->add($tracks))
    echo "Spotify Playlists Track added to Databases.";
}*/

// Request a access token using the code from Spotify
/*$session->requestAccessToken($_GET['code']);
$accessToken = $session->getAccessToken();

var_dump($accessToken); exit();

// Set the access token on the API wrapper
$api->setAccessToken($accessToken);*/
?>