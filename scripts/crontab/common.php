<?php
defined('APPLICATION_PATH') && exit('No direct script access allowed');

date_default_timezone_set('Asia/Shanghai');
mb_internal_encoding('UTF-8');
$app = new Yaf_Application(APPLICATION_PATH . '/conf/application.ini');
$app->bootstrap();

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
