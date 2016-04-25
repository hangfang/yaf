<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{
    public function _initErrorAndExceptionHandler(){
        
        require APPLICATION_PATH .'/application/helper/function.php';
        require APPLICATION_PATH .'/application/helper/file.php';
        set_error_handler('_error_handler');
        set_exception_handler('_exception_handler');
    }

    public function _initConfig() {
		//把配置保存起来
		$arrConfig = Yaf_Application::app()->getConfig();
		Yaf_Registry::set('config', $arrConfig);
	}

	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		//注册一个插件
		$smarty = new Smarty_Adapter(null, Yaf_Registry::get('config')->get('smarty'));  
        Yaf_Dispatcher::getInstance()->setView($smarty)->disableView();//disableView作用是禁用yaf本身的模板引擎，否则会在smarty渲染完后，yaf在自动渲染一次，导致页面有重复
        $smarty->assign('environ', ini_get('yaf.environ'));
	}

	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
	}
	
	public function _initView(Yaf_Dispatcher $dispatcher){
		//在这里注册自己的view控制器，例如smarty,firekylin
	}
    
    public function _initName(Yaf_Dispatcher $dispatcher){
        $dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
    }
}
