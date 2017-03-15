<?php
defined('BASE_PATH') OR exit('No direct script access allowed');
/**
 * QQ自动登录
 * @desc $qq = new Login(15000103);$login = $qq->login();//15000103是广点通的appid
 * @author fangh@me.com
 */
class Login{

#登录相关---start
    private $pre_login_url = '';
    private $check_login_url = '';
    private $login_url = '';
    private $uin = '';
    private $password = '';
    private $cookie = array();
    private $ip = '';
    private $verifycode = '';
    private $pt_vcode_v1 = '';
    private $ptvfsession = '';
    private $salt = '';
    private $login_sig = '';
    private $cookiejar = '';
#登录相关---end	

    /**
     * 初始化参数
     * @param string $appId 应用的appid
     */
    public function __construct($appId){

        $this->cookiejar = BASE_PATH .'/cookie/qq.cookie';
        
        if(!file_exists(BASE_PATH .'/cookie/')){
            mkdir(BASE_PATH .'/cookie/', 666);
        }
        
        if(!file_exists($this->cookiejar)){
            touch($this->cookiejar);
        }
        
        $qq = get_var_from_conf('qq');
        if(!is_array($qq[$appId])){
            exit(json_encode(array('rtn'=>999, 'error_msg'=>'无效的appId')));
        }
        $qq = $qq[$appId];
        
        $this->uin = $qq['UIN'];//登录QQ号码
        $this->password = $qq['PASSWORD'];//QQ密码
        $this->u1 = $qq['U1'];//回调地址
        $this->appId = $appId;//回调地址
        $this->pre_login_url = $qq['PRE_LOGIN_URL'];//登录页面url
        $this->check_login_url = $qq['CHECK_LOGIN_URL'];//获取验证码、salt、session
        $this->login_url = $qq['LOGIN_URL'];//提交登陆

        $this->api_url = $qq['API_ADDR'];//获取广告列表
        $this->ip = long2ip(mt_rand($qq['LONG_MIN'], $qq['LONG_MAX']));//生成ip
    }

    /**
     * 访问http://xui.ptlogin2.qq.com/cgi-bin/xlogin，获取登录所需的cookie
     * @return boolean 成功 or 失败
     */
    public function login(){
        $res = $this->http($this->pre_login_url, null, 'GET');
        if(!$res){
            return false;
        }

        $this->login_sig = $this->getCookie('pt_login_sig');
        return $this->checkLogin();
    }

    /**
     * 获取验证码，加密密码所需的salt和ptvfsession
     * @return boolean 成功 or 失败
     */
    public function checkLogin(){
        $data = array();
        $data['regmaster'] = '';
        $data['pt_tea'] = 1;
        $data['pt_vcode'] = 1;
        $data['uin'] = $this->uin;
        $data['appid'] = $this->appId;
        $data['js_ver'] = 10148;
        $data['js_type'] = 1;
        $data['login_sig'] = $this->login_sig;
        $data['u1'] = $this->u1;
        $data['r'] = '0.5311711819376796';
        $res = $this->http($this->check_login_url, $data, 'GET');
        if(!$res){
            return false;
        }

        preg_match('/\(([^\)]+)\)/', $res, $matches);

        $matches = explode('\',\'', trim($matches[1], '\''));

        $this->pt_vcode_v1 = $matches[0];
        $this->verifycode = $matches[1];

        //不能直接传递hex2bin后的二进制串，先转成16进制，并且去掉\x，然后在js的$.Encryption.getEncryption前，调用hexchar2bin，转成二进制，作为加密password的参数之一
        $this->salt = str_replace('\\x','',$matches[2]);

        $this->ptvfsession = $matches[3];

        if($this->ptvfsession === ''){
            $this->_error = '需要输入验证码，无法自动登录'."\n";
            return false;
        }

        return $this->doLogin();
    }

    /**
     * 提交登陆
     * @return boolean 成功 or 失败
     */
    public function doLogin(){
        $loginJs = BASE_PATH.DIRECTORY_SEPARATOR.'static/login.js';
//echo 'node '. $loginJs.' '. $this->salt .' '.$this->login_sig.' '.$this->ptvfsession .' '. $this->verifycode.' '. $this->password .' '. $this->uin."\n\n";

        $loginUrl = exec('node '. $loginJs .' '. $this->salt .' '. $this->login_sig .' '.$this->ptvfsession .' '. $this->verifycode.' '. $this->password .' '. $this->uin);
//echo $loginUrl;exit;
        $res = $this->http($loginUrl, null, 'GET', true);
        if(!$res){
            return false;
        }

        preg_match('/\(([^\)]+)\)/', $res, $matches);
        $matches = explode(',', str_replace('\'', '', $matches[1]));

        if($matches[4]==='登录成功！'){
            return true;
        }

        $this->_error = $matches[4];
        return false;
    }

    /**
     * 从cookiejar文件所设置的cookie，存入$this->cookie，待需要时调用
     * @param string $cname cookie名
     * @return string cookie值
     */
    public function getCookie($cname='', $domain=''){
        $cookie = array();
        $lines = file($this->cookiejar);

        foreach($lines as $line) {
            if($line[0] != '#' && substr_count($line, "\t") == 6) {
                $tokens = explode("\t", $line);

                if($domain!==''){
                    $tmp = trim($tokens[0])===$domain ? trim($tokens[6]) : '';
                }else{
                    $tmp = trim($tokens[6]);
                }
                    
                if(!empty($cname) && trim($tokens[5])===$cname){
                    return $tmp;
                }else{
                    $cookie[$tokens[5]] = $tmp;
                }

            }
        }
        return empty($cname) ? $cookie : '';
    }

    /**
     * 发送http请求
     * @param string $url 请求url
     * @param string $data 请求的参数
     * @param string $method 请求方式
     * @param string $returnHeader 是否输出返回头
     * @return boolean|string 返回结果
     */
    public function http($url, $data=null, $method='GET', $returnHeader=false)
    {
        $ch = curl_init();
        if(!empty($data))
        {
            if($method=='POST'){
                curl_setopt($ch, CURLOPT_PORT, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }else{

                $param = array();
                foreach($data as $_k=>$_v){
                        $param[] = $_k.'='.$_v;
                }
                $url = strpos($url, '?')===false ? $url.'?'.implode('&', $param) : $url.'&'.implode('&', $param);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        $returnHeader && curl_setopt($ch, CURLOPT_HEADER, $returnHeader);//返回结果包含返回头

        $header = array(
                        'X-FORWARDED-FOR: '.$this->ip, //伪造客户端ip
                        'CLIENT-IP: '.$this->ip,//伪造客户端ip
                        'Connection: keep-alive', 
                        'Accept-Language: zh-CN,zh;q=0.8',);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置http请求头
        curl_setopt($ch, CURLOPT_REFERER, $this->pre_login_url);//http请求头referer
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36');


        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiejar);//存储cookie的目标文件
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiejar);//发送cookie的来源文件


        //curl_setopt($ch, CURLOPT_VERBOSE, true);//如果你想CURL报告每一件意外的事情,设置这个选项为一个非零值
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $res = curl_exec($ch);

        if($res===false){
            $curl_error = curl_error($ch);
            $curl_info = curl_getinfo($ch);

            Log::error('curl error :'. $curl_error ."\n".'curl_get_info: '. print_r($curl_info, true));
            $this->_error = '<pre>Curl error: ' . curl_error($ch) .'<br/>curl_get_info: '. print_r($curl_info, true).'</pre>';
            curl_close($ch);

            return false;
        }

        curl_close($ch);
        return $res;
    }

    /**
     * 返回错误信息
     * @return string
     */
    public function errorInfo(){
        return $this->_error;
    }
    
    /**
     * 返回qq登录账号
     * @return string
     */
    public function getUin(){
        return $this->uin;
    }

    /**
     * 计算广点通防csrf的token
     * @return boolean
     */
    public function csrfToken(){
        if($this->g_tk!=''){
            return $this->g_tk;
        }
        
        $str = $this->getCookie('skey', '.qq.com');
        $hash = 5381;
        if(!!$str){
            for($i=0,$len=strlen($str); $i<$len; ++$i){
                $hash += ($hash<<5)+$this->uniord(mb_substr($str,$i,1,'utf-8'));
            }
        }

        return $this->g_tk = $hash & 0x7fffffff;
    }

    /**
     * 字符转为unicode编码值
     * @param string $str 需要计算的字符
     * @param string $from_encoding 输入字符的编码
     * @return int
     */
    public function uniord($str,$from_encoding=false){
        $from_encoding=$from_encoding ? $from_encoding : 'UTF-8';

        if(strlen($str)==1){ return ord($str);} 


        $str=mb_convert_encoding($str, 'UCS-4BE', $from_encoding);
        $tmp=unpack('N',$str);
        return $tmp[1];
    }
}
