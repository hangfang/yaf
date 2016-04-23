<?php

define('APPLICATION_PATH', dirname(__FILE__));
$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");
$app->getDispatcher()->throwException(true)->bootstrap()->run();
