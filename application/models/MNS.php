<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

require APPLICATION_PATH . '/application/library/MNS/GuzzleHttp/functions.php';
require APPLICATION_PATH . '/application/library/MNS/GuzzleHttp/Psr7/functions.php';
require APPLICATION_PATH . '/application/library/MNS/GuzzleHttp/Promise/functions.php';

use MNS\AliyunMNS\Client;
use MNS\AliyunMNS\Topic;
use MNS\AliyunMNS\Constants;
use MNS\AliyunMNS\Model\MailAttributes;
use MNS\AliyunMNS\Model\SmsAttributes;
use MNS\AliyunMNS\Model\BatchSmsAttributes;
use MNS\AliyunMNS\Model\MessageAttributes;
use MNS\AliyunMNS\Model\SubscriptionAttributes;
use MNS\AliyunMNS\Exception\MnsException;
use MNS\AliyunMNS\Requests\PublishMessageRequest;
use MNS\AliyunMNS\Requests\CreateTopicRequest;

class MNSModel extends BaseModel{
    
    public function __construct() {
        parent::__construct();
        $this->_error = get_var_from_conf('error');
    }
    
    public function sms($params, $exit=true){
        if(Yaf_Registry::get('app')->environ()==='develop'){
            $return = $this->_error[0];
            if($exit){
                exit(json_encode($return));
            }
            
            return true;
        }
        
        /**
         * Step 1. 初始化Client
         */
        $this->endPoint = END_POINT; // eg. http://1234567890123456.mns.cn-shenzhen.aliyuncs.com
        $this->accessId = ALIYUN_ACCESS_KEY_ID;
        $this->accessKey = ALIYUN_ACCESS_KEY;
        $this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);
        
        //$topicName = "sms.topic-cn-hangzhou".uniqid();
        $topicName = $params['topicName'];

        // now sub and send message
        $messageBody = "test";
        $bodyMD5 = md5($messageBody);
       
        $topic = $this->client->getTopicRef($topicName);
        
        try{
            $smsEndpoint = $topic->generateSmsEndpoint();
            
            $batchSmsAttributes = new BatchSmsAttributes(SMS_SIGN_NAME, $params['templateName']);
            foreach($params['receiver'] as $v){
                $batchSmsAttributes->addReceiver($v['phone'], $v['smsParam']);
            }
            
            $messageAttributes = new MessageAttributes($batchSmsAttributes);
            $request = new PublishMessageRequest($messageBody, $messageAttributes);

            $res = $topic->publishMessage($request);
            if(! $res->isSucceed()){
                if($exit){
                    exit(json_encode(array('rtn'=>-1, 'error_msg'=>'发布短信失败')));
                }
                
                return false;
            }
            
            if(strtoupper($bodyMD5)!==$res->getMessageBodyMD5()){
                log_message('error', "消息体验签失败\n消息体: ". $messageBody ."\n己方md5: ". strtoupper($bodyMD5) ."\n阿里云返回md5: ". $res->getMessageBodyMD5());
                if($exit){
                    exit(json_encode(array('rtn'=>-1, 'error_msg'=>'消息体验签失败')));
                }
                
                return false;
            }
            
            if($res->isSucceed()){
                $return = $this->_error[0];
                $return['messageId'] = $res->getMessageId();
                if($exit){
                    exit(json_encode($return));
                }
                
                return true;
            }
            
            if($exit){
                exit(json_encode($this->_error[13]));
            }
            
            return false;
        }catch (MnsException $e){
            log_message('error', "发送短信失败, msg: ". $e->getMessage() ."\n参数: ". print_r($params, true));
            if($exit){
                exit(json_encode(array('rtn'=>-1, 'error_msg'=>$e->getMessage())));
            }
            
            return false;
        }
    }
}