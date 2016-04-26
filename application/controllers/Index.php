<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Yaf_Controller_Abstract {
    public $_msg_stock = <<<EOF
<p class="weui_media_desc blue">%s</p>
<p class="weui_media_desc">股票代码: %s</p>
<p class="weui_media_desc">日期: %s</p>
<p class="weui_media_desc">时间: %s</p>
<p class="weui_media_desc">开盘价: %s元</p>
<p class="weui_media_desc">收盘价: %s元</p>
<p class="weui_media_desc">当前价格: %s元</p>
<p class="weui_media_desc">最高价: %s元</p>
<p class="weui_media_desc">最低价: %s元</p>
<p class="weui_media_desc">买一报价: %s元</p>
<p class="weui_media_desc">卖一报价: %s元</p>
<p class="weui_media_desc">成交量: %s万手</p>
<p class="weui_media_desc">成交额: %s亿</p>
<p class="weui_media_desc">涨幅: %s</p>
<p class="weui_media_desc">买一: %s手  %s元</p>
<p class="weui_media_desc">买二: %s手  %s元</p>
<p class="weui_media_desc">买三: %s手  %s元</p>
<p class="weui_media_desc">买四: %s手  %s元</p>
<p class="weui_media_desc">买五: %s手  %s元</p>
<p class="weui_media_desc">卖一: %s手  %s元</p>
<p class="weui_media_desc">卖二: %s手  %s元</p>
<p class="weui_media_desc">卖三: %s手  %s元</p>
<p class="weui_media_desc">卖四: %s手  %s元</p>
<p class="weui_media_desc">卖五: %s手  %s元</p>
<p class="weui_media_desc">分时图: <img src="%s"/></p>
<p class="weui_media_desc">日K线: <img src="%s"/></p>
<p class="weui_media_desc">周K线: <img src="%s"/></p>
<p class="weui_media_desc">月K线: <img src="%s"/></p>

<p class="weui_media_desc blue">仅供参考，非投资依据。</p>
EOF;
    
    public $_msg_weather = <<<EOF
<p class="weui_media_desc">%s天气：</p>
<p class="weui_media_desc">    日期：%s</p>
<p class="weui_media_desc">    发布时间：%s</p>
<p class="weui_media_desc">    天气：%s</p>
<p class="weui_media_desc">    当前气温：%s℃</p>
<p class="weui_media_desc">    最高：%s℃</p>
<p class="weui_media_desc">    最低：%s℃</p>
<p class="weui_media_desc">    风向：%s</p>
<p class="weui_media_desc">    风力：%s</p>
<p class="weui_media_desc">    日出时间：%s</p>
<p class="weui_media_desc">    日落时间：%s</p>      
EOF;
    
    public $_msg_news = <<<EOF
<div class="container-fluid">
<!--<div class="hd">
<h1 class="page_title">资讯</h1>
</div>-->
<div class="bd">
<ul class="list-group">%s%s</ul>
</div>
</div>
EOF;

    public $_msg_news_banner = <<<EOF
<li class="list-group-item">
<a class="bg-wrapper" href="%s">
<img src="%s" class="carousel-inner img-responsive" />
<div class="banner">
<h5 class="font16">%s</h5>
</div>
</a>
</li>    
EOF;
    
    public $_msg_news_list = <<<EOF
<li class="list-group-item">
<a class="row" href="%s">
<div class="col-xs-9 no-new-line">
<div class="txt"><span>%s</span></div>
</div>
<div class="col-xs-3"><img src="%s" class="pull-right img"/></div>
</a>
</li>    
EOF;
    
    
	/** 
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/sample/index/index/index/name/root 的时候, 你就会发现不同
     */
	public function indexAction($name = "Stranger") {
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
        $data['title'] = '生活查询';
        $data['class'] = 'app';

        $this->getView()->assign('data', $data);
    }
}
