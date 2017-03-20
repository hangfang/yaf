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
        Yaf_Loader::import( BASE_PATH .'/application/helper/function.php' );
        Yaf_Loader::import( BASE_PATH .'/application/helper/file.php' );
        
        set_error_handler(function($errno, $errstr, $errfile, $errline){
            log_message('error', 'error('. $errno .'): '.$errstr.' file:'.$errfile.' line '.$errline);
            exit(json_encode(array('rtn'=>501, '服务器内部错误')));
        });
        
        set_exception_handler(function($exception){
            log_message('error', 'error('. $exception->getCode() .'):'. $exception->getMessage());
            exit(json_encode(array('rtn'=>501, '服务器内部错误')));
        });
    }

    public function _initConfig() {
        //把配置保存起来
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);
        Yaf_Loader::import( BASE_PATH .'/conf/constants.php' );
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        //注册一个插件
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        //在这里注册自己的路由协议,默认使用简单路由
//        $route = new UserPlugin();
//        $dispatcher->registerPlugin($route);
    }
	
    public function _initName(Yaf_Dispatcher $dispatcher){
        $dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
    }

    public function _initView(Yaf_Dispatcher $dispatcher){
            //在这里注册自己的view控制器，例如smarty,firekylin
    }
}
