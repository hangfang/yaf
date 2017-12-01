<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/*
 * 微信事件处理
 */
class Wechat_EventModel extends BaseModel{
    /**
    * @todo 订阅的事件推送
    */
    public static function eventSubscribe($msgXml){
        
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        
        //查询是否是老用户回归
        $data['text']['content'] = true ? sprintf($msgformat['msg_welcome_newbeing'], '') : sprintf($msgformat['msg_welcome_back'], '');
        Wechat_MsgModel::sendMessage($data);
    }

    /**
    * @todo 取消订阅的事件推送
    */
    public static function eventUnsubscribe($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        
        $rt = Wechat_MsgModel::unsubscribe($msgXml['FromUserName']);
        return false;
    }

    /**
    * @todo 扫描二维码的事件推送
    */
    public static function eventScan($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '扫描结果：'. $msgXml['EventKey'];
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }

    /**
    * @todo 地理位置上报的事件推送（订阅号不支持）
    */
    public static function eventLocation($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $wechat = get_var_from_conf('wechat');
        if(!in_array($msgXml['FromUserName'], $wechat['staff']) || date('w')==='0' || date('w')==='6'){
            return false;
        }
        
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        
        $staff_file_format = <<<EOF
<?php
    defined('APPLICATION_PATH') OR exit('No direct script access allowed');
    return \$staff = %s; 
EOF;
        if(abs($msgXml['Latitude']-22.538925) < 0.005 && abs($msgXml['Longitude']-113.947526)<0.005 && date('H')-0<12){
            if(file_exists(APPLICATION_PATH.'/cache/staff/staff_'. date('Ymd') .'.php')){
                $staff = include(APPLICATION_PATH.'/cache/staff/staff_'. date('Ymd') .'.php');
                if(!isset($staff['on'])){
                    $staff['on'] = array();
                }
                
                if(isset($staff['on'][$msgXml['FromUserName']])){
                    return false;
                }
                $staff['on'][$msgXml['FromUserName']] = $msgXml['FromUserName'] .'@'. date('Y-m-d H:i:s');
                write_file(APPLICATION_PATH.'/cache/staff/staff_'. date('Ymd') .'.php', sprintf($staff_file_format, var_export($staff, true)));
            }else{
                $staff = array();
                $staff['on'] = array();
                
                $staff['on'][$msgXml['FromUserName']] = $msgXml['FromUserName'] .'@'. date('Y-m-d H:i:s');
                write_file(APPLICATION_PATH.'/cache/staff/staff_'. date('Ymd') .'.php', sprintf($staff_file_format, var_export($staff, true)));
            }
            $format = <<<EOF
@%s
工作即将开始，为自己加油！
EOF;
            $data['text']['content'] = sprintf($format, date('Y-m-d H:i:s'));
        }elseif((abs($msgXml['Latitude']-22.538925) > 0.005 || abs($msgXml['Longitude']-113.947526) > 0.005) && date('H')-0>17){
            if(file_exists(APPLICATION_PATH.'/cache/staff/staff_'. date('Ymd') .'.php')){
                $staff = include(APPLICATION_PATH.'/cache/staff/staff_'. date('Ymd') .'.php');
                
                if(isset($staff['off'][$msgXml['FromUserName']]) || !isset($staff['on'][$msgXml['FromUserName']])){
                    return false;
                }
                
                if(!isset($staff['off'])){
                    $staff['off'] = array();
                }
                
                $staff['off'][$msgXml['FromUserName']] = $msgXml['FromUserName'] .'@'. date('Y-m-d H:i:s');
                write_file(APPLICATION_PATH.'/cache/staff/staff_'. date('Ymd') .'.php', sprintf($staff_file_format, var_export($staff, true)));
            }else{
                return false;
            }
            $format = <<<EOF
@%s
工作一天了，好好休息哦！
EOF;
            $data['text']['content'] = sprintf($format, date('Y-m-d H:i:s'));
        }else{
            return false;
        }
        
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }

    /**
    * @todo 点击菜单拉取消息的事件推送
    */
    public static function eventClick($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = $msgXml['FromUserName'] .'点击菜单';
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }

    /**
    * @todo 点击菜单跳转链接时的事件推送
    */
    public static function eventView($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = $msgXml['FromUserName'] .'菜单跳转';
        
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
    
    public function eventKf_create_session($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '已接入客服';
            
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
    
    public function eventKf_close_session($msgXml){
        echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '本次服务到此结束';
            
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
    
    public function eventTemplatesendjobfinish($msgXml){
        if($msgXml['Status'] !== 'success'){
            log_message('error', 'push msg to '. $msgXml['FromUserName']. ' failed, msg: '. $msgXml['Status']);
        }
        echo '';exit;//输出空字符，微信不做任何反应
    }
    
    public static function eventLocation_select($msgXml){
        //echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '你选择的位置';
            
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
    
    /**
     * 弹出微信相册发图器的事件推送
     * @param type $msgXml
     */
    public static function eventPic_weixin($msgXml){
        //echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '你从相册里选择了图片';
            
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
    
    /**
     * 弹出系统拍照发图的事件推送
     * @param type $msgXml
     */
    public static function eventPic_sysphoto($msgXml){
        //echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '你在用相机拍照';
            
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
    
    /**
     * 弹出拍照或者相册发图的事件推送
     * @param type $msgXml
     */
    public static function eventPic_photo_or_album($msgXml){
        //echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '你选择照片或者拍照';
            
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
    
    /**
     * 扫码推事件且弹出“消息接收中”提示框的事件推送
     * @param type $msgXml
     */
    public static function eventScancode_waitmsg($msgXml){
        //echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '你要扫码并接收消息';
            
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
    
    /**
     * 扫码推事件的事件推送
     * @param type $msgXml
     */
    public static function eventScancode_push($msgXml){
        //echo '';exit;//输出空字符，微信不做任何反应
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '你扫码推事件';
            
        
        Wechat_MsgModel::sendMessage($data);
        return false;
    }
    
    /**
     * 资质认证成功（此时立即获得接口权限）
     * @param type $msgXml
     */
    public static function eventQualification_verify_success($msgXml){
        //做一些事情，比如更新数据状态
        return false;
    }
    
    /**
     * 资质认证失败
     * @param type $msgXml
     */
    public static function eventQualification_verify_fail($msgXml){
        //做一些事情，比如更新数据状态
        return false;
    }
    
    /**
     * 名称认证成功（即命名成功）
     * @param type $msgXml
     */
    public static function eventNaming_verify_success($msgXml){
        //做一些事情，比如更新数据状态
        return false;
    }
    
    /**
     * 名称认证失败（这时虽然客户端不打勾，但仍有接口权限）
     * @param type $msgXml
     */
    public static function eventNaming_verify_fail($msgXml){
        //做一些事情，比如更新数据状态
        return false;
    }
    
    /**
     * 年审通知
     * @param type $msgXml
     */
    public static function eventAnnual_renew($msgXml){
        //做一些事情，比如更新数据状态
        return false;
    }
    
    /**
     * 认证过期失效通知
     * @param type $msgXml
     */
    public static function eventVerify_expired($msgXml){
        //做一些事情，比如更新数据状态
        return false;
    }
}