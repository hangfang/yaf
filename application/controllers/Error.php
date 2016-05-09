<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author root
 */
class ErrorController extends Yaf_Controller_Abstract {
     
    public function errorAction($exception){
        $request = new Yaf_Request_Http();
        switch($exception->getCode()) {
            case YAF_ERR_NOTFOUND_CONTROLLER:
                log_message('error', 'YAF_ERR_NOTFOUND_CONTROLLER: '. $request->getControllerName());
                header( "location: /index/missing" );
                return false;
            case YAF_ERR_NOTFOUND_ACTION:
                log_message('error', 'YAF_ERR_NOTFOUND_ACTION: '. $request->getActionName());
                header( "location: /index/missing" );
                return false;
            case YAF_ERR_NOTFOUND_MODULE:
                log_message('error', 'YAF_ERR_NOTFOUND_MODULE: '. $request->getModuleName());
                header( "location: /index/missing" );
                return false;
            case YAF_ERR_NOTFOUND_VIEW:
                log_message('error', 'YAF_ERR_NOTFOUND_VIEW: controller='. $request->getControllerName() .' action='. $request->getActionName());
                header( "location: /index/missing" );
                return false;
        }

        $conent = print_r($exception->getTrace(), true);
        log_message('error', $conent);
        header( "location: /index/broken" );
        return false;
    }
}
