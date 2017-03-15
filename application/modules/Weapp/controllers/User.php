<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class UserController extends Yaf_Controller_Abstract{
   public function indexAction(){
        $data = array();
        $data['title'] = '个人中心';
        $data['class'] = 'user';
        
        $wechatModel = new WechatModel();
        $sigObj = $wechatModel->getJsApiSigObj();

        $data = array_merge($data, $sigObj);
        $this->getView()->assign('data', $data);
    }
    
}
