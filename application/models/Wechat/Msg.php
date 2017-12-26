<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Wechat_MsgModel extends BaseModel{
    public static function initDomain(){
        $wechat = Operation_ClientsWechatModel::getRow(['client_id'=>BaseModel::domain2Id()]);
        if(!$wechat){
            log_message('error', '当前企业未接入微信公众号, client_id:'. BaseModel::domain2Id());
            return false;
        }
        
        Yaf_Registry::set('WECHAT_APP_ID', $wechat['app_id']);
        Yaf_Registry::set('WECHAT_APP_SECRET',$wechat['app_secret']);
        Yaf_Registry::set('WECHAT_TOKEN', $wechat['app_token']);
        Yaf_Registry::set('WECHAT_ENCPRYPT_TYPE', $wechat['app_encrypt_type']);
        Yaf_Registry::set('WECHAT_ENCODING_AES_KEY', $wechat['app_encoding_aes_key']);
        return true;
    }
    
    /**
     * @todo 从微信获取access_token，并存储于数据库
     * @param boolean force 是否强制从微信接口获取token
     * @return string
     */
    public static function getAccessToken($force=false){
        $cache = Cache::getInstance(BaseModel::getDomain().':');
        if(!$force && $cache->exists('wechat.token')){
            $result = $cache->hGetAll('wechat.token');
            if(!empty($result['access_token']) && !empty($result['jsapi_ticket'])){
                return $result;
            }
        }
        
        $result =['access_token'=>'', 'jsapi_ticket'=>''];
        
        //获取微信对话服务.access_token
        $data['url'] = sprintf(Yaf_Registry::get('WECHAT_API_HOST').'/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', Yaf_Registry::get('WECHAT_APP_ID'), Yaf_Registry::get('WECHAT_APP_SECRET'));
        $rt = http($data);
        if(isset($rt['errcode'])){
            log_message('error', 'get access_token from wechat error, msg: '. json_encode($rt));
            return $result;
        }

        $result['access_token'] = $rt['access_token'];
        if(strlen($result['access_token'])){
            $data = array();
            $data['url'] = sprintf(Yaf_Registry::get('WECHAT_API_HOST').'/cgi-bin/ticket/getticket?access_token=%s&type=jsapi', $result['access_token']);
            $rt = http($data);
            if(isset($rt['errcode']) && $rt['errcode']>0){
                log_message('error', 'get jsapi_ticket from wechat error, msg: '. json_encode($rt));
                return $result;
            }
            
            $result['jsapi_ticket'] = $rt['ticket'];
        }
        
        if(!empty($result['access_token']) && !empty($result['jsapi_ticket'])){
            $cache->hMset('wechat.token', $result, 7100);
            return $result;
        }
        
        return false;
    }
    
    public static function getJsApiSigObj(){
        
        $data = array();
        $data['debug'] = Yaf_Registry::get('WECHAT_WEB_JS_DEBUG');
        $data['appId'] = Yaf_Registry::get('WECHAT_APP_ID');
        $data['timestamp'] = time();
        $data['nonceStr'] = md5($data['timestamp']);
        
        $access_token = self::getAccessToken();
        if(!$access_token){
            return false;
        }
        
        $data2gen = array();
        $data2gen['jsapi_ticket'] = $access_token['jsapi_ticket'];
        $data2gen['noncestr'] = $data['nonceStr'];
        $data2gen['timestamp'] = $data['timestamp'];
        $data2gen['url'] = (empty($_SERVER['HTTPS']) ? 'http':'https') .'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'];
        
        $string1 = '';
        foreach($data2gen as $_k=>$_v){
            $string1 .= $_k.'='.$_v.'&';
        }
        unset($data2gen, $_k, $_v);
        
        $string1 = trim($string1, '&');
        
        $data['signature'] = sha1($string1);
        
        return $data;
    }
    
    /**
     * @todo 查询上一条接收记录(5分钟之内)
     * @param array $msgXml
     * @param array $where
     * @param array $like
     * @return array
     */
    public static function getLastReceiveMsg($msgXml, $where=array(), $like=array()){
        
        $db = Database::getInstance('operation');
        $db->where('FromUserName', $msgXml['FromUserName']);
        foreach($where as $_k=>$_v){
            $db->where($_k, $_v);
        }
        
        foreach($like as $_k=>$_v){
            if(is_array($_v)){
                $db->like($_k, $_v['value'], isset($_v['side'])?$_v['side']:'both', isset($_v['escape'])?$_v['escape']:NULL);
                continue;
            }
            $db->like($_k, $_v);
        }
        $db->order_by('CreateTime', 'desc');
        $db->limit(1, 0);
        $query = $db->get('wechat_receive_message');

        return $query && $query->num_rows()===1 ? $query->row_array() : array();
    }
    
       /**
     * @todo 查询上一条回复记录
     * @param array $msgXml
     * @param array $where
     * @param array $like
     * @return array
     */
    public static function getLastSendMsg($msgXml, $where=array(), $like=array(),$msg_id=null){
        
        $db = Database::getInstance('operation');
        $db->where('touser', $msgXml['FromUserName']);
        $msg_id && $db->where('msg_id',$msg_id);
        if($where){
            foreach($where as $_k=>$_v){
                $db->where($_k, $_v);
            }
        }
        if($like){
            foreach($like as $_k=>$_v){
                if(is_array($_v)){
                    $db->like($_k, $_v['value'], isset($_v['side'])?$_v['side']:'both', isset($_v['escape'])?$_v['escape']:NULL);
                }else{
                    $db->like($_k, $_v);
                }
            }
        }
        $db->order_by('createtime', 'desc');
        $db->limit(1, 0);
        $query = $db->get('wechat_send_message');

        return $query && $query->num_rows()===1 ? $query->row_array() : array();
    }
    
    /**
     * @todo 存储用户发过来的微信消息
     * @param array $msg
     * @return boolean
     */
    public static function saveMessage($msg){
        return Operation_WechatReceiveMessageModel::insert($msg);
    }
    
    /**
     * @todo 回复用户信息
     * @param array $msg
     * @return boolean
     */
    public static function sendMessage($msg){
//        没权限发消息/(ㄒoㄒ)/~~
//        $data['data'] = $msg;
//        $data['url'] = sprintf('%s/message/custom/send?access_token=%s', WX_CGI_ADDR, self::getAccessToken());
//        $data['method'] = 'post';
//        $rt = http($data);
//
//        if(!$rt || isset($rt['errcode'])){
//            if($rt['errcode'] == 42001){//access_token过期
//                self::accessTokenExpired();
//                return call_user_func_array(array($this, 'sendMessage'), array($msg));
//            }
//            error_log('send wechat message, msg: '. json_encode($rt));
//            return false;
//        }
        $data = array();
        foreach($msg as $_msg_name=>$_msg_value){
            if(is_array($_msg_value)){
                if($_msg_name ==='articles'){
                    $data['articles'] = json_encode($_msg_value);
                    continue;
                }
                foreach($_msg_value as $_k=>$_v){
                    $data[$_k] = $_v;
                }
                continue;
            }
            
            $data[$_msg_name] = $_msg_value;
        }
        
       Operation_WechatSendMessageModel::insert($data);
        
        self::autoAnwserWxMessage($msg);
        return true;
    }
    
    public static function autoAnwserWxMessage($msg){
        $msgformat = get_var_from_conf('msgformat');
        switch($msg['msgtype']){
            case 'image':
                $msg = sprintf($msgformat['image_format'], $msg['touser'], $msg['fromuser'], time(), $msg['image']['media_id']);
                break;
            case 'video':
                $msg = sprintf($msgformat['video_format'], $msg['touser'], $msg['fromuser'], time(), $msg['video']['media_id'], $msg['video']['thumb_media_id'], $msg['music']['title'], $msg['music']['description']);
                break;
            case 'music':
                $msg = sprintf($msgformat['music_format'], $msg['touser'], $msg['fromuser'], time(), $msg['music']['title'], $msg['music']['description'], $msg['music']['musicurl'], $msg['music']['hqmusicurl'], $msg['music']['thumb_media_id']);
                break;
            case 'news':
                $article_template = <<<EOF
<item>
<Title><![CDATA[%s]]></Title> 
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>
EOF;
                $articles = '';
                foreach($msg['articles'] as $_article){
                    $articles .= sprintf($article_template, $_article['title'], $_article['description'], $_article['picurl'], $_article['url']);
                }
                $msg = sprintf($msgformat['news_format'], $msg['touser'], $msg['fromuser'], time(), count($msg['articles']), $articles);
                break;
            case 'transfer_customer_service':
                $msg = sprintf($msgformat['transfer_customer_service_format'], $msg['touser'], $msg['fromuser'], time(), $msg['kfaccount']);
                break;
            default:
                $msg = sprintf($msgformat['text_format'], $msg['touser'], $msg['fromuser'], time(), $msg['text']['content']);
                break;
            
        }
        
        log_message('info', 'response msg: '. $msg);
        if(Yaf_Registry::get('WECHAT_ENCPRYPT_TYPE') === 'aes'){
            $request = new Yaf_Request_Http();
            $timestamp  = $request->getQuery('timestamp', '');
            $nonce = $request->getQuery('nonce', '');
            $wxBizMsgCrypt = new Wechat_WXBizMsgCrypt(Yaf_Registry::get('WECHAT_TOKEN'), Yaf_Registry::get('WECHAT_ENCODING_AES_KEY'), Yaf_Registry::get('WECHAT_APP_ID'));
            $bak4log = $msg;
            $res = $wxBizMsgCrypt->encryptMsg($msg, $timestamp, $nonce, $msg);
            
            if($res !==0 ){
                log_message('error', 'encrypt msg error, error code: '. $res ."\r\n msg content: ". $bak4log);
                exit('');
            }
        }
        
        log_message('all', 'request_id:'.Yaf_Registry::get('request_id')."\tip:". ip_address() ."\n    ".'response:'.$msg."\n");
        header('Content-Type: text/xml');
        exit($msg);
    }

    public static function subscribe($openid){
        log_message('info', 'user['. $openid .'] subscribe');
    }

    public static function unsubscribe($openid){
        log_message('info', 'user['. $openid .'] unsubscribe');
    }

    public static function getUser($openid){
        $db = Database::getInstance('operation');
        $db->where('openid', $openid);
        $query = $db->get('wechat_user');

        return $query && $query->num_rows()===1 ? $query->row_array() : array();
    }

    public static function kfSessionCreate($msgXml){
        $args = array(
            "kf_account" => "ever10",
            "openid" => $msgXml['FromUserName'],
            "text" => $msgXml['Content']
        );
        
        $access_token = self::getAccessToken();
        if(!$access_token){
            return false;
        }

        $data['url'] = 'https://api.weixin.qq.com/customservice/kfsession/create?access_token='. $access_token['access_token'];
        $data['data'] = json_encode($args);
        $data['method'] = 'post';
        $rt = http($data); 

        if(isset($rt['rtn']) && $rt['rtn']>0){
            log_message('error', 'create kfsession error, msg: '. json_encode($rt));
            return false;
        }
        
        if(isset($rt['errcode']) && $rt['errcode']>0){
            log_message('error', 'create kfsession error, msg: '. json_encode($rt));
            return false;
        }
        
        return true;
    }
    
    
    public static function kfGetOnlineList(){
        $access_token = self::getAccessToken();
        if(!$access_token){
            return false;
        }
        
        $data['url'] = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='. $access_token['access_token'];
        $data['method'] = 'get';
        $rt = http($data); 

        if(isset($rt['rtn']) && $rt['rtn']>0){
            log_message('error', 'getonlinekflist error, msg: '. json_encode($rt));
            return false;
        }
        
        if(isset($rt['errcode']) && $rt['errcode']>0){
            if($rt['errcode']===40001){
                self::getAccessToken(true);
            }
            log_message('error', 'getonlinekflist, msg: '. json_encode($rt));
            return false;
        }
        
        return $rt['kf_online_list'];
    }
    
    public static function kfGetFreeKf(){

        $kf_online_list = self::kfGetOnlineList();
        
        $min = 999;
        $index = 0;
        foreach($kf_online_list as $_k=>$_v){
            if($_v['accepted_case'] < $min && $_v['status']===1){
                $index = $_k;
                $min = $_v['accepted_case'];
            }
        }
        
        return empty($kf_online_list[$index]) ? array() : $kf_online_list[$index];
    }
    
    /**
     * 回复文本消息
     * @param string $text 回复的文本
     * @param array $msgXml 收到的消息
     * @return boolean
     */
    public static function text($text, $msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = $text;
        
        self::sendMessage($data);
        return false;
    }
    
    /**
     * 回复图片消息
     * @param string $mediaId 图片素材id
     * @param array $msgXml 收到的消息
     * @return boolean
     */
    public static function image($mediaId, $msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['image'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['image']['media_id'] = $mediaId;
        
        self::sendMessage($data);
        return false;
    }
    
    /**
     * 回复语音消息
     * @param string $mediaId 语音素材id
     * @param array $msgXml 收到的消息
     * @return boolean
     */
    public static function voice($mediaId, $msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['voice'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['voice']['media_id'] = $mediaId;
        
        self::sendMessage($data);
        return false;
    }
    
    /**
     * 回复视频消息
     * @param string $mediaId 视频素材id
     * @param string $thumbMediaId 视频缩略图素材id
     * @param string $title 视频标题
     * @param string $description 视频描述
     * @param array $msgXml 收到的消息
     * @return boolean
     */
    public static function video($mediaId, $thumbMediaId, $title, $description, $msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['video'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['video'] = [
            'media_id'          =>  $mediaId,
            'thumb_media_id'    =>  $thumbMediaId,
            'title'             =>  $title,
            'description'       =>  $description,
        ];
        
        self::sendMessage($data);
        return false;
    }
    
    /**
     * 回复音乐消息
     * @param string $musicUrl 普通音乐url
     * @param string $hqMusicUrl 高品质音乐url
     * @param string $thumbMediaId 音乐缩略图素材id
     * @param string $title 音乐标题
     * @param string $description 音乐描述
     * @param array $msgXml 收到的消息
     * @return boolean
     */
    public static function music($musicUrl, $hqMusicUrl, $thumbMediaId, $title, $description, $msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['music'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['music'] = [
            'musicurl'          =>  $musicUrl,
            'hqmusicurl'        =>  $hqMusicUrl,
            'thumb_media_id'    =>  $thumbMediaId,
            'title'             =>  $title,
            'description'       =>  $description,
        ];
        
        self::sendMessage($data);
        return false;
    }
    
    /**
     * 回复图文消息
     * @param string $articles 图文列表
     * @param array $msgXml 收到的消息
     * @return boolean
     */
    public static function news($articles, $msgXml){
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['news'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['articles'] = $articles;
        $data['article_count'] = count($articles);
        
        self::sendMessage($data);
        return false;
    }
}