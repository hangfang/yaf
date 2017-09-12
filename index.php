<?php

define('BASE_PATH', dirname(__FILE__));
define('PHP_ENV', ini_get('yaf.environ'));
define('SERVER_NAME', $_SERVER['HTTP_HOST']);
define('REQUEST_SCHEME', $_SERVER['HTTPS'] === "on" ? 'https' : 'http');

if (!extension_loaded("yaf"))
{
	include(BASE_PATH . '/framework/loader.php');
}

$application = new Yaf_Application( BASE_PATH . "/conf/application.ini");
Yaf_Registry::set('app', $application);
$application->bootstrap()->run();
