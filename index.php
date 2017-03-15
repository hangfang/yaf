<?php

define('BASE_PATH', dirname(__FILE__));
if (!extension_loaded("yaf"))
{
	include(BASE_PATH . '/framework/loader.php');
}

$application = new Yaf_Application( BASE_PATH . "/conf/application.ini");
Yaf_Registry::set('app', $application);
$application->bootstrap()->run();
