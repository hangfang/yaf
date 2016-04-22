<?php

define('APPLICATION_PATH', dirname(__FILE__));
ini_set('yaf.environ', 'develop');
$application = new \Yaf\Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();
?>
