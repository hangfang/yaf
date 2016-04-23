<?php
/**
 * @name LotteryController
 * @author hangfang
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class LotteryController extends Yaf_Controller_Abstract {

	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/sample/index/index/index/name/root 的时候, 你就会发现不同
     */
	public function indexAction($name = "Stranger") {
		//1. fetch query
		$get = $this->getRequest()->getQuery("get", "default value");

		//2. fetch model
		$lottery = new LotteryModel();

		//3. assign
		$this->getView()->assign("content", $lottery->selectSample());
		$this->getView()->assign("name", $name);

		//4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return TRUE;
	}
    
    public function checkLotteryAction(){

        $lotteryType = $this->getRequest()->getQuery('lottery_type', 'ssq');
        
        
        $data = array();
        $request = $this->getRequest();
        $data['a'] = str_pad(intval($request->getQuery('a')), 2, 0, STR_PAD_LEFT);
        $data['b'] = str_pad(intval($request->getQuery('b')), 2, 0, STR_PAD_LEFT);
        $data['c'] = str_pad(intval($request->getQuery('c')), 2, 0, STR_PAD_LEFT);
        $request->getQuery('d') && $data['d'] = str_pad(intval($request->getQuery('d')), 2, 0, STR_PAD_LEFT);
        $request->getQuery('e') && $data['e'] = str_pad(intval($request->getQuery('e')), 2, 0, STR_PAD_LEFT);
        $request->getQuery('f') && $data['f'] = str_pad(intval($request->getQuery('f')), 2, 0, STR_PAD_LEFT);
        $request->getQuery('g') && $data['g'] = str_pad(intval($request->getQuery('g')), 2, 0, STR_PAD_LEFT);
        
        $opt = array();
        $lottery = new LotteryModel();
        $rt = $lottery->checkLottery($data, $lotteryType);
        
        $this->getView()->assign("rt", $rt);
    }
}
