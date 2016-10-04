<?php

// Error Reporting
//error_reporting(0);		// Turn off all error reporting
//error_reporting(E_ERROR | E_WARNING | E_PARSE); // Report simple running errors
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); // Reporting E_NOTICE can be good too (to report uninitialized variables or catch variable name misspellings ...)
//error_reporting(E_ALL & ~E_NOTICE); // Report all errors except E_NOTICE
//error_reporting(E_ALL); // Report all PHP errors (see changelog)
//ini_set('display_errors', 1);

// PHP Settings
date_default_timezone_set('Europe/Istanbul');

// Server Differentiation & Database Confidentials

error_reporting(E_ALL); 
ini_set('display_errors', 1);
define('ENV','LOCAL');
define('MYSQLHOST','localhost');
define('MYSQLUSER','root');
define('MYSQLPASS','root');
define('MYSQLDB','spotify');
define('ROOTPATH',$_SERVER['DOCUMENT_ROOT'].'/');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
	define('ROOTURL',"https://local.spotify.com/");
} else {
	define('ROOTURL',"http://local.spotify.com/");
}
define('SECUREURL','https://local.spotify.com/');

// Paths and URLs
define('CLASSPATH',ROOTPATH.'classes/');
define('VIEWSPATH',ROOTPATH.'views/');
define('ENVIRONMENTPATH',ROOTPATH.'../environment/');
define('LOGSPATH',ROOTPATH.'../logs/');
define('SESSIONPATH',ROOTPATH.'../sessions');
define('VENDORPATH',ROOTPATH.'vendor/');
define('LIBRARIESPATH',ROOTPATH."libraries/");
define('LOCALEPATH',ROOTPATH.'locale/');
define('FILESPATH',ROOTPATH."files/");
define('MODULE','SITE');

define('LIBRARIESURL',ROOTURL."libraries/");

define('RECAPTCHASITEKEY','6LfFFSkTAAAAANL3RS_uQJ0Hghtvibnbf5BH-x--');
define('RECAPTCHASECRETKEY','6LfFFSkTAAAAALmdGpsjsorh5Tumvrh9zAgc1zQr');

define('SPOTIFYCLIENTID','37472290043b4bf78bc452f5febb2580');
define('SPOTIFYCLIENTSECRET','23f87d44870a4c1e9898dc05a66cd950');

// Benchmarking
if(ENV!="PRODUCTION") {
	require_once VENDORPATH.'devster/ubench/src/Ubench.php';
	$bench = new Ubench;
	$bench->start();
} 

// Start Session
//session_save_path(SESSIONPATH);
//session_start();

// Functios for General Usage
require 'functions.php';

/*// Autoload
//require_once(CLASSPATH."Autoloader.php");
function __autoload($cn) {
	echo "here";
  require(VENDORPATH.str_replace('\\','/',$cn).'.php');
};*/

//use \contentmankit;

// Vendors
require_once(ROOTPATH.'vendor/autoload.php');

function Loader($path = null) {
	if($path==null) $path = CLASSPATH;
    $loaderFunction = create_function('$class', 'include  "' . $path . '$class.php";');
    spl_autoload_register($loaderFunction);
}

Loader();

if(ENV!="LOCAL") {
	// Error Logging using Sentry
	if(ENV=="LOCAL")
		$logger = new Raven_Client('https://9e6894f104f54238b5cf1cd693143302:11eb7fbaa57b4b38a244a8ba38369ac7@app.getsentry.com/88342');
	else 
		$logger = new Raven_Client('https://d6e9b4cde57d417980d43ab75898130e:b608f336eca8447fa44e064be62d7383@app.getsentry.com/88341');
	//$logger->captureMessage('Hello world!'); // Test

	// Set Default Error Handler As Sentry
	$errorHandler = new Raven_ErrorHandler($logger);
	$errorHandler->registerExceptionHandler();
	$errorHandler->registerErrorHandler();
	$errorHandler->registerShutdownFunction();

	// Use Catcher to catch all PHP errors
	$catcher = new UniversalErrorCatcher_Catcher();
	$catcher->registerCallback(function(Exception $e) use ($logger) {
	    $logger->captureException($e);
	});
	$catcher->start();
}

// Utilities
$utility = new Utility();
$utility->setDebug(false); 

$setting = new Setting(); 

// MySQL Connection
$database = new MySQL();
$database->setHost(MYSQLHOST);
$database->setUser(MYSQLUSER);
$database->setPassword(MYSQLPASS);
$database->setDatabase(MYSQLDB);

// Activity
$activity = new Activity();

// Manuel Classes Loader
function useClasses($classes) {
	$classesArray = explode(",",$classes);
	foreach($classesArray as $c) {
		include_once CLASSPATH.$c.".php";
	}
}

// CMK Access
$scriptPath = explode("/",str_replace(ROOTPATH,"",$_SERVER["SCRIPT_FILENAME"]));
//echo "hoppa2"; exit();
//var_dump($scriptPath); exit();
if(count($scriptPath)>0) {
	if($scriptPath[0]=="panel")
		textdomain("panel");
	if($scriptPath[0]=="views")
		textdomain("site");
	if(!isset($scriptPath[1]))
		header("Location: ".CMKURL."dashboard/dashboard.php");
	if($scriptPath[0]=="panel" && $scriptPath[2]!="signin.php" && $user->signedIn==false)
		header("Location: ".CMKURL."users/signin.php?redirect=".urlencode($_SERVER["REQUEST_URI"]));
}


//throw new ExceptionLogger("check one");

?>