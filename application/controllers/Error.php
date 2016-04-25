<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author root
 */
class ErrorController extends Yaf_Controller_Abstract {
     
     public function html($exception) {
        $app = Yaf_Registry::get('app');
        switch ($app->getLastErrorNo()) {
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
            case YAF_ERR_NOTFOUND_VIEW:
                echo 404, ":", $app->getLastErrorMsg();
                break;
            default :
                echo 0, ":", $app->getLastErrorMsg();
                break;
        }
    }
    
    public function cli($exception) {
        $app = Yaf_Registry::get('app');
        switch ($app->getLastErrorNo()) {
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
            case YAF_ERR_NOTFOUND_VIEW:
                echo 404, ":", $app->getLastErrorMsg();
                break;
            default :
                echo 0, ":", $app->getLastErrorMsg();
                break;
        }
    }
}
