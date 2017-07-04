<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @name LogPlugin
 * @author root
 * @desc 插件
 * @see http://php.net/manual/en/class.yaf-plugin-abstract.php
 */
class LogPlugin extends Yaf_Plugin_Abstract {

	public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        $post = $request->getPost();
        $get = $request->getQuery();
        $cookie = $_COOKIE;
        
        $requestStr = 'get:'.json_encode($get, JSON_UNESCAPED_UNICODE)."\n    ".'post:'.json_encode($post, JSON_UNESCAPED_UNICODE)."\n    ".'cookie:'.json_encode($cookie, JSON_UNESCAPED_UNICODE);
        
        $requestId = md5($requestStr.microtime(true));
        Yaf_Registry::set('request_id', $requestId);
        
        $requestStr = 'request_id:'.$requestId."\n    ".$requestStr."\n";
        log_message('all', $requestStr);
	}

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //echo '<p>routerShutdown</p>';
    }

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //echo '<p>dispatchLoopStartup</p>';
    }

    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //echo '<p>preDispatch</p>';
    }

    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //echo '<p>postDispatch</p>';
    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //echo '<p>dispatchLoopShutdown</p>';
    }
}
