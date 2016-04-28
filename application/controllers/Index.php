<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/sample/index/index/index/name/root 的时候, 你就会发现不同
     */
	public function indexAction($name = "Stranger") {
        $email = new Email();

        $email->from('www@rbmax.com', 'WeApp管理员');
        $email->to('ever10@qq.com');
        $email->cc('296295780@qq.com');
        $email->bcc('532581736@qq.com');

        $email->subject('Email Test');
        $email->message('Testing the email class.');

        $email->send();
        $data = array();
        $data['title'] = 'WeApp首页';
        $data['class'] = 'app';
        
        $this->getView()->assign('data', $data);
	}
    
    public function demoAction(){
        $data = array();
        $data['title'] = '页面样例';
        $data['class'] = 'app';
        
        $this->getView()->assign('data', $data);
    }
    
    public function queryAction(){
        
        $data = array();
        $data['title'] = '便利';
        $data['class'] = 'app';
                
        $data['expressList'] = get_var_from_conf('kdniao');
        $this->getView()->assign('data', $data);
    }
    
    public function missingAction(){
        
        $data = array();
        $data['title'] = '页面丢失...';
        $request = new Yaf_Request_Http();
        $data['baseUrl'] = 'http://'.$request->getServer('HTTP_HOST');
        
        $this->getView()->assign('data', $data);
    }
}
