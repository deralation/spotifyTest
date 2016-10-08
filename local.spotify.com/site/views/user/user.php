<?php  
require_once(__DIR__.'/../../config/config.php');
$api = new SpotifyWebAPI\SpotifyWebAPI();

$user = new User();
$me = $api->me();
$array = json_decode(json_encode($me), True);
var_dump($api); exit();

$member = array();

if(isset($array)){
    $member["spotifyID"] = $array["id"];
    $member["displayName"] = $array["display_name"];
    $member["accessToken"] = $array["accessToken"];
    $member["creationDate"] = date("Y-m-d H:i:s");

    if($user->add($member))
        echo "Spotify User added to Database.";
}

?>