<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @todo 微信公众号消息系统
 */
class MsgController extends WechatController {
    /**
     * 用作接入微信时，校验token
     */
    private function valid(){
        $echoStr = $_GET["echostr"];        //随机字符串
        
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    /**
     * 校验token时，对参数签名
     */
    private function checkSignature(){
        $signature = $_GET["signature"];    //微信加密签名
        $timestamp = $_GET["timestamp"];    //时间戳
        $nonce = $_GET["nonce"];            //随机数
        $token = Yaf_Registry::get('WECHAT_TOKEN');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);      //进行字典序排序
        //sha1加密后与签名对比
        if( sha1(implode($tmpArr)) == $signature ){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * @todo 接收微信消息
     * @return boolean
     */
    public function receiveAction(){
        /*---start---验证微信token,通过后，注释掉即可，但是一定不要删除---start--*/
        //$this->valid();exit;
        /*---end---验证微信token,通过后，注释掉即可，但是一定不要删除---end--*/
        $request = new Yaf_Request_Http();
        
        $timestamp  = $request->getQuery('timestamp', '');
        $nonce = $request->getQuery('nonce', '');
        $msgSignature  = $request->getQuery('msg_signature', '');
        $encryptType = $request->getQuery('encrypt_type','');
        
        $bak4log = $data = file_get_contents('php://input');
        
        if(ENCPRYPT_TYPE === 'aes'){
            $wxBizMsgCrypt = new Wechat_WXBizMsgCrypt(Yaf_Registry::get('WECHAT_TOKEN'), Yaf_Registry::get('WECHAT_ENCODING_AES_KEY'), Yaf_Registry::get('WECHAT_APP_ID'));
            $res = $wxBizMsgCrypt->decryptMsg($msgSignature, $timestamp, $nonce, $data, $data);
            if($res !== 0){
                log_message('error', 'decrypt msg error, error code: '. $res ."\r\n msg content: ". $bak4log);
                exit('');
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
            $msgformat = get_var_from_conf('msgformat')['receive_format'][$msgXml['MsgType']];
            foreach($msgformat as $_field){
                if(!empty($msgXml[$_field]) && in_array($msgXml[$_field])){
                    if($_field==='SendLocationInfo'){
                        foreach($msgXml[$_field] as $_subk=>$_subv){
                            $msg[$_subk] = $_subv;
                        }
                        continue;
                    }else if($_field==='SendPicsInfo'){
                        $msg['Count'] = $msgXml[$_field]['Count'];
                        $msg['PicList'] = json_encode($msgXml[$_field]['PicList']);
                        continue;
                    }else if($_field==='ScanCodeInfo'){
                        foreach($msgXml[$_field] as $_subk=>$_subv){
                            $msg[$_subk] = $_subv;
                        }
                        continue;
                    }
                }

                if($_field==='EventKey'){
                    $msg['EventKey'] = empty($msgXml['EventKey']) ? '' : $msgXml['EventKey'];
                    continue;
                }else if($_field==='CreateTime'){
                    $msg['CreateTime'] = date('Y-m-d H:i:s', $msgXml['CreateTime']);
                    continue;
                }
                $msg[$_field] = isset($msgXml[$_field]) ? $msgXml[$_field] : '';
            }  
            
            //保存用户发来的信息
            Wechat_MsgModel::saveMessage($msg);
            
            $msgType = $msgXml['MsgType']==='event' ? $msgXml['MsgType'].Ucfirst(strtolower($msgXml['Event'])) : $msgXml['MsgType'];
            if($msgXml['MsgType']==='event'){
                Wechat_EventModel::$msgType($msgXml);
            }else{
                Wechat_AnalysisModel::$msgType($msgXml);
            }
        }
        
        return false;
    }
}
