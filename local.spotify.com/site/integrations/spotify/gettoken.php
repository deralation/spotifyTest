<?php
require_once(__DIR__.'/../../config/config.php');

$session = new SpotifyWebAPI\Session(SPOTIFYCLIENTID, SPOTIFYCLIENTSECRET, 'http://local.spotify.com/integrations/spotify/gettoken.php');

$api = new SpotifyWebAPI\SpotifyWebAPI();
$user = new User();
$playlist = new Playlist();
$track = new Track();

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $accessToken = $session->getAccessToken();
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
$array["accessToken"] = $accessToken; 

$member = array();

if(isset($array)){
    $member["spotifyID"] = $array["id"];
    $member["displayName"] = $array["display_name"];
    $member["accessToken"] = $array["accessToken"];

    if($user->add($member))
        echo "Spotify User added to Database.";
}

$playlists = $api->getUserPlaylists($array["id"], array(
    'limit' => 5
));

$songLists = json_decode(json_encode($playlists), True);
$lists = array();

if(count($songLists)>0){
    foreach ($songLists["items"] as $key) {
        $lists["listID"] = $key["id"];
        $lists["name"] = $key["name"];

        if($playlist->add($lists))
            echo "Spotify Users Playlists are added to Database.";

         $tracksRequest = $api->getUserPlaylistTracks($array["id"], $key["id"]);
         $playlistTracks = json_decode(json_encode($tracksRequest), True);

         $tracks = array();

         foreach ($tracksRequest->items as $value) {
            $value = $value->track;

            $tracks["trackName"] = '<a href="' . $value->external_urls->spotify . '">' . $value->name . '</a> <br>';
            $tracks["playlistID"] = $key["id"];

            if($track->add($tracks))
                echo "Spotify Playlists Track added to Databases.";
         }
    }    
}

// Request a access token using the code from Spotify
/*$session->requestAccessToken($_GET['code']);
$accessToken = $session->getAccessToken();

var_dump($accessToken); exit();

// Set the access token on the API wrapper
$api->setAccessToken($accessToken);*/
?>