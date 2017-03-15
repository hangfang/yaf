<?php
/**
 * 命令行请求入口
 * @demo : /usr/local/php7/bin/php cli.php request_uri="/weapp/jobnew/dlt/eventname/16047"
 * @Created by NetBeans.
 * @author: HangFang
 * @date: 2016-04-26
 */
define('BASE_PATH', dirname(__FILE__));

if (!extension_loaded("yaf"))
{
	include(BASE_PATH . '/framework/loader.php');
}
$application = new Yaf_Application(BASE_PATH . "/conf/application.ini");
$application->bootstrap()->getDispatcher()->dispatch(new Yaf_Request_Simple());
