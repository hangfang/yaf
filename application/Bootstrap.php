<?php
defined('BASE_PATH') OR exit('No direct script access allowed');
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{
    public function _initHelpers(){
        Yaf_Loader::import( APPLICATION_PATH .'/application/helper/function.php' );
        Yaf_Loader::import( APPLICATION_PATH .'/application/helper/file.php' );
                
        set_error_handler(function($errno, $errstr, $errfile, $errline){
            log_message('error', 'error('. $errno .'): '.$errstr.' file:'.$errfile.' line '.$errline);
            lExit(json_encode(array('rtn'=>$errno+100000, 'error_msg'=>$errstr)));
        });
        
        $tmp = function($e){
            log_message('error', $e->getMessage() .'('. $e->getCode() .') at file: '. $e->getFile() .' in line: '. $e->getLine() ."\n");
            lExit(json_encode(array('rtn'=>$e->getCode()+10000, 'error_msg'=>$e->getMessage())));
        };
        set_exception_handler($tmp);
    }

    public function _initConfig() {
        //把配置保存起来
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);
        Yaf_Loader::import( APPLICATION_PATH .'/conf/constants.php' );
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        //注册一个插件
        !is_cli() && $dispatcher->registerPlugin(new LogPlugin());
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        //在这里注册自己的路由协议,默认使用简单路由
        $request = new Yaf_Request_Http();
        if(!is_cli()){
            if(strpos(strtolower($_SERVER['SERVER_NAME']), 'crm')===false){
                if(strtolower($request->module) === 'manage'){
                    lExit(json_encode($this->_error[3]));
                    return false;
                }
                
                $defaultModule = "Client";
            }else{
                if(strtolower($request->module) === 'client'){
                    lExit(json_encode($this->_error[3]));
                    return false;
                }
                
                $defaultModule = "Manage";
            }
            
            if(strpos($_SERVER['REQUEST_URI'], '?')!==false){
                $request_uri = explode('/', trim(strstr($_SERVER['REQUEST_URI'], '?', true), '/'));
            }else{
                $request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
            }
            
            if(empty($request_uri[0])){
                $request_uri = array();
            }

            switch(count($request_uri)){
                case 1:
                    $route = new Yaf_Route_Regex("#/([^/]+)#i", ['module'=>$defaultModule, 'controller'=>'index','action'=>'index']);
                    break;

                case 2:
                    $route = new Yaf_Route_Regex("#/([^/]+)/([^/]+)[/]{0,}#i", ['module'=>$defaultModule, 'controller'=>':first','action'=>':second'], [1=>'first', 2=>'second']);
                    break;

                case 3:
                    $route = new Yaf_Route_Regex("#/([^/]+)/([^/]+)/([^/]+)#i", ['module'=>':first', 'controller'=>':second','action'=>':third'], [1=>'first', 2=>'second', 3=>'third']);
                    break;

                default :
                    $route = new Yaf_Route_Regex("#.*#i", ['module'=>$defaultModule, 'controller'=>'index','action'=>'index']);
                    break;
            }
            $dispatcher->getRouter()->addRoute('route', $route);
        }
        
        //$dispatcher->setDefaultModule($defaultModule)->setDefaultController("Index")->setDefaultAction("index");
    }
    
    public function _initView(Yaf_Dispatcher $dispatcher){
            //在这里注册自己的view控制器，例如smarty,firekylin
    }
}
