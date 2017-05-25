<?php
defined('BASE_PATH') OR exit('No direct script access allowed');
/**
 * @name BaseController
 * @author fanghang@me.com
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class BaseController extends Yaf_Controller_Abstract {
    public function init(){
        if(!is_cli()){
            
        }else{
            Yaf_Dispatcher::getInstance()->autoRender(FALSE);
            $tmp = get_class_methods($this->_request->getControllerName().'Controller');
            $methods = array();
            foreach($tmp as $v){
                if(strtolower(substr($v, -6))==='action'){
                    $methods[] = strtolower(preg_replace('/(.*)action$/i', '\1', $v));
                }
            }

            $action = strtolower($this->_request->getActionName());
            if(!in_array($action, $methods)){
                exit("php: '". $action ."' is not a correct command.\n\nDid you mean one of these?\n\t".implode("\n\t", $methods)."\n");
            }
        }
    }
}