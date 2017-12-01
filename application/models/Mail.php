<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

Class MailModel{
    
    private $_mailChannel = '';
    
    public function __construct($mailChannel='') {
        $this->_mailChannel = empty($mailChannel) ? ucfirst(MAIL_CHANNEL) : ucfirst($mailChannel);
    }
    
    /**
     * 发送邮件
     * @param string $title 邮件标题
     * @param string $body 邮件正文
     * @param array $to 收件人 (array('fanghang@fujiacaifu.com'=>'方航'))
     * @param array $cc 抄送 (array('fanghang@fujiacaifu.com'=>'方航'))
     * @param array $bcc 暗送 (array('fanghang@fujiacaifu.com'=>'方航'))
     * @return boolean
     * @author fanghagn@fujiacaifu.com
     */
    public function sendMail($title, $body, $to, $cc=array(), $bcc=array()){
        if(empty($this->_mailChannel) || !method_exists($this, $this->_mailChannel)){
            log_message('error', 'email channel['. $this->_mailChannel .'] not exists');
            return false;
        }
        
        $return = true;//初始化
        $funcName = $this->_mailChannel;
        if($funcName==='Aliyun'){
            //阿里云不支持密送、抄送，因此分组发送
            $to = array_merge($to, $cc);
            if(!empty($to)){
                $return = $this->$funcName($title, $body, $to);
            }
            
            if(!empty($bcc)){
                $return = $this->$funcName($title, $body, $bcc) && $return;
            }

            //return !!$return;
            return $return;
        }else{
            return $this->$funcName($title, $body, $to, $cc, $bcc);
        }
    }
    
    /**
     * 发送邮件
     * @param string $title 邮件标题
     * @param string $body 邮件正文
     * @param array $to 收件人 (array('fanghang@fujiacaifu.com'=>'方航'))
     * @param array $cc 抄送 (array('fanghang@fujiacaifu.com'=>'方航'))
     * @param array $bcc 暗送 (array('fanghang@fujiacaifu.com'=>'方航'))
     * @return boolean
     * @author fanghagn@fujiacaifu.com
     */
    private function Exmail($title, $body, $to, $cc=array(), $bcc=array()){
        $config = Yaf_Registry::get('config');
        $emailConf = $config['application']['email']->toArray();

        $mail = new PHPMailer($emailConf);
        $mail->From = $mail->Username;
        $mail->FromName = "【诸葛到店】管理员";
        
        if(!empty($bcc)){
            if(is_array($bcc)){
                foreach($bcc as $k=>$v){
                    $mail->addBCC($k, $v);
                }
            }else{
                $mail->addBCC($bcc, $bcc);
            }
        }
        
        if(!empty($cc)){
            if(is_array($cc)){
                foreach($cc as $k=>$v){
                    $mail->addCC($k, $v);
                }
            }else{
                $mail->addCC($cc, $cc);
            }
        }
        
        if(!empty($to)){
            if(is_array($to)){
                foreach($to as $k=>$v){
                    $mail->addAddress($k, $v);
                }
            }else{
                $mail->addAddress($to, $to);
            }
        }
        
        $mail->Subject = PHP_ENV!='product' ? '【测试】'.$title : $title;
        $mail->Body = $body; 
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略 
        $mail->WordWrap = 80; // 设置每行字符串的长度 
        //$mail->AddAttachment("f:/test.png"); //可以添加附件 
        $mail->IsHTML(true); 
        $rt = $mail->Send();

        if(!$rt){
            log_message('error', $this->_error[12]."\n".'errorInfo:'. $mail->ErrorInfo ."\n" .'body:'.$mail->Body);
        }
        
        return !!$rt;
    }
    
    /**
     * 发送邮件
     * @param string $title 邮件标题
     * @param string $body 邮件正文
     * @param array $to 收件人 (array('fanghang@fujiacaifu.com'=>'方航'))
     * @return boolean
     * @author fanghagn@fujiacaifu.com
     */
    private function Aliyun($title, $body, $to){
        require APPLICATION_PATH.'/application/library/DM/Config.php';
        
        $iClientProfile = DM_Profile_DefaultProfile::getProfile("cn-hongkong", ALIYUN_MAIL_ACCESS_KEY_ID, ALIYUN_MAIL_ACCESS_KEY);        
        $client = new DM_DefaultAcsClient($iClientProfile);    
        $request = new DM_SingleSendMailRequest();
        
        $from = array('server@rostonefx.com', 'server1@rostonefx.com', 'server2@rostonefx.com', 'server3@rostonefx.com', 'server4@rostonefx.com', 'server5@rostonefx.com');
        $request->setAccountName($from[array_rand($from)]);
        $request->setFromAlias("【诸葛阿到店】");
        $request->setAddressType(0);
        $request->setTagName('server');
        $request->setReplyToAddress('true');

        $request->setToAddress(implode(',', array_keys($to)));        
        $request->setSubject(PHP_ENV!='product' ? '【测试】'.$title : $title);
        $request->setHtmlBody($body);
        try {
            $response = $client->getAcsResponse($request);
            log_message('info', 'aliyun dm response:'."\n".json_encode($response));
            //return true;
            return $response;
        }
        catch (DM_Exception_ClientException  $e) {
            log_message('error', 'aliyun dm mail failed, msg('.$e->getErrorCode().'):'.$e->getErrorMessage()); 
        }
        catch (DM_Exception_ServerException  $e) {      
            log_message('error', 'aliyun dm mail failed, msg('.$e->getErrorCode().'):'.$e->getErrorMessage()); 
        }
        
        return false;
    }
}