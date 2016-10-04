<?php

require '../config/config.php';

use Phroute\Phroute\RouteCollector;

try {

	
	$router = new RouteCollector();


	// Homepage
	$router->any('/', function(){
	    return VIEWSPATH.'homepage/homepage.php';
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