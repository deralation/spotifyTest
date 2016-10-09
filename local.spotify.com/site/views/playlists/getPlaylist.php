<?php  
require_once(__DIR__.'/../../config/config.php');
echo "<pre>";

$session = new SpotifyWebAPI\Session(SPOTIFYCLIENTID, SPOTIFYCLIENTSECRET, 'http://local.spotify.com/views/playlists/getPlaylist.php');

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

$user = new User();
$user->addFilter("creationDate",date("Y-m-d"));
$currentUser = $user->get();

$playlist = new Playlist();

$me = $api->me();
$array = json_decode(json_encode($me), True);

$playlists = $api->getUserPlaylists($array["id"], array(
    'limit' => 5
));

$songLists = json_decode(json_encode($playlists), True);
$lists = array();

if(count($songLists)>0){
    foreach ($songLists["items"] as $key) {
        $lists["playlistID"] = $key["id"];
        $lists["name"] = $key["name"];
        $lists["userID"] = $currentUser[0]["id"];
        $lists["creationDate"] = date("Y-m-d H:i:s");

        if($playlist->add($lists))
            echo "Spotify Users Playlists are added to Database.";
    }
}

?>