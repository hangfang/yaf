<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/*
 * 诸葛到店自己配置的消息回复
 */
class Wechat_DiyModel extends BaseModel{
    /**
     * 未识别
     * @param array $msgXml 接收到的消息内容
     */
    public static function unrecognize($msg, $msgXml){
        //echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = $msgformat['msg_unrecognized'];
        $data['msg_id'] = $rs['msg_id'];

        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 太复杂
     * @param array $msgXml 接收到的消息内容
     */
    public static function complexedMessage($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
              
        $data['text']['content'] = sprintf($msgformat['msg_to_large'], '');
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 识别黄金关键字
     * @param array $msgXml 接收到的消息内容
     */
    public static function gold($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        
        $data['text']['content'] = sprintf($msgformat['msg_gold'], '');
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 识别邀请关键字
     * @param array $msgXml 接收到的消息内容
     */
    public static function invite($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        
        $data['text']['content'] = sprintf($msgformat['msg_invite'], '');
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
        
    /**
     * 返回稍复杂的文本消息
     * @param array $msgXml 接收到的消息内容
     */
    public static function responseMediaMsg($msgXml){
        
        $msgformat = get_var_from_conf('msgformat');
        $mediaMsgFormat = get_var_from_conf('mediamsgformat');
        
        if(!isset($mediaMsgFormat[strtolower($msgXml['Content'])])){
            log_message('error', 'mediamsgformat error, msg: '. print_r($msgXml['Content'], true));
            echo '';exit;
        }
        
        $mediaMsg = $mediaMsgFormat[strtolower($msgXml['Content'])];
            
        $type = $mediaMsg['type'];
        $data = $msgformat['send_format'][$type];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        
        switch($type){
            case 'news':
                return Wechat_MsgModel::news($mediaMsg['content'], $msgXml);
            case 'image':
                return Wechat_MsgModel::image($mediaMsg['content']['media_id'], $msgXml);
            case 'voice':
                return Wechat_MsgModel::voice($mediaMsg['content']['media_id'], $msgXml);
            case 'video':
                return Wechat_MsgModel::video($mediaMsg['content']['media_id'], $mediaMsg['content']['thumb_media_id'], $mediaMsg['content']['title'], $mediaMsg['content']['description'], $msgXml);
            case 'music':
                return Wechat_MsgModel::music($mediaMsg['content']['musicurl'], $mediaMsg['content']['hqmusicurl'], $mediaMsg['content']['thumb_media_id'], $mediaMsg['content']['title'], $mediaMsg['content']['description'], $msgXml);
            default:
                $data['text']['content'] = $mediaMsg['content'];
                break;
        }
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
}
