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
        $conent = $exception->getTraceString();
        log_message('error', $conent);
        $this->getView()->assign('content', $conent);
    }
}
