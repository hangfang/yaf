<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * Yaf 自定义路由协议 Demo
 * 自定义继承 Yaf\Request_Abstract，为的是修改protected params参数
 */

class Router  extends Yaf_Request_Abstract implements Yaf_Route_Interface{

    /**
     * Route 实现，继承实现Yaf_Router_Interface route 
     * @access public 
     * @param  Object(Yaf_Request_Http) $req 默认参数
     * @return boole  true
     */

    public function route ($request){
        $request_uri = $request->getRequestUri();
        $router = get_var_from_conf('router');
        if(isset($router[$request_uri])){
            $request_uri =  $router[$request_uri];
            $request->setRequestUri($request_uri);
        }
        $request_uri = explode('/', trim($request_uri, '/'), 4);

        switch(count($request_uri)){
            case 0:
                $tmp = Yaf_Registry::get('defaultModule');
                $request->module = empty($tmp) ? 'index' : $tmp;
                $request->controller = 'index';
                $request->action = 'index';
                return true;
                
            case 1:
                $request->module = $request_uri[0];
                $request->controller = 'index';
                $request->action = 'index';
                return true;

            case 2:
                $request->module = $request_uri[0];
                $request->controller = $request_uri[1];
                $request->action = 'index';
                return true;

            case 3:
                $request->module = $request_uri[0];
                $request->controller = $request_uri[1];
                $request->action = $request_uri[2];
                return true;

            default :
                $request->module = $request_uri[0];
                $request->controller = $request_uri[1];
                $request->action = $request_uri[2];
                $params = explode('/', $request_uri[3]);
                
                $keyValue = [];
                for($i=0,$len=count($params);$i<$len;$i+=2){
                    $keyValue[$params[$i]] = isset($params[$i+1]) ? $params[$i+1] : NULL;
                }
                $request->params = $keyValue;
                return true;
        }
    }

    public function assemble (array $mvc, array $query = NULL){
        $route = get_var_from_conf('route');
        return true;
    }
}