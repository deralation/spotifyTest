<?php  
require_once(__DIR__.'/../../config/config.php');

echo "<pre>";

$session = new SpotifyWebAPI\Session(SPOTIFYCLIENTID, SPOTIFYCLIENTSECRET, 'http://local.spotify.com/views/user/user.php');

$api = new SpotifyWebAPI\SpotifyWebAPI();
$user = new User();

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

$member = array();

if(isset($array)){
    $member["spotifyID"] = $array["id"];
    $member["displayName"] = $array["display_name"];
    $member["accessToken"] = $accessToken;
    $member["creationDate"] = date("Y-m-d H:i:s");
    
    if($user->add($member))
        echo "Spotify User added to Database.";
}

?>