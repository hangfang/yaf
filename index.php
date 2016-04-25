<?php

define('APPLICATION_PATH', dirname(__FILE__));
$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");
Yaf_Registry::set('app', $application);
$application->bootstrap()->run();
