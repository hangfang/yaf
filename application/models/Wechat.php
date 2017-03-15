<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class WechatModel extends BaseModel{
    
    public $access_token = '';
    public $jsapi_ticket = '';
    
    public function __construct(){
        parent::__construct();
        $this->access_token = $this->getAccessToken();
        $this->jsapi_ticket = $this->getJsApiTicket();
    }
    
    public function accessTokenExpired(){
        $db = Database::getInstance();
        return $db->truncate('wechat_token');
    }
    
    /**
     * 从微信获取access_token，并存储于数据库
     * @return string
     */
    public function getAccessToken(){
        $db = Database::getInstance();
        $db->select('token');
        $db->where('insert_time > ', date('Y-m-d H:i:s', time()-2*3600));
        $db->order_by('insert_time', 'desc');
        $db->limit(1, 0);
        $query = $db->get('wechat_token');
        
        $token = $query && $query->num_rows()>0 ? $query->row()->token : '';
        
        if($token === ''){
            $this->accessTokenExpired();//清除token记录表
            
            $data = array();
            $data['method'] = 'get';
            $data['url'] = sprintf('%s/token?grant_type=client_credential&appid=%s&secret=%s', WX_CGI_ADDR, WX_APP_ID, WX_APP_SECRET);
            $rt = http($data);
            
            if(isset($rt['errcode'])){
                log_message('error', 'get access_token from wechat error, msg: '. json_encode($rt));
                return '';
            }
            
            $token = $rt['access_token'];
            if(strlen($token)){
                if(!$db->insert('wechat_token', array('token'=>$token))){
                    log_message('error', 'insert into access_token error, sql: '. $db->last_query());
                }
            }
        }
        
        return $this->access_token = $token;
    }
    
    public function getJsApiSigObj(){
        
        $data = array();
        $data['debug'] = WX_JSAPI_DEBUG;
        $data['appId'] = WX_APP_ID;
        $data['timestamp'] = time();
        $data['nonceStr'] = md5($data['timestamp']);
        
        
        $data2gen = array();
        $data2gen['jsapi_ticket'] = $this->jsapi_ticket;
        $data2gen['noncestr'] = $data['nonceStr'];
        $data2gen['timestamp'] = $data['timestamp'];
        $data2gen['url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'];
        
        $string1 = '';
        foreach($data2gen as $_k=>$_v){
            $string1 .= $_k.'='.$_v.'&';
        }
        unset($data2gen, $_k, $_v);
        
        $string1 = trim($string1, '&');
        
        $data['signature'] = sha1($string1);
        
        return $data;
    }
    
    
    public function getJsApiTicket(){
        
        $db = Database::getInstance();
        $db->select('jsapi_ticket');
        $db->where('insert_time > ', date('Y-m-d H:i:s', time()-2*3600));
        $db->order_by('insert_time', 'desc');
        $db->limit(1, 0);
        $query = $db->get('wechat_token');
        
        $jsapi_ticket = $query && $query->num_rows()>0 ? $query->row()->jsapi_ticket : '';
        
        if($jsapi_ticket === ''){
            $data = array();
            $data['method'] = 'get';
            $data['url'] = sprintf('%s/ticket/getticket?access_token=%s&type=jsapi', WX_CGI_ADDR, $this->access_token);
            $rt = http($data);
            
            if(isset($rt['errcode']) && $rt['errcode']>0){
                log_message('error', 'get jsapi_ticket from wechat error, msg: '. json_encode($rt));
                if($rt['errcode'] === 42001){//access_token过期
                    $this->accessTokenExpired();
                    return call_user_func_array(array($this, 'getJsApiTicket'), array());
                }
            }
            
            $jsapi_ticket = $rt['ticket'];
            if(strlen($jsapi_ticket)){
                if(!$db->update('wechat_token', array('jsapi_ticket'=>$jsapi_ticket))){
                    log_message('error', 'update jsapi_ticket error, sql: '. $db->last_query());
                }
            }
        }
        
        return $this->jsapi_ticket = $jsapi_ticket;
    }
    
    /**
     * 查询上一条接收记录(5分钟之内)
     * @param array $msgXml
     * @param array $where
     * @param array $like
     * @return array
     */
    public function getLastReceiveMsg($msgXml, $where=array(), $like=array()){
        
        $db = Database::getInstance();
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
     * 查询上一条回复记录
     * @param array $msgXml
     * @param array $where
     * @param array $like
     * @return array
     */
    public function getLastSendMsg($msgXml, $where=array(), $like=array()){
        
        $db = Database::getInstance();
        $db->where('touser', $msgXml['FromUserName']);
        foreach($where as $_k=>$_v){
            $db->where($_k, $_v);
        }
        
        foreach($like as $_k=>$_v){
            if(is_array($_v)){
                $db->like($_k, $_v['value'], isset($_v['side'])?$_v['side']:'both', isset($_v['escape'])?$_v['escape']:NULL);
            }else{
                $db->like($_k, $_v);
            }
        }
        $db->order_by('CreateTime', 'desc');
        $db->limit(1, 0);
        $query = $db->get('wechat_send_message');

        return $query && $query->num_rows()===1 ? $query->row_array() : array();
    }
    
    /**
     * 存储用户发过来的微信消息
     * @param array $msg
     * @return boolean
     */
    public function saveMessage($msg){
        
        unset($msg['CreateTime']);
        $db = Database::getInstance();
        if(!$db->insert('wechat_receive_message', $msg)){
            log_message('error', 'save wechat message error, sql:'. $db->last_query());
            return false;
        }
        
        return true;
    }
    
    /**
     * 回复用户信息
     * @param array $msg
     * @return boolean
     */
    public function sendMessage($msg){
//        没权限发消息/(ㄒoㄒ)/~~
//        $data['data'] = $msg;
//        $data['url'] = sprintf('%s/message/custom/send?access_token=%s', WX_CGI_ADDR, $this->access_token);
//        $data['method'] = 'post';
//        $rt = http($data);
//
//        if(!$rt || isset($rt['errcode'])){
//            if($rt['errcode'] == 42001){//access_token过期
//                $this->accessTokenExpired();
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
        
        $db = Database::getInstance();
        if(!$db->insert('wechat_send_message', $data)){
            log_message('error', 'save wechat_send_message error, sql:'. $db->last_query());
        }
        
        $this->autoAnwserWxMessage($msg);
        return true;
    }
    
    public function autoAnwserWxMessage($msg){
        $msgformat = get_var_from_conf('msgformat');
        switch($msg['msgtype']){
            case 'image':
                $msg = sprintf($msgformat['image_format'], $msg['touser'], $msg['fromuser'], time(), $msg['image']['media_id']);
                break;
            case 'video':
                $msg = sprintf($msgformat['video_format'], $msg['touser'], $msg['fromuser'], time(), $msg['video']['media_id']);
                break;
            case 'music':
                $msg = sprintf($msgformat['music_format'], $msg['touser'], $msg['fromuser'], time(), $msg['music']['title'], $msg['music']['description'], $msg['music']['musicurl'], $msg['music']['hqmusicurl'], $msg['music']['hqmusicurl'], $msg['music']['thumbmediaid']);
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
            default:
                $msg = sprintf($msgformat['text_format'], $msg['touser'], $msg['fromuser'], time(), $msg['text']['content']);
                break;
            
        }
        
        if(ENCPRYPT_TYPE === 'aes'){
            
            $request = new Yaf_Request_Http();

            $timestamp  = $request->getQuery('timestamp', '');
            $nonce = $request->getQuery('nonce', '');
            $wxBizMsgCrypt = new Wechat_WXBizMsgCrypt(WX_TOKEN, WX_ENCODING_AES_KEY, WX_APP_ID);
            $bak4log = $msg;
            $res = $wxBizMsgCrypt->encryptMsg($msg, $timestamp, $nonce, $msg);
            
            if($res !==0 ){
                log_message('error', 'encrypt msg error, error code: '. $res ."\r\n msg content: ". $bak4log);
                echo '';
                exit;
            }
        }
        
        header('Content-Type: text/xml');
        echo $msg;
        exit;
    }

    public function subscribe($openid){
        $db = Database::getInstance();
        if($this->getUser($openid)){
            
            $db->set('status', 0);
            $db->set('update_time', date('Y-m-d H:i:s'));
            $db->where('openid', $openid);
            $query = $db->update('wechat_user');

            if($query === false){
                log_message('error', 'resubscribe error, sql: '. $db->last_query());
                return false;
            }

            return 'old';
        }

        $query = $db->insert('wechat_user', array('openid'=>$openid, 
            'status'=>0, 'update_time'=>date('Y-m-d H:i:s')));

        if($query === false){
            log_message('error', 'save subscribe error, sql: '. $db->last_query());
            return false;
        }

        return 'new';
    }

    public function unsubscribe($openid){
        if($this->getUser($openid)){
            $db = Database::getInstance();
            $db->set('status', 1);
            $db->set('update_time', date('Y-m-d H:i:s'));
            $db->where('openid', $openid);
            $query = $db->update('wechat_user');

            if($query === false){
                log_message('error', 'unsubscribe error, sql: '. $db->last_query());
                return false;
            }

            return true;
        }

        log_message('error', 'openid not found error, sql: '. $db->last_query());

        return false;
    }

    public function getUser($openid){
        $db = Database::getInstance();
        $db->where('openid', $openid);
        $query = $db->get('wechat_user');

        return $query && $query->num_rows()===1 ? $query->row_array() : array();
    }
}