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

// Server Differentiation
if(in_array($_SERVER["SERVER_ADDR"], array('172.31.9.49','52.17.147.65'))) {
	define('ENV','PRODUCTION');
	define('MYSQLHOST','database-1.cbaiq0rgufvi.eu-west-1.rds.amazonaws.com:3306');
	define('MYSQLUSER','sysdbadmin');
	define('MYSQLPASS','datahard13');
	define('MYSQLDB','yoyo_v2_prod');
	define('ROOTURL',"http://v3.driveyoyo.com/");
	define('ROOTPATH',$_SERVER['DOCUMENT_ROOT'].'/../');
	define('V2PATH',ROOTPATH.'../../yoyo_v2_prod/');
	define('V2URL','https://driveyoyo.com/');
	error_reporting(0);
} else if($_SERVER["SERVER_ADDR"]=="111.11.11.11") {
	define('ENV','DEVELOPMENT');
	throw new ExceptionLogger("Development environment configuration is not ready");
} else {
	define('ENV','LOCAL');
	define('MYSQLHOST','localhost');
	define('MYSQLUSER','root');
	define('MYSQLPASS','root');
	define('MYSQLDB','driveyoyo');
	define('ROOTURL',"http://local.driveyoyo.com/");
	define('ROOTPATH',$_SERVER['DOCUMENT_ROOT'].'/../');
	define('V2PATH',ROOTPATH.'../../yoyo-v2/');
	define('V2URL','https://yoyo_scarlett.local.host/');
	error_reporting(E_ALL); // Report all PHP errors (see changelog)
	ini_set('display_errors', 1);
}

// Database Confidentials


// Paths and URLs
define('CLASSPATH',ROOTPATH.'classes/');
define('ENVIRONMENTPATH',ROOTPATH.'../environment/');
define('SESSIONPATH',ROOTPATH.'../sessions');
define('VENDORPATH',ROOTPATH.'vendor/');
define('CMKINCLUDES',ROOTPATH.'panel/includes/');
define('INCLUDESPATH',ROOTPATH.'views/includes/');
define('LOCALEPATH',ROOTPATH.'locale/');
define('FILESPATH',ROOTPATH."files/");
define('MEDIAPATH',ROOTPATH."files/media/");


// Functios for General Usage
require ROOTPATH.'config/functions.php';

// Vendors
require ROOTPATH.'vendor/autoload.php';

function Loader($path = null) {
	if($path==null) $path = CLASSPATH;
    $loaderFunction = create_function('$class', 'include  "' . $path . '$class.php";');
    spl_autoload_register($loaderFunction);
}

Loader();

// Error Logging using Sentry
if(ENV=="LOCAL")
	$logger = new Raven_Client('https://42f218325d864bb981b55c19a67b92b2:4574917467d8422f87eaa0748b83e494@app.getsentry.com/44830');
else 
	$logger = new Raven_Client('https://b2ab7105b34346eb9e4c9afe100eecbf:b5c40e44b573412396d36177f13acd7e@app.getsentry.com/44828');
//$logger->captureMessage('Hello world!'); // Test

// Set Default Error Handler As Sentry
$errorHandler = new Raven_ErrorHandler($logger);
//set_error_handler(array($errorHandler, 'handleError'));
//set_exception_handler(array($errorHandler, 'handleException'));
$errorHandler->registerExceptionHandler();
$errorHandler->registerErrorHandler();
$errorHandler->registerShutdownFunction();

// Use Catcher to catch all PHP errors
$catcher = new UniversalErrorCatcher_Catcher();
$catcher->registerCallback(function(Exception $e) use ($logger) {
    $logger->captureException($e);
});
$catcher->start();

// Utilities
//include CLASSPATH.'Utility.php';
$utility = new Utility();
$utility->setDebug(false); 

// MySQL Connection
$database = new MySQL();
//$database->setDebug(true);
$database->setHost(MYSQLHOST);
$database->setUser(MYSQLUSER);
$database->setPassword(MYSQLPASS);
$database->setDatabase(MYSQLDB);

// Translation
/*$locale = "en_US";  // the locale you want
$localeDomain = "default"; // the domain you're using, this is the .PO/.MO file name without the extension
setlocale(LC_ALL, $locale); // activate the locale setting
setlocale(LC_TIME, $locale);
putenv("LANG=$locale");
$localeSource = LOCALEPATH.$locale."/LC_MESSAGES/".$localeDomain.".mo";
$localeChangeTime = filemtime($localeSource);
$localeFile = LOCALEPATH.$locale."/LC_MESSAGES/".$localeDomain."_".$localeChangeTime.".mo"; 
if(!file_exists($localeFile)) copy($localeSource,$localeFile);
$localeDomain = $localeDomain."_".$localeChangeTime;
bindtextdomain($localeDomain,LOCALEPATH);
textdomain($localeDomain);*/

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
	if(!isset($scriptPath[1]))
		header("Location: ".CMKURL."dashboard/dashboard.php");
	if($scriptPath[0]=="panel" && $scriptPath[2]!="signin.php" && $user->signedIn==false)
		header("Location: ".CMKURL."users/signin.php?redirect=".urlencode($_SERVER["REQUEST_URI"]));
}

//throw new ExceptionLogger("check one");

?>