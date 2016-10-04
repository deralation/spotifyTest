<?php
require_once(__DIR__.'/../../config/config.php');
	
try {

	$authorizationCode = "AQA09YoXFlZnk9oF5eKURIMhNf-DZbOID5E8ux4EedhhmuunGoD52kspVQyfeemh2zJfegIHW8RtGP-3uxbgVd1ovI0QmFvU0_2k9YJGd2gOJdGwaJtQeddOgbPCLvl4d_B2jicQF5hGBigwXwJv3ASVte-Julv2qAjue17lvJdiPzXuLnGdDPR92fP-1Q";
	
	$session = new SpotifyWebAPI\Session(SPOTIFYCLIENTID, SPOTIFYCLIENTSECRET, 'http://local.spotify.com/integrations/spotify/gettoken.php');

	$scopes = array(
	    'playlist-read-private',
	    'user-read-private'
	);

	$authorizeUrl = $session->getAuthorizeUrl(array(
	    'scope' => $scopes
	));

	header('Location: ' . $authorizeUrl);
	die();

} catch(Exception $e) {
	stop("Bakım çalışması yapılıyor. Kısa bir süre sonra web sitemiz yayında olacaktır.");
}

?>
