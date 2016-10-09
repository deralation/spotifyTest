<?php 
require_once(__DIR__.'/../../config/config.php');

echo "<pre>";

$session = new SpotifyWebAPI\Session(SPOTIFYCLIENTID, SPOTIFYCLIENTSECRET, 'http://local.spotify.com/views/tracks/getTracks.php');

$api = new SpotifyWebAPI\SpotifyWebAPI();

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $session->getAccessToken();
    $api->setAccessToken($session->getAccessToken());
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

$me = $api->me();
$array = json_decode(json_encode($me), True);

$track = new Track();
$playlist = new Playlist();
$playlists = $playlist->getOne("289");

var_dump($playlists);

$tracks = array();


$tracksRequest = $api->getUserPlaylistTracks($array["id"], $playlists["playlistID"]);

foreach ($tracksRequest->items as $value) {
    $trackName = $value->track;
    $tracks["trackName"] = $trackName->name;      
    $tracks["playlistID"] = $playlists["id"];
    $tracks["creationDate"] = date("Y-m-d H:i:s");

    foreach ($trackName->artists as $k) {
        $tracks["artistName"] = $k->name;
    }

    var_dump($tracks);

    if($track->add($tracks))
        echo "Spotify Playlists Track added to Databases.";
}

?>