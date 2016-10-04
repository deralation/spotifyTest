<?php

require '../config/config.php';

use Phroute\Phroute\RouteCollector;

try {

	
	$router = new RouteCollector();


	// Homepage
	$router->any('/', function(){
	    return VIEWSPATH.'homepage/homepage.php';
	});

	// Blogs
	$router->group(['prefix'=>'blog'], function($router){
		$router->any(['/', 'yenilikler'], function(){
			return VIEWSPATH.'blogs/posts.php';
		});
		$router->any(['/{alias:c},{post:i}','yenilik'], function($alias,$post){
			return array(VIEWSPATH.'blogs/post.php',get_defined_vars());	
		});
	});

	// Campaigns
	$router->group(['prefix'=>'firsatlar'], function($router){
		$router->any(['/', 'firsatlar_kampanyalar'], function(){
			return VIEWSPATH.'campaigns/campaigns.php';
		});
		$router->any(['/{alias:c},{campaign:i}','kampanya'], function($alias,$campaign){
			return array(VIEWSPATH.'campaigns/campaign.php',get_defined_vars());	
		});		
	});
	
	// Reservations
	$router->group(['prefix' => 'rezervasyonlar'], function($router){
		$router->any('/{reservation:i}', function($member){
		    header("Location: https://driveyoyo.com/uye/rezervasyonlar");
		    exit();
		});
		$router->any('/{reservation:a}/yorum', function($member){
			return array(VIEWSPATH.'reservations/feedback.php', get_defined_vars());
		});
	});

	// Vehicles
	$router->group(['prefix' => 'araclar'], function($router){
		$router->any(['/{alias:c}--{vehicle:i}','araclar'],function($alias,$vehicle){
			return array(VIEWSPATH.'vehicles/vehicle.php',get_defined_vars());
		});
	});

	// Members
	$router->group(['prefix' => 'uye'], function($router){
		$router->any(['/', 'uye_profil'], function(){
		    return VIEWSPATH.'members/member.php';
		});
		$router->any(['/giris', 'uye_giris'], function(){
		    return VIEWSPATH.'members/signin.php';
		});
		$router->any(['/cikis', 'uye_cikis'], function(){
		    return VIEWSPATH.'members/signout.php';
		});
		$router->any(['/kayit', 'uye_kayit'], function(){
		    return VIEWSPATH.'members/signup.php';
		});
		$router->any('/kayit/2', function(){
		    return VIEWSPATH.'members/signup-2.php';
		});
		$router->any('/kayit/3', function(){
		    return VIEWSPATH.'members/signup-3.php';
		});
		$router->any('/kayit/tamam', function(){
		    return VIEWSPATH.'members/welcome.php';
		});
		$router->any('/davet', function(){
		    return VIEWSPATH.'members/invite.php';
		});
		$router->any('/davet/{member:i}', function($member){
		    return array(VIEWSPATH.'members/invited.php',get_defined_vars());
		});
		$router->any('/duzeltme', function(){
		    return ROOTPATH.'custom/members/validation.php';
		});
		$router->any('/rezervasyonlar', function(){
			return VIEWSPATH.'members/reservations.php';
		});
		$router->any('/rezervasyonlar/gecmis', function(){
			return VIEWSPATH.'members/reservationsHistory.php';
		});
		$router->any('/rezervasyonlar/{reservation:i}/tarih', function($reservation){
			return array(VIEWSPATH.'reservations/reschedule.php',get_defined_vars());
		});
		$router->any('/rezervasyonlar/{reservation:i}/iptal', function($reservation){
			return array(VIEWSPATH.'reservations/cancel.php',get_defined_vars());
		});
		$router->any('/hesap', function(){
			return VIEWSPATH.'members/account.php';
		});
		$router->any('/odeme', function(){
			return VIEWSPATH.'members/finance.php';
		});
		$router->any('/odeme/krediler', function(){
			return VIEWSPATH.'members/credits.php';
		});
		$router->any('/odeme/gecmis', function(){
			return VIEWSPATH.'members/payments.php';
		});
		$router->any('/odeme/fis/{payment:i}',function($payment){
			return array(VIEWSPATH.'members/invoice.php',get_defined_vars());
		});		
		$router->any('/destek', function(){
			return VIEWSPATH.'members/support.php';
		});
		$router->any(['/yenileme', 'uye_yenileme'], function(){
		    return VIEWSPATH.'members/renew.php';
		});
		$router->any(['/iptal', 'uye_iptal'], function(){
		    return VIEWSPATH.'members/cancel.php';
		});
	});

	// Surveys
	$router->group(['prefix' => 'anketler'], function($router){
		$router->any(['/toyota/prius', 'custom_survey_prius'], function(){
		    return ROOTPATH.'custom/toyota/survey-prius.php';
		});
		$router->any(['/toyota/yaris', 'custom_survey_yaris'], function(){
		    return ROOTPATH.'custom/toyota/survey-yaris.php';
		});
		$router->any(['/hyundai', 'custom_survey_yaris'], function(){
		    return ROOTPATH.'custom/hyundai/survey.php';
		});
	});

	// Prepaid
	$router->group(['prefix' => 'ye-ye'], function($router){
		$router->any(['/', 'prepaid'], function(){
		    return VIEWSPATH.'prepaid/prepaid.php';
		});
		$router->any(['/al', 'prepaid_buy'], function(){
		    return VIEWSPATH.'members/prepaid.php';
		});
	});

	// Pages
	$router->any(['/hakkinda', 'sayfa_hakkinda'], function(){
	    return VIEWSPATH.'pages/about.php';
	});
	$router->any(['/sikca-sorulan-sorular', 'sayfa_sikca-sorulan-sorular'], function(){
	    return VIEWSPATH.'pages/faq.php';
	});
	$router->any(['/hizmetler', 'sayfa_hizmetler'], function(){
	    return VIEWSPATH.'pages/services.php';
	});
	$router->any(['/vale', 'sayfa_vale'], function(){
	    return VIEWSPATH.'pages/delivery.php';
	});
	$router->any(['/kariyer', 'sayfa_kariyer'], function(){
	    return VIEWSPATH.'pages/career.php';
	});
	$router->any(['/is-ortaklari', 'sayfa_is-ortaklari'], function(){
	    return VIEWSPATH.'pages/partners.php';
	});
	$router->any(['/iletisim', 'sayfa_iletisim'], function(){
	    return VIEWSPATH.'pages/contact.php';
	});
	$router->any(['/destek', 'sayfa_destek'], function(){
	    return VIEWSPATH.'pages/support.php';
	});
	$router->any(['/harita', 'sayfa_harita'], function(){
	    return VIEWSPATH.'pages/map.php';
	});
	$router->any(['/basin', 'sayfa_basin'], function(){
	    return VIEWSPATH.'pages/press.php';
	});
	$router->any(['/uyelik-sozlesmesi', 'sayfa_yelik-sozlesmesi'], function(){
	    return VIEWSPATH.'pages/useragreement.php';
	});

	$router->any(['/bodrum', 'custom_bodrum'], function(){
	    return ROOTPATH.'custom/bodrum/welcome.php';
	});

	// DISPATH THEM ALL
	// @todo cache getData result
	$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());
	$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

	if(is_array($response)) {
		if(isset($response[1]) && count($response[1])>0) {
			foreach($response[1] as $key=>$value) {
				$_REQUEST[$key] = $value;
			}
		}
		include $response[0];
	} else {
		include $response;
	}

} catch (Phroute\Phroute\Exception\HttpRouteNotFoundException $e) {
	include VIEWSPATH.'errors/404.php';
	exit();
} catch (Exception $e) {
	throw new ExceptionLogger($e->getMessage());
	stop($e->getMessage());
	exit();
}
?>