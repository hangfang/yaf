<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class WechatController extends Yaf_Controller_Abstract {

    public function init(){
        if(!Wechat_MsgModel::initDomain()){
            lExit(502, '当前企业未接入微信公众号');
        }
        
        if(strtolower($this->_request->controller)==='msg'){
            return true;
        }
        
        if(empty($_SESSION['wechat']['access_token'])){
            if(!in_array(strtolower($this->_request->action), ['login', 'code'])){
                header('location: /wechat/auth/login?redirect_uri='. urlencode(BASE_URL.$this->_request->getRequestUri()));
                exit;
            }
        }else if(time()-$_SESSION['wechat']['access_token_time']>7200){//access_token过期
            if(time()-$_SESSION['wechat']['refresh_token_time']<30*86400){//access_token过期
                return $this->refreshToken();
            }

            header('location: /wechat/auth/login?redirect_uri='. urlencode(BASE_URL.$this->_request->getRequestUri()));
            exit;
        }
    }
    
    /**
     * 刷新access_token
     * @author fanghang@fujiacaifu.com
     */
    protected function refreshToken(){
        if(empty($_SESSION['wechat']['refresh_token'])){//此时去微信登录页面重新授权
            log_message('error', 'get refresh_token failed, wechat: '. print_r($_SESSION['wechat'], true));
            return false;
        }
        
        $args = ['url'=>sprintf(Yaf_Registry::get('WECHAT_API_HOST').'/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s', Yaf_Registry::get('WECHAT_APP_ID'), $_SESSION['wechat']['refresh_token'])];
        $result = http($args);
        if(isset($result['errcode'])){
            log_message('error', 'get sns_user_info failed, wechat: '. print_r($result, true));
            return false;
        }

        $_SESSION['wechat']['access_token'] = $result['access_token'];
        $_SESSION['wechat']['access_token_time'] = time()-50;//防止刚好7200秒，导致token过期
        $_SESSION['wechat']['refresh_token'] = $result['refresh_token'];
        $_SESSION['wechat']['refresh_token_time'] = time()-50;//30天内有效，用来刷新access_token
        $_SESSION['wechat']['openid'] = $result['openid'];
        
        return true;
    }
    
    /**
     * 获取access_token
     * @param string code 微信返回的授权码
     * @param string state 微信带回的state
     */
    protected function getAccessToken($code, $state){
        $args = ['method'=>'get', 'url'=>sprintf(Yaf_Registry::get('WECHAT_API_HOST').'/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code', Yaf_Registry::get('WECHAT_APP_ID'), Yaf_Registry::get('WECHAT_APP_SECRET'), $code)];
        $result = http($args);
        if(isset($result['errcode'])){
            log_message('error', '微信授权失败, result: '. print_r($result, true));
            
            $url = '/wechat/auth/index';
            if($tmp=BaseModel::getQuery('redirect_uri')){
                $url = $tmp;
            }
        
            header('refresh:3;url='.Yaf_Registry::get('WECHAT_OPEN_HOST').'/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect', Yaf_Registry::get('WECHAT_APP_ID'), urlencode(BASE_URL.'/wechat/auth/code?redirect_uri='.$url));
            exit(isset($result['errmsg']) ? $result['errmsg'] : '微信授权失败');
        }
        
        return $result;
    }
}