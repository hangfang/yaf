<?php
define('APPLICATION_PATH', dirname(__FILE__));

$request = new Yaf_Request_Simple();
if(!$request->isCli()){
    exit('No direct script access allowed');
}

$app = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini");
$app->bootstrap();


global $argc, $argv;
if ($argc > 1) {
    $module = '';
    $uri = $argv [1];
    if (preg_match ( '/^[^?]*%/i', $uri )) {
        list ( $module, $uri ) = explode ( '%', $uri, 2 );
    }
    $module = strtolower ( $module );
    $modules = Yaf_Application::app ()->getModules ();
    if (in_array ( ucfirst ( $module ), $modules )) {
        $request->setModuleName ( $module );
    }
    if (false === strpos ( $uri, '?' )) {
        $args = array ();
    } else {
        list ( $uri, $args ) = explode ( '?', $uri, 2 );
        parse_str ( $args, $args );
    }
    foreach ( $args as $k => $v ) {
        $request->setParam ( $k, $v );
    }
    $request->setRequestUri ( $uri );
    if ($request->isRouted () && ! empty ( $uri )) {
        if (false !== strpos ( $uri, '/' )) {
            list ( $controller, $action ) = explode ( '/', $uri );
            $request->setActionName ( $action );
        } else {
            $controller = $uri;
        }
        $request->setControllerName ( ucfirst ( strtolower ( $controller ) ) );
    }
}

$app->getDispatcher()->throwException(false)->setErrorHandler(array('Error', ''))->dispatch($request);