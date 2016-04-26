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
        switch($exception->getCode()) {
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_VIEW:
                log_message('error', $exception->getPrevious ());
                header( "location: /index/missing" );
                return false;
        }

        $conent = $exception->getTraceString();
        log_message('error', $conent);
        $this->getView()->assign('content', $conent);
    }
}
