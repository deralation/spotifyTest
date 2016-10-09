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

?>