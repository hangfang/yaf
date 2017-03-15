<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class WechatController extends Yaf_Controller_Abstract {

    public function indexAction(){
        header('location: /');
    }
    
    private function valid()
    {
        $echoStr = $_GET["echostr"];        //随机字符串
        
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    
    private function checkSignature()
    {
        $signature = $_GET["signature"];    //微信加密签名
        $timestamp = $_GET["timestamp"];    //时间戳
        $nonce = $_GET["nonce"];            //随机数
        $token = WX_TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);      //进行字典序排序
        //sha1加密后与签名对比
        if( sha1(implode($tmpArr)) == $signature ){
            return true;
        }else{
            return false;
        }
    }
    
    public function messageAction(){
        //$this->valid();exit;//验证微信token
        $request = new Yaf_Request_Http();
        
        $timestamp  = $request->getQuery('timestamp', '');
        $nonce = $request->getQuery('nonce', '');
        $msgSignature  = $request->getQuery('msg_signature', '');
        $encryptType = $request->getQuery('encrypt_type','');
        
        $bak4log = $data = file_get_contents('php://input');
        
        if(ENCPRYPT_TYPE === 'aes'){
            $wxBizMsgCrypt = new Wechat_WXBizMsgCrypt(WX_TOKEN, WX_ENCODING_AES_KEY, WX_APP_ID);
            $res = $wxBizMsgCrypt->decryptMsg($msgSignature, $timestamp, $nonce, $data, $data);
            if($res !== 0){
                log_message('error', 'decrypt msg error, error code: '. $res ."\r\n msg content: ". $bak4log);
                echo '';
                exit;
            }
        }
        
        /**
         * 微信消息结构
         * <xml>
         *       <ToUserName><![CDATA[%s]]></ToUserName>
         *       <FromUserName><![CDATA[%s]]></FromUserName>
         *       <CreateTime>%s</CreateTime>
         *       <MsgType><![CDATA[%s]]></MsgType>
         *       <Content><![CDATA[%s]]></Content>
         *       <FuncFlag>0</FuncFlag>
         *   </xml>
         */
      	//extract xml data
		if (!empty($data)){
            libxml_disable_entity_loader(true);
            $msgXml = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            
            $msg = array();
            
            $msgformat = get_var_from_conf('msgformat');
            foreach($msgformat['receive_format'][$msgXml['MsgType']] as $_v){
                if($msgXml['MsgType']==='event'){
                    if(in_array($_v, array('EventKey', 'Latitude', 'Longitude', 'Precision'))){
                        empty($msgXml[$_v]) && $msg[$_v] = $msgXml[$_v] = '';
                    }
                }
                $msg[$_v] = isset($msgXml[$_v]) ? $msgXml[$_v] : '';
            }
            
            $wechatModel = new WechatModel();
            $suc = $wechatModel->saveMessage($msg);
            
            $msgtype = $msgXml['MsgType']==='event' ? $msgXml['MsgType'].Ucfirst(strtolower($msgXml['Event'])) : $msgXml['MsgType'];
            $this->$msgtype($msgXml);
        }
    }
    
    private function text($msgXml){
        
        $contents = $msgXml['Content'];
        
        $contents = trim(str_replace(array('，', ','), array(' ', ' '), $contents));
        $contents = explode(' ', $contents);
        
        $kdniao = get_var_from_conf('kdniao');
        $weather = get_var_from_conf('weather');
        $wechat = get_var_from_conf('wechat');
        $lottery = get_var_from_conf('lottery');
        switch(count($contents)){
            case 1:
                if(in_array($contents[0], array_keys($kdniao))){//快递公司
                    $this->getExpressCom($contents[0], $msgXml);
                }else if(in_array($contents[0], array_keys($weather))){//天气
                    $this->getWeather($weather[$contents[0]], $msgXml);
                }else if(in_array($contents[0], $wechat['daigou'])){//图文广告
                    $this->daigou($msgXml);
                }else if(in_array($contents[0], $wechat['at'])){//关注微信号
                    $this->hopeSubscribe($msgXml);
                }elseif(in_array($contents[0], $wechat['position'])){//提示发送位置信息
                    $this->tellMeYourPosition($msgXml);
                }elseif(preg_match('/^[\d]{6}$/i', $contents[0]) === 1){//股票代码
                    $this->getStock($stockid, $msgXml);
                }elseif(in_array($contents[0], $wechat['around'])){//上一条是位置信息
                    $this->searchAround($contents[0], $msgXml);
                }elseif($contents[0]==='快递'){
                    $this->getRecentExpress($msgXml);
                }elseif($contents[0]==='天气'){
                    $this->getRecentWeather($msgXml);
                }elseif(in_array($contents[0], $wechat['girl'])){
                    $this->getGirls($msgXml);
                }elseif(in_array($contents[0], array_keys($lottery))){
                    $this->getLottery($lottery[$contents[0]], $msgXml);
                }elseif(in_array($contents[0], $wechat['joke'])){
                    $this->getJoke($msgXml);
                }elseif(in_array($contents[0], $wechat['genlottery'])){
                    $this->genLottery($contents[0], $msgXml);
                }else{
                    $type = array('getNews', 'getSocials');
                    $funcName = $type[rand(0, 1)];
                    $this->$funcName($contents[0], $msgXml);
                }
            case 2:
                if(in_array($contents[0], array_keys($kdniao))){
                    $this->getExpress($kdniao[$contents[0]], $contents[1], $msgXml);
                }
            default :
                $this->complexedMessage($msgXml);
                break;
        }
    }
    
    private function image($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '抱歉，图片处理comming soon...';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function voice($msgXml){
        $msgXml['Content'] = trim($msgXml['Recognition'], '？！');
        if(strlen($msgXml['Content'])>0){
            $this->text($msgXml);
        }
        
        
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '正在接入语音机器人...';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function video($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '分享视频真的好吗？';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function shortvideo($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = 'Oh, I like it';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function location($msgXml){
//        
//        //查询腾讯地图
//        $rt = $this->PositionModel->getLocation($msgXml['Location_X'], $msgXml['Location_Y']);
//
//        if($rt['status'] === 0){
//
//            $data = $this->_send_format['text'];
//            $data['touser'] = $msgXml['FromUserName'];
//            $data['fromuser'] = $msgXml['ToUserName'];
//            $data['text']['content'] = sprintf($this->_msg_position, $rt['result']['address']);
//            $this->WechatModel->sendMessage($data);
//
//        }
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = sprintf($msgformat['msg_position'], $msgXml['Label']);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function link($msgXml){
        
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '等等，对，这链接有毒！';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }

    private function event($msgXml){
        $this->$msgXml['Event']($msgXml);
    }

    /**
    * 订阅的事件推送
    */
    private function eventSubscribe($msgXml){
        $wechatModel = new WechatModel();
        $rt = $wechatModel->subscribe($msgXml['FromUserName']);

        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        
        $request = new Yaf_Request_Http();
        $baseUrl = 'http://'. $request->getServer('HTTP_HOST');
        $data['text']['content'] = $rt==='new' ? sprintf($msgformat['msg_welcome_newbeing'], $baseUrl, $baseUrl, $baseUrl, $baseUrl) : sprintf($msgformat['msg_welcome_back'], $baseUrl, $baseUrl, $baseUrl, $baseUrl);
        $wechatModel->sendMessage($data);
    }

    /**
    * 取消订阅的事件推送
    */
    private function eventUnsubscribe($msgXml){
        $wechatModel = new WechatModel();
        $rt = $wechatModel->unsubscribe($msgXml['FromUserName']);
    }

    /**
    * 扫描二维码的事件推送
    */
    private function eventScan($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '扫描结果：'. $msgXml['EventKey'];
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }

    /**
    * 地理位置上报的事件推送（订阅号不支持）
    */
    private function eventLocation($msgXml){
        //$rt = $this->WechatModel->location($msgXml);

        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = $msgXml['FromUserName'] .'上报位置';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }

    /**
    * 点击菜单拉取消息的事件推送
    */
    private function eventClick($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = $msgXml['FromUserName'] .'点击菜单';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }

    /**
    * 点击菜单跳转链接时的事件推送
    */
    private function eventView($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = $msgXml['FromUserName'] .'菜单跳转';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function daigou($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['news'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];

        $data['news'] = $tmp = array();
        $tmp['title'] = '香港代购';
        $tmp['description'] = '#4月5日#今天才是小胖妹真正意义上的生日，也因为她，妈咪才走上#香港代购#这条不归路[偷笑]';
        $tmp['picurl'] = 'https://mmbiz.qlogo.cn/mmbiz/vacvmEeokHY8vfIeqTeF3rR8gGria7u8m0rzD2EoVDCpo64IjyDwkkxicN0pKNUwfHzjKmShsNBGMLicnPwTUAbJA/0?wx_fmt=jpeg';
        $tmp['url'] = 'http://mp.weixin.qq.com/s?__biz=MzI4NzIyMjQwNw==&mid=100000006&idx=1&sn=2f99b09162bba5902ac99acf99ef9659#rd';
        $data['articles'][] = $tmp;

        $data['article_count'] = count($data['news']);

        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function getExpressCom($msg, $msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '咳，终于找到“'. $msg .'”公司...';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function getStock($stockid, $msgXml){
        if(preg_match('/^6[\d]{5}$/i', $stockid) === 1){
            $stockid = 'sh'. $stockid;//上海
        }elseif(preg_match('/^0[\d]{5}|3[\d]{5}$/i', $stockid) === 1){
            $stockid = 'sz'. $stockid;//深圳
        }else{
            $stockid = $stockid;
        }

        $baiduModel = new BaiduModel();
        $data = $baiduModel->getStock($stockid, $msgXml);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function unrecognize($msg, $msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        
        
        $request = new Yaf_Request_Http();
        $baseUrl = 'http://'. $request->getServer('HTTP_HOST');
        $data['text']['content'] = sprintf($msgformat['msg_unrecognized'], $msg, $baseUrl, $baseUrl, $baseUrl, $baseUrl);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function hopeSubscribe($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '搜索“'. WX_HK_ACCOUNT .'”吧'."\n".'期待您的关注n(*≧▽≦*)n';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function tellMeYourPosition($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '爽快点，告诉我你的位置吧？';
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function searchAround($msg, $msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $wechatModel = new WechatModel();
        $lastMsg = $wechatModel->getLastReceiveMsg($msgXml, array('MsgType'=>'location'));
        if(empty($lastMsg)){
            $data = $msgformat['send_format']['text'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];
            $data['text']['content'] = '请发送您的位置，以精准定位';
            
            $wechatModel = new WechatModel();
            $wechatModel->sendMessage($data);
        }elseif(time()-strtotime($lastMsg['CreateTime']) > 300){
            
            $friendlyDate = new FriendlyDate();
            $data = $msgformat['send_format']['text'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];
            $data['text']['content'] = sprintf($msgformat['msg_position_expired'], $friendlyDate->timeDiff($lastMsg['CreateTime']));
            
            $wechatModel = new WechatModel();
            $wechatModel->sendMessage($data);
        }
        
        $positionModel = new PositionModel();
        $data = $positionModel->searchAround($lastMsg, $msgXml);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function getRecentExpress($msgXml){
        $wechatModel = new WechatModel();
        $lastMessage = $wechatModel->getLastSendMsg($msgXml, array('msgtype'=>'text'), array('content'=>array('value'=>'公司名称', 'side'=>'after')));
                    
        if(!empty($lastMessage)){

            $com_nu = explode('物流信息', $lastMessage['content']);
            $com_nu = explode("\n", $com_nu[0]);

            list($tmp, $com) = explode('：', $com_nu[0]);
            list($tmp, $nu) = explode('：', $com_nu[1]);
            
            
            $kdniao = get_var_from_conf('kdniao');
            $kuaidiModel = new KuaidiModel();
            $data = $kuaidiModel->kdniao($kdniao[$com], $nu, $msgXml);

            $wechatModel->sendMessage($data);
        }
    }
    
    private function getRecentWeather($msgXml){
        $wechatModel = new WechatModel();
        $lastMessage = $wechatModel->getLastSendMsg($msgXml, array('msgtype'=>'text'), array('content'=>array('value'=>'天气', 'side'=>'after')));    
        if(!empty($lastMessage)){

            $city = explode("\n", $lastMessage['content']);

            preg_match_all('/\((.+)\)/', $city[0], $match);
            $weather = get_var_from_conf('weather');
            $baiduModel = new BaiduModel();
            $data = $baiduModel->getWeather($weather[$match[1][0]], $msgXml);
            
            $wechatModel = new WechatModel();
            $wechatModel->sendMessage($data);
        }
    }
    
    public function getWeather($msg, $msgXml){
        $baiduModel = new BaiduModel();
        $data = $baiduModel->getWeather($msg, $msgXml);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function getExpress($com, $nu, $msgXml){
        $kuaidiModel = new KuaidiModel();
        $data = $kuaidiModel->kdniao($com, $nu, $msgXml);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function complexedMessage($msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        
        
        $request = new Yaf_Request_Http();
        $baseUrl = 'http://'. $request->getServer('HTTP_HOST');
        $data['text']['content'] = sprintf($msgformat['msg_to_large'], $baseUrl, $baseUrl, $baseUrl, $baseUrl);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function getGirls($msgXml){
        $data = array();
        $data['num'] = 5;
        $baiduModel = new BaiduModel();
        $data = $baiduModel->getGirls($data, $msgXml);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function getNews($keyword, $msgXml){
        $data = array();
        $keyword && $data['word'] = $keyword;
        $data['page'] = $keyword ? rand(1,5) : rand(1, 999);
        $data['rand'] = 1;
        $data['num'] = 5;
        $baiduModel = new BaiduModel();
        $data = $baiduModel->getNews($data, $msgXml);
        if($data===false){
            $this->unrecognize($keyword, $msgXml);
            return false;
        }
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function getSocials($keyword, $msgXml){
        $data = array();
        $data['page'] = rand(1, 999);
        $data['num'] = 5;
        $baiduModel = new BaiduModel();
        $data = $baiduModel->getSocials($data, $msgXml);
        if($data===false){
            $this->unrecognize($keyword, $msgXml);
            return false;
        }
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function getLottery($lotteryCode, $msgXml){
        $data = array();
        $data['lotterycode'] = $lotteryCode;
        $data['recordcnt'] = 1;
        $lotteryModel = new LotteryModel();
        $data = $lotteryModel->getLottery($data, $msgXml);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function getJoke($msgXml){
        $data = array();
        $data['page'] = rand(1, 260);
        $baiduModel = new BaiduModel();
        $data = $baiduModel->getJoke($data, $msgXml);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
    
    private function genLottery($msg, $msgXml){
        $ch2num = array('一'=>1 ,'二'=>2,'两'=>2,'三'=>3,'四'=>4,'五'=>5);
        $type = array('双色球'=>'Ssq');
        
        $num = 1;
        foreach($ch2num as $_k=>$_v){
            if(strpos($msg, $_k) !== false){
                $num = $_v;
                break;
            }
        }
        
        foreach($type as $_k=>$_v){
            if(strpos($msg, $_k) !== false){
                $type = $_v;
                break;
            }
        }
        
        $lotteryModel = new LotteryModel();
        $funcName = 'gen'.$type;
        $data = $lotteryModel->$funcName($num, $msgXml);
        
        $wechatModel = new WechatModel();
        $wechatModel->sendMessage($data);
    }
}
