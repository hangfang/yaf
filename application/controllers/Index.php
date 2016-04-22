<?php
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
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");

		//2. fetch model
		$model = new SampleModel();

		//3. assign
		$this->getView()->assign("content", $model->selectSample());
		$this->getView()->assign("name", $name);

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return TRUE;
	}
    
    public function demoAction(){
        //实例化表单对象，并传入需要验证的参数数组
        //其中键表示字段名
        $form = new \Forms\User\LoginModel($this->getRequest()->getParams());
        //调用表单对象的校验方法，该方法会根据字段设置校验所有字段
        if (!$form->validate()) {
            //校验失败，可以通过getMessages获取有错误字段的错误信息
            var_dump($form->getMessages());
            exit();
        }
        //表单校验通过，通过getFieldValue获取所有字段的值
        $params = $form->getFieldValue();
        var_dump($params);
    }
}
