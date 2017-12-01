<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/*
 * 微信消息分析
 */
class Wechat_AnalysisModel extends BaseModel{
    /**
     * 文本消息
     * @param array $msgXml 接收到的消息内容
     */
    public static function text($msgXml){
        $contents = $msgXml['Content'];
        
        $contents = trim(str_replace(array('，', ','), array(' ', ' '), $contents));
        $contents = explode(' ', $contents);
        
        $wechat = get_var_from_conf('wechat');
        $mediamsgformat = get_var_from_conf('mediamsgformat');
        //echo '';exit;//输出空字符，微信不做任何反应
        switch(count($contents)){
            case 1:
                if(in_array(strtolower($contents[0]), $wechat['kf'])){
                    self::kfSessionCreate($msgXml);
                }elseif(isset($mediamsgformat[strtolower($contents[0])])){
                    Wechat_DiyModel::responseMediaMsg($msgXml);
                }else{
                    Wechat_DiyModel::unrecognize($contents[0], $msgXml);
                }
                break;
            default :
                Wechat_DiyModel::unrecognize($contents[0], $msgXml);
                break;
        }
    }
    
    /**
     * 图片消息
     * @param array $msgXml 接收到的消息内容
     */
    public static function image($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '抱歉，图片处理comming soon...';
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 语音消息
     * @param array $msgXml 接收到的消息内容
     */
    public static function voice($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgXml['Content'] = trim($msgXml['Recognition'], '？！');
        if(strlen($msgXml['Content'])>0){
            self::text($msgXml);
            return false;
        }
        
        
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '正在接入语音机器人...';
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 视频消息
     * @param array $msgXml 接收到的消息内容
     */
    public static function video($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '分享视频真的好吗？';
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 短信视频消息
     * @param array $msgXml 接收到的消息内容
     */
    public static function shortvideo($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = 'Oh, I like it';
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 定位消息
     * @param array $msgXml 接收到的消息内容
     */
    public static function location($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
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
//            Wechat_MsgModel::sendMessage($data);
//
//        }
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = sprintf($msgformat['msg_position'], $msgXml['Label']);
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 链接消息
     * @param array $msgXml 接收到的消息内容
     */
    public static function link($msgXml){
        
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '等等，对，这链接有毒！';
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 客服服务消息
     * @param array $msgXml 接收到的消息内容
     */
    public static function kfSessionCreate($msgXml){
        //echo '';exit;//输出空字符，微信不做任何反应
        
        $rt = Wechat_MsgModel::kfGetFreeKf($msgXml);
        
        $msgformat = get_var_from_conf('msgformat');
        
        if(empty($rt)){
            $data = $msgformat['send_format']['text'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];
            $data['text']['content'] = '客服不在线，<a href="tel:'.SERVICE_TEL.'">拨打客服电话</a>';
        }else{
            $data = $msgformat['send_format']['transfer_customer_service'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];
            $data['msgtype'] = 'transfer_customer_service';
            $data['kfaccount'] = sprintf($data['transinfo']['kfaccount'], $rt['kf_account']);
        }
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
}
