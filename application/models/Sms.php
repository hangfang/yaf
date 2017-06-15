<?php

class SmsModel extends BaseModel{
    
    /**
     * @todo 阿里云短信
     * @param string $phone 短信接收方信息
     * @param string $biz 业务代码
     * @param boolean $exit 发送短信后是否退出
     * @return boolean
     * @author fanghang@fujiacaifu.com
     */
    private function aliyun($receiver, $biz='', $exit=true){
        $tpl = array('register'=>'SMS_68225357', 'withdraw'=>'SMS_68090327', 'identify'=>'SMS_68120300', 'set_trade_passwd'=>'SMS_69985464', 'identify_send_password'=>'SMS_69990438', 'staff_find'=>'SMS_70010372', 'customer_find'=>'SMS_70135360', 'customer_register_notice'=>'SMS_70040911', 'customer_recharge_notice_for_admin'=>'SMS_70190005', 'agency_apply'=>'SMS_70565004');
        $params = array(
            'receiver'=>$receiver,
            'templateName'=>$tpl[$biz],
            'topicName'=>'sms.topic-cn-shenzhen',
            'subscriptionName'=>'sms.subscription'
        );
        
        return (new MNSModel())->sms($params, $exit);
    }
    
    /**
     * @todo 发送短信
     * @param string $phone 短信接收方手机号码
     * @param array $content 短信配置数组
     * @param string $biz 业务代码
     * @param boolean $exit 发送短信后是否退出
     * @return boolean
     * @author fanghang@fujiacaifu.com
     */
    static function send($phone, $content, $biz, $exit=true){
        $smsChannel = SMS_CHANNEL;
        
        if($smsChannel==='aliyun'){
            $receiver = array();
            if(is_array($phone)){
                foreach($phone as $_phone){
                    $receiver[] = array('smsParam'=>$content, 'phone'=>$_phone);
                }
            }else{
                $receiver[] = array('smsParam'=>$content, 'phone'=>$phone);
            }
            return (new self())->$smsChannel($receiver, $biz, $exit);
        }
    }
}