<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

if ( ! function_exists('dump'))
{
    /**
     * 浏览器友好的变量输出
     * @param mixed $var 变量
     * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
     * @param string $label 标签 默认为空
     * @param boolean $strict 是否严谨 默认为true
     * @return void|string
     */
    function dump($var, $echo=true, $label=null, $strict=true) {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        }else
            return $output;
    }
}

if ( ! function_exists('log_message'))
{
	/**
	 * Error Logging Interface
	 *
	 * We use this as a simple mechanism to access the logging
	 * class and send messages to be logged.
	 *
	 * @param	string	the error level: 'error', 'debug' or 'info'
	 * @param	string	the error message
	 * @return	void
	 */
	function log_message($level, $message)
	{
		static $_log;

		if ($_log === NULL)
		{
			// references cannot be directly assigned to static variables, so we use an array
			$_log = new Log();
		}

		$_log->write_log($level, $message);
	}
}

if ( ! function_exists('is_php'))
{
	/**
	 * Determines if the current version of PHP is equal to or greater than the supplied value
	 *
	 * @param	string
	 * @return	bool	TRUE if the current version is $version or higher
	 */
	function is_php($version)
	{
		static $_is_php;
		$version = (string) $version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
		}

		return $_is_php[$version];
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_really_writable'))
{
	/**
	 * Tests for file writability
	 *
	 * is_writable() returns TRUE on Windows servers when you really can't write to
	 * the file, based on the read-only attribute. is_writable() is also unreliable
	 * on Unix servers if safe_mode is on.
	 *
	 * @link	https://bugs.php.net/bug.php?id=54709
	 * @param	string
	 * @return	bool
	 */
	function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR === '/' && (is_php('5.4') OR ! ini_get('safe_mode')))
		{
			return is_writable($file);
		}

		/* For Windows servers and safe_mode "on" installations we'll actually
		 * write a file then read it. Bah...
		 */
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand());
			if (($fp = @fopen($file, 'ab')) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, 0777);
			@unlink($file);
			return TRUE;
		}
		elseif ( ! is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_https'))
{
	/**
	 * Is HTTPS?
	 *
	 * Determines if the application is accessed via an encrypted
	 * (HTTPS) connection.
	 *
	 * @return	bool
	 */
	function is_https()
	{
		if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
		{
			return TRUE;
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
		{
			return TRUE;
		}
		elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
		{
			return TRUE;
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_cli'))
{

	/**
	 * Is CLI?
	 *
	 * Test to see if a request was made from the command line.
	 *
	 * @return 	bool
	 */
	function is_cli()
	{
		return (PHP_SAPI === 'cli' OR defined('STDIN'));
	}
}

if ( ! function_exists('set_status_header'))
{
	/**
	 * Set HTTP Status Header
	 *
	 * @param	int	the status code
	 * @param	string
	 * @return	void
	 */
	function set_status_header($code = 200, $text = '')
	{
		if (is_cli()){
			return;
		}

        $server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
		if (empty($code) OR ! is_numeric($code)){
			log_message('error', 'Status codes must be numeric');
            header($server_protocol.' 404 not found', TRUE, 404);
            exit;
		}

		if (empty($text)){
			is_int($code) OR $code = (int) $code;
			$stati = array(
				100	=> 'Continue',
				101	=> 'Switching Protocols',

				200	=> 'OK',
				201	=> 'Created',
				202	=> 'Accepted',
				203	=> 'Non-Authoritative Information',
				204	=> 'No Content',
				205	=> 'Reset Content',
				206	=> 'Partial Content',

				300	=> 'Multiple Choices',
				301	=> 'Moved Permanently',
				302	=> 'Found',
				303	=> 'See Other',
				304	=> 'Not Modified',
				305	=> 'Use Proxy',
				307	=> 'Temporary Redirect',

				400	=> 'Bad Request',
				401	=> 'Unauthorized',
				402	=> 'Payment Required',
				403	=> 'Forbidden',
				404	=> 'Not Found',
				405	=> 'Method Not Allowed',
				406	=> 'Not Acceptable',
				407	=> 'Proxy Authentication Required',
				408	=> 'Request Timeout',
				409	=> 'Conflict',
				410	=> 'Gone',
				411	=> 'Length Required',
				412	=> 'Precondition Failed',
				413	=> 'Request Entity Too Large',
				414	=> 'Request-URI Too Long',
				415	=> 'Unsupported Media Type',
				416	=> 'Requested Range Not Satisfiable',
				417	=> 'Expectation Failed',
				422	=> 'Unprocessable Entity',

				500	=> 'Internal Server Error',
				501	=> 'Not Implemented',
				502	=> 'Bad Gateway',
				503	=> 'Service Unavailable',
				504	=> 'Gateway Timeout',
				505	=> 'HTTP Version Not Supported'
			);

			if (isset($stati[$code])){
				$text = $stati[$code];
			}else{
				log_message('error', 'No status text available. Please check your status code number or supply your own message text.');
			}
		}

		if (strpos(PHP_SAPI, 'cgi') === 0){
			header('Status: '.$code.' '.$text, TRUE);
		}else{
			header($server_protocol.' '.$code.' '.$text, TRUE, $code);
		}
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('remove_invisible_characters'))
{
	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('html_escape'))
{
	/**
	 * Returns HTML escaped variable.
	 *
	 * @param	mixed	$var		The input string or array of strings to be escaped.
	 * @param	bool	$double_encode	$double_encode set to FALSE prevents escaping twice.
	 * @return	mixed			The escaped string or array of strings as a result.
	 */
	function html_escape($var, $double_encode = TRUE)
	{
		if (empty($var))
		{
			return $var;
		}

		if (is_array($var))
		{
			foreach (array_keys($var) as $key)
			{
				$var[$key] = html_escape($var[$key], $double_encode);
			}

			return $var;
		}

        $config = Yaf_Registry::get('config');
		return htmlspecialchars($var, ENT_QUOTES, $config['application']['charset'], $double_encode);
	}
}

// ------------------------------------------------------------------------

if(!function_exists('ip_address')){
    function ip_address(){
        $ip = '';
		$unknown = 'unknown';  
        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR'])  && $_SERVER['HTTP_X_FORWARDED_FOR']  && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) ) {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        } elseif ( isset($_SERVER['REMOTE_ADDR'])  && $_SERVER['REMOTE_ADDR'] &&  strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) ) {  
            $ip = $_SERVER['REMOTE_ADDR'];  
        } elseif ( isset($_SERVER['HTTP_CLIENT_IP'])  && $_SERVER['HTTP_CLIENT_IP'] &&  strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown) ) {  
            $ip = $_SERVER['HTTP_CLIENT_IP'];  
        } 
        /*  
        处理多层代理的情况  
        或者使用正则方式：$ip = preg_match("/[d.]
        {7,15}/", $ip, $matches) ? $matches[0] : $unknown;  
        */  
        if (false !== strpos($ip, ',')) {
            $tmp = explode(',', $ip);
            $ip = $tmp[0]; 
        }
        
         return $ip;  
	}
}

if(!function_exists('get_var_from_conf')){
    function get_var_from_conf($filename, $filepath=APPLICATION_PATH .'/conf/'){
        $filename = basename($filename, '.php');
        $var = Yaf_Registry::get($filename);
		if(!$var){
            $filepath = rtrim($filepath).'/'. $filename .'.php';
            if(!file_exists($filepath)){
                lExit(501, 'file not exists, file:'. $filepath);
                return false;
            }
            
            Yaf_Loader::import($filepath);
            Yaf_Registry::set($filename, $$filename);
            return $$filename;
        }else{
            return $var;
        }
	}
}

if(!function_exists('http')){
    function http($args = array()){
        $request = new Yaf_Request_Http();
        $ch = curl_init();
        $config = Yaf_Registry::get('config');
        $cookiePrefix = $config['application']['cookie_prefix'];

        $args = array_merge(array(
            'data' => array(),
            'method' => 'get',
            'type' => 'json',
            'cookie' => array(),
            'header' => array('Connection: keep-alive', 'Expect: '),
            'withheader' => isset($args['withheader']) ? $args['withheader'] : true,
        ) ,$args);
        if(strtolower($args['method']) === 'post'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args['data']);
        }else if(strtolower($args['method']) === 'put'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: PUT"));
            curl_setopt($ch, CURLOPT_POSTFIELDS, (is_array($args['data']) || is_object($args['data'])) ? json_encode($args['data'], JSON_UNESCAPED_UNICODE) : $args['data']);
        }else{
            $data = array();
            foreach ($args['data'] as $key => $value) {
                $data[] = $key.'='.$value;
            }
            $data = implode('&', $data);
            $args['url'] .= (strpos($args['url'] ,'?')===false?'?':'&').$data;
        }
        curl_setopt($ch, CURLOPT_URL, $args['url']);
        //跟踪301
        curl_setopt($ch ,CURLOPT_FOLLOWLOCATION ,1);
        //设置 referer
        if(isset($_SERVER['HTTP_REFERER'])){
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
        }
        // 头部信息
        curl_setopt($ch ,CURLOPT_HEADER, $args['withheader']);
        // 返回字符串，而非直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 30秒超时
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        // cookie
        if($args['cookie']!==false){
            $args['cookie'] = array_merge($request->getCookie(), $args['cookie']);
            $cookie = array();
            foreach ($args['cookie'] as $key => $value) {
                if($cookiePrefix !== '' && strpos($key, $cookiePrefix)===0){
                    $key = str_replace($cookiePrefix, '', $key);
                }
                $cookie[] = urlencode($key) . '=' .urlencode($value);
            }
            $cookie = implode(';', $cookie);
            curl_setopt($ch ,CURLOPT_COOKIE, $cookie);
        }

        // http头
        curl_setopt($ch ,CURLOPT_HTTPHEADER, $args['header']);
        curl_setopt($ch ,CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.18 Safari/537.36');
        $args['auth'] && curl_setopt($ch, CURLOPT_USERPWD, $args['auth']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        
        log_message('debug', 'request remote server'. "\n" .'url: ['. $args['url'] .']'."\n".'param: ['. json_encode($args['data'], JSON_UNESCAPED_UNICODE) .']');
        $result = curl_exec($ch);

        //lExit(curl_getinfo($ch));
        
        $tmp = $result;
        $from = strtoupper(mb_detect_encoding($tmp));
        $from == false && $from = 'GB2312';
        if($from != 'UTF-8'){
            $tmp = mb_convert_encoding($tmp, 'UTF-8', $from);
        }
        log_message('debug', 'result: ['. $tmp .']');

        curl_close($ch);

        if($args['withheader']){
            $header_body = preg_split("#\r\n\r\n#" ,$result ,2);//$header_body[0]是返回头的字符串形式，$header_body[1]是返回数据的字符串形式
            $headers = preg_split("#\r\n#", $header_body[0] ,2);//$headers是返回头的数组
            $status = preg_split("# #" ,$headers[0] ,3);//返回头第一行，用空格分隔，形成数组：协议、状态码、状态码对应的英文字符
            $code = isset($status[1]) ? $status[1] : '502';//状态码，如200、302、404
            $text = isset($status[2]) ? $status[2] : '第三方响应超时';
            $data = isset($header_body[1]) ? $header_body[1] : '';//返回数据
            
            $_is_image = false;
            // 头部写入
            $headers = preg_split("#\r\n#", $headers[1]);
            foreach ($headers as $_header) {
                if(preg_match("#^Content-Type:\s*image.*#", $_header)===1){
                    $_is_image = true;
                    header($_header);
                    break;
                }
            }

            if($_is_image){
                header('Content-Length:'. strlen($data));
                return $data;
            }
        }else{
            $code = 200;
            $data = $result;
        }

        if($args['type']==='json'){
            if($result === false){
                $return = array();
                $return['code'] = 1;
                $return['message'] = 'requrest remote server failed';
                return $return;
            }

            $from = mb_detect_encoding($data);
            if(strtoupper($from) != 'UTF-8'){
                $data = mb_convert_encoding($data, 'UTF-8', $from);
            }

            $return = json_decode($data ,true);

            if(!$return){
                $return = array();
                $return['code'] = 2;
                $return['message'] = '返回数据非json格式';
                return $return;
            }

            if(isset($return['error'])){
                $return = array();
                $return['code'] = 9999;
                $return['message'] = isset($return['error']['message']) ? $return['error']['message'] : $data;
                return $return;
            }
        }else if($args['type']==='xml'){
            $from = strtoupper(mb_detect_encoding($data));
            $from == false && $from = 'GB2312';
            if($from != 'UTF-8'){
                $data = mb_convert_encoding($data, 'UTF-8', $from);
            }
            $data = str_replace($from, 'UTF-8', $data);
            
            $return = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

            if(!$return){
                $return = array();
                $return['code'] = 2;
                $return['message'] = '返回数据非xml格式';
                $return['source_code'] = $data;
                return $return;
            }

            if($args['withheader']){
                $headers = preg_split('/\n/', $headers[1]);
                foreach($headers as $v){
                    list($key, $value) = explode(': ', $v);
                    $return['header'][$key] = rtrim($value); 
                }
            }
            $return['code'] = $code==200 ? 0 : $code;
            $return['source_code'] = $data;
        }else{
            $return = array();
            $return['code'] = 0;
            $return['message'] = $data;
        }

        return $return;
   }
}

if(!function_exists('cookie')){
    /**
     * Set cookie
     *
     * Accepts an arbitrary number of parameters (up to 7) or an associative
     * array in the first parameter containing all the values.
     *
     * @param	string|mixed[]	$name		Cookie name or an array containing parameters
     * @param	string		$value		Cookie value
     * @param	int		$expire		Cookie expiration time in seconds
     * @param	string		$domain		Cookie domain (e.g.: '.yourdomain.com')
     * @param	string		$path		Cookie path (default: '/')
     * @param	string		$prefix		Cookie name prefix
     * @param	bool		$secure		Whether to only transfer cookies via SSL
     * @param	bool		$httponly	Whether to only makes the cookie accessible via HTTP (no javascript)
     * @return	void
     */
    function cookie($name, $value = null, $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE, $httponly = FALSE)
    {
        $config = Yaf_Registry::get('config');
        
        if ($prefix === '' && $config['application']['cookie_prefix'] !== '')
        {
            $prefix = $config['application']['cookie_prefix'];
        }
              
        if($name === null){
            foreach($_COOKIE as $k=>$v){
                if($prefix !== '' && strpos($k, $prefix)===0){
                    $k = str_replace($prefix, '', $k);
                }
                cookie($k, null);
            }
            return true;
        }
        
        if(is_string($name) && func_num_args()===1){
            return isset($_COOKIE[$prefix.$name]) ? $_COOKIE[$prefix.$name] : null;
        }
        
        if (is_array($name))
        {
            // always leave 'name' in last place, as the loop will break otherwise, due to $$item
            foreach (array('value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name') as $item)
            {
                if (isset($name[$item]))
                {
                    $$item = $name[$item];
                }
            }
        }

        if ($expire === '' && $config['application']['cookie_expire'] !== '')
        {
            if($config['application']['cookie_expire']-0>0){
                $expire = time() + $config['application']['cookie_expire'];
            }else{
                $expire = 0;
            }
        }elseif(! is_numeric($expire)){
            $expire = time() - 86500;
        }else{
            $expire = ($expire > 0) ? time() + $expire : 0;
        }
        
        if($value === null){
            $expire = time() - 86500;
        }
        
        if ($domain === '' && $config['application']['cookie_domain'] != '')
        {
            $domain = $config['application']['cookie_domain'];
        }

        if ($path === '/' && $config['application']['cookie_path'] !== '/')
        {
            $path = $config['application']['cookie_path'];
        }

        if ($secure === FALSE && $config['application']['cookie_secure'] === TRUE)
        {
            $secure = $config['application']['cookie_secure'];
        }

        if ($httponly === FALSE && $config['application']['cookie_httponly'] !== FALSE)
        {
            $httponly = $config['application']['cookie_httponly'];
        }

        setcookie($prefix.$name, $value, intval($expire), $path, $domain, $secure, $httponly);
     }
 }

 
 if ( ! function_exists('create_captcha'))
{
	/**
	 * Create CAPTCHA
	 *
	 * @param	array	$data		data for the CAPTCHA
	 * @param	string	$img_path	path to create the image in
	 * @param	string	$img_url	URL to the CAPTCHA image folder
	 * @param	string	$font_path	server path to font
	 * @return	string
	 */
	function create_captcha($conf)
	{
        //$func = array('Captcha_Elephant', 'Captcha_Animation', 'Captcha_Twist');
        $func = array('Captcha_Elephant', 'Captcha_Animation');
        $index = array_rand($func);
        
        $captcha = new $func[$index]($conf);
        $image = $captcha->doImg();
        $code = $captcha->getCode();
        return array('code' => $code, 'image' =>$image );
    }
}

if ( ! function_exists('is_id_card')){
	/**
	 * 检查身份证号是否有效
	 * @param String $number
	 * @return boolean
	 */
	function is_id_card($number) {
		if(!preg_match('/^\d{17}(\d|x)$/i',$number)){
			return false;
		}
		// 转化为大写，如出现x
		$number = strtoupper($number);
		$wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		$ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		$sigma = 0;
		for ($i = 0;$i < 17;$i++) {
			$b = (int) $number{$i};
			$w = $wi[$i];
			$sigma += $b * $w;
		}
		$snumber = $sigma % 11;
		$check_number = $ai[$snumber];
		
		if ($number{17} == $check_number) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists('get_sex_by_id_card')){
	/**
	 * 根据身份证提取性别
	 * @param int $cid
	 * @return string
	 */
	function get_sex_by_id_card($cid) {
		if (!is_id_card($cid)){
			return 'unknown';
		}

		$sexint = (int)substr($cid,16,1);
		return $sexint % 2 === 0 ? 'woman' : 'man';
	}
}

if ( ! function_exists('get_birthday_by_id_card')){
    /**
     * 根据身份证获取生日
     * @param $cid
     * @return string
     */
    function get_birthday_by_id_card($cid){
        if (!is_id_card($cid)){
			return 'unknown';
		}
        return substr($cid,6,4)."-".substr($cid,10,2).'-'.substr($cid,12,2);
    }
}

if ( ! function_exists('is_email')){
    /**
     * 是否是邮箱
     * @param string $email
     * @return bool
     */
    function is_email($email){
        $exp = '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/';
        return preg_match($exp,$email);
    }
}

if ( ! function_exists('is_integer')){
	/**
	 * 是否是数字
	 * @param string $number
	 * @return bool
	 */
	function is_integer($number){
		$exp = '/^[0-9]*$/';
		return preg_match($exp,$number);
	}
}

if ( ! function_exists('is_phone')){
    /**
     * 是否是手机
     * @param string $phone 手机号码
     * @return bool
     */
    function is_phone($phone){
        $exp = '/^1[3|4|5|6|7|8|9][0-9]{9}$/';
        return preg_match($exp,$phone);
    }
}

if(!function_exists('rand_str')){
    function rand_str($len){
        //$number = '0123456789';
        $number = '23456789';
        //$alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alpha = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $rt = '';
        for($i=0;$i<$len;$i++){
            if($i&1){
                $rt .= $number{mt_rand(0, strlen($number)-1)};
            }else{
                $rt .= $alpha{mt_rand(0, strlen($alpha)-1)};
            }
        }
        return $rt;
    }
}

if(!function_exists('arrayTo2d')){
    function arrayTo2d($array){
        $rt = array();
        foreach($array as $k=>$v){
            if(is_array($v)){
                $rt = array_merge($rt, arrayTo2d($v));
            }else{
                $rt[$k] = $v;
            }
        }
        
        return $rt;
    }
}

if(!function_exists('is_bank_card')){
    function is_bank_card($bankCard){
        $bankCard = str_split($bankCard);
        $last_n = $bankCard[count($bankCard)-1];
        krsort($bankCard);
        $i = 1;
        $total = 0;
        foreach ($bankCard as $n){
            if($i%2==0){
                $ix = $n*2;
                if($ix>=10){
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                }else{
                    $total += $ix;
                }
            }else{
                $total += $n;
            }
            $i++;
        }
        $total -= $last_n;
        $total *= 9;
        
        return $last_n == ($total%10);
    }
}

if(!function_exists('passwd_strength')){
    function passwd_strength($passwd){
        $strength = 1;//密码强度：弱

        $count = 0;
        if(preg_match('/[a-z]/', $passwd)){
            ++$count;
        }
        
        if(preg_match('/[A-Z]/', $passwd)){
            ++$count;
        }
        
        if(preg_match('/\d/', $passwd)){
            ++$count;
        }
        
        if(preg_match('/[`~!@#$%^&*()_+\-={}:"<>?\[\];\',.\/ \\|]/', $passwd)){
            ++$count;
        }

        $length = strlen($passwd);
        if(($length >= 12 && $count >=3) || ($length >= 8 && $count >= 4)){
            // 8位及以上，包含4种 强
            // 12位及以上，包含3种 强
            $strength = 3;
        }else if(($length >= 12 && $count >= 2) || ($length >= 8 && $count >= 3)){
            // 8位及以上，包含3种 中
            // 12位及以上，包含2种 中
            $strength = 2;
        }
        
        return $strength;
    }
}

if(!function_exists('lExit')){
    function lExit($code=[], $msg=null){
        header('content-type:application/json;charset=utf-8', true);
        if(is_object($code) || is_array($code)){
            $body4log = json_encode(['response'=>['data'=>$code], '_a'=>ENCRYPT_OUTPUT], JSON_UNESCAPED_UNICODE);//保存加密前的数据，写入日志
            $body = json_encode([
				'response' => ENCRYPT_OUTPUT ? DES::encodeWithBase64(['data'=>$code]):['data'=>$code],
				'_a'=> ENCRYPT_OUTPUT,
				'state'=>true
			],JSON_UNESCAPED_UNICODE);
		}else if(is_bool($code)){
            $body4log = $body = json_encode(['response'=>['data'=>$code], '_a'=>false, 'state'=>true], JSON_UNESCAPED_UNICODE);
        }else{
            if(is_null($msg)){
                $error = get_var_from_conf('error');
                if(isset($error[$code])){
                    $body4log = $body = json_encode(['error'=>['code'=>$code, 'message'=>$error[$code]['message']]], JSON_UNESCAPED_UNICODE);
                }else{
                    $body4log = $body = json_encode(['response'=>['data'=>$code], '_a'=>false, 'state'=>true], JSON_UNESCAPED_UNICODE);
                }
            }else{
                $body4log = $body = json_encode(['error'=>['code'=>$code, 'message'=>$msg]], JSON_UNESCAPED_UNICODE);
            }
        }
        log_message('all', 'request_id:'.Yaf_Registry::get('request_id')."\tip:". ip_address() ."\n    ".'response:'.$body4log."\n");
		exit($body);
    }
}

if (!function_exists('aExit')) {
    function aExit(Array $error)
    {
        lExit($error['code'], $error['message']);
    }
}

if (!function_exists('array_index')) {
    /**
     * @param $arr
     * @param $key
     * @param bool $group   按照 $key 为索引分组
     * @param bool|Closure $extra 是否跳过 $Key | 接受闭包对 $item 进行处理
     * @return array
     */
    function array_index($arr, $key, $group=false, $extra=false)
    {
        $result = [];

        if (is_array($arr)) {
            foreach ($arr as $item) {
                if (array_key_exists($key, $item)) {
                    $tempItem = $item;
                    if ($extra) {
                        if ($extra instanceof Closure) {
                            $tempItem = $extra($item);
                        } else {
                            unset($tempItem[$key]);
                        }
                    }
                    $group ? $result[$item[$key]][] = $tempItem : $result[$item[$key]] = $tempItem;
                } else {
                    $parentStack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1];
                    $parentStack['message'] = 'missing desired key: '.$key;
                    log_message('error', print_r($parentStack, true));
                }
            }
        }

        return $result;
    }
}

if(!function_exists('line2Hump')){
    /**
     * 下划线转驼峰
     * @param string $str 字符串 
     * @return string
    */
   function line2Hump($str)
   {
       $str = preg_replace_callback('/([-_]+([a-z]{1}))/i',function($matches){
           return strtoupper($matches[2]);
       },$str);
       return $str;
   }
}

if(!function_exists('hump2Line')){
    /**
     * 驼峰转下划线
     * @param string $str 字符串
     * @return string
     */
    function hump2Line($str){
        $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
            return '_'.strtolower($matches[0]);
        },$str);
        return $str;
    }
}

if (!function_exists('checkDateIsValid'))
{
    /**
     * 校验日期格式是否正确
     *
     * @param string $date 日期
     * @param array $formats 需要检验的格式数组
     * @return boolean
     */
    function checkDateIsValid($date, $formats = array("Y-m-d", "Y/m/d"))
    {
        $unixTime = strtotime($date);
        if (!$unixTime) { //strtotime 转换不对，则日期格式不对。
            return false;
        }
        // 校验日期的有效性，只要满足其中一个格式就OK
        foreach ($formats as $format) {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('checkWeightUnit')) {
    /**
     * 验证单位,若单位不存在默认为g
     * @param $unit
     * @return string
     */
    function checkWeightUnit($unit) {
        switch ($unit) {
            case 'g':
            case 'ct':
            case 'mi':
                break;
            default:
                $unit = 'g';
        }

        return $unit;
    }
}

if (!function_exists('checkNumber')) {
    /**
     * 截取以数字开头的字符串中的数字
     * @param $string
     * @return float
     */
    function checkNumber($string) {
        return Formatter::FormatData($string, 6)*1;
    }
}

if (!function_exists('weightTransform')) {
    /**
     * 重量转换
     * @param number $curWeight 待转换重量
     * @param string $curUnit 待转换重量单位
     * @param string $tarUnit 目标重量单位
     * @returns boolean|number
     */
    function weightTransform($curWeight, $curUnit, $tarUnit) {
        $curUnitRadixToG = getWeightUnitRadixToG($curUnit);
        $tarUnitRadixToG = getWeightUnitRadixToG($tarUnit);

        if (is_numeric($curWeight) && $curUnitRadixToG !== false && $tarUnitRadixToG != false) {
            return Formatter::FormatData($curWeight*$curUnitRadixToG/$tarUnitRadixToG, Formatter::DECIMAL_WEIGHT);
        }

        return false;
    }
}

if (!function_exists('getWeightUnitRadixToG')) {
    /**
     * 获得指定单位转换成克的转换率
     * @param string $needToTsfUnit 单位
     * @returns boolean|number
     */
    function getWeightUnitRadixToG($needToTsfUnit) {
        $radix = false;
        switch ($needToTsfUnit) {
            case 'g': $radix = 1;break;
            case 'ct': $radix = 0.2;break;
            case 'mi': $radix = 0.2/100;break;
        }

        return $radix;
    }
}

if (!function_exists('get_attr')) {
    /**
     * 递归获取层级菜单
     * @param $arr
     * @param $pid
     * @return array
     */
    function get_attr($arr,$pid){
        $tree = array();                                //每次都声明一个新数组用来放子元素
        foreach($arr as $v){
            if($v['pid'] == $pid){                      //匹配子记录
                $v['children'] = get_attr($arr,$v['id']); //递归获取子记录
                if($v['children'] == null){
                    unset($v['children']);             //如果子元素为空则unset()进行删除，说明已经到该分支的最后一个元素了（可选）
                }
                $tree[] = $v;                           //将记录存入新数组
            }
        }
        return $tree;                                  //返回新数组
    }
}

if (!function_exists('genPrimaryKey')) {
    // 生成主键
    function genPrimaryKey()
    {
        $m = microtime();
        $ms = explode(' ', $m);
        $ms1 = $ms[1];

        // 组合新的key=(UNIX时间戳秒+用户ID中的后三位微妙+当前时间戳的微妙)
        $ms2 = explode('.', $ms[0]);
        $ms2 = $ms2[1];
        $ms2 = substr($ms2, 0, 6);

        // 随机数
        $rand = mt_rand(0, 999);
        if ($rand < 100)
        {
            if ($rand < 10) {
                $rand = '00'.$rand;
            } else {
                $rand = '0'.$rand;
            }
        }
        // 10位UNIX时间戳(秒)+6位当前时间微妙+3位随机数(共计18位ID)
        $coder = $ms1.$ms2.$rand;

        return $coder;
    }
}

if (!function_exists('checkAndSet')) {
    /**
     * 初始化、处理 数据
     * @param $container
     * @param array $fields
     * @param null $default
     * @param Closure|null|string $func
     * @return mixed
     */
    function checkAndSet(&$container, Array $fields, $default=null, $func=null)
    {
        foreach ($fields as $item) {
            if (isset($container[$item])) {
                $container[$item] = is_null($func) ? $container[$item] : $func($container[$item]);
            } else {
                $container[$item] = $default;
            }
        }
    }
}


if (!function_exists('p')) {
	/**
	 * 格式化输出
	 * @param $arr
	 */
	function p($arr,$type=0){
		echo "<pre>";
		if($type){
			var_dump($arr);die;
		}
		print_r($arr);die;
	}
}

if (!function_exists('failed_and_exit')) {
    /**
     * 如果数据库操作是 false 则退出
     * @param bool $state
     * @param string|array $error
     * @param false|Database_Drivers_Pdo_Mysql $db
     */
    function failed_and_exit($state, $error, $db)
    {
        if ($state===false) {
            !empty($db) && $db->rollBack();
            is_array($error) ? lExit($error[0], $error[1]) : lExit($error);
        }
    }
}

if ( ! function_exists('today_is_birthday')){
    /**
     * 根据身份证获取生日
     * @param string $cid
     * @return bool
     */
    function today_is_birthday($cid){
        if (!is_id_card($cid)){
            return 'unknown';
        }
        return substr($cid,10,4) == date('md');
    }
}

if ( ! function_exists('merge_by_keys')) {
    /**
     * 将右边的二维数组元素逐个合并到左边的二维数组中
     * @param array $leftArr
     * @param array $rightArr
     * @param string|array $keys 连接两个数组的公共键。 可以指定多个键作为唯一键（多列主键的情况）
     * @return array
     * @desc merge_by_keys($l, $r, ['goods_user', 'goods_line']);
     */
    function merge_by_keys(Array $leftArr, $rightArr, $keys)
    {
        if (empty($rightArr)) {
            return $leftArr;
        }

        $result = [];
        $keys = (array)$keys;
        $_idx2RightArr = [];

        foreach ($rightArr as $value) {
            $_idx = '';
            foreach ($keys as $_k) {
                $_idx .= $value[$_k].'_';
            }
            $_idx2RightArr[$_idx][] = $value;
        }

        foreach ($leftArr as $item) {
            $_idx = '';
            foreach ($keys as $_k) {
                $_idx .= $item[$_k].'_';
            }
            if (isset($_idx2RightArr[$_idx])) {
                foreach($_idx2RightArr[$_idx] as $_val) {
                    $result[] = $item + $_val;
                }
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }
}

if (!function_exists('df')) {
    function df($data, $channel='error', $stack=false)
    {
        $pStack = $stack===false ? [] : debug_backtrace()[$stack];
        log_message($channel, print_r(['stack'=>$pStack, 'data'=>$data], true));
    }
}

if (!function_exists('buildSql')) {
    function buildSql($preparedSql, $bindParams)
    {
        $isInsert  = false;
        $preparedSql = trim($preparedSql);
        if ($preparedSql{0}==='(') {
            $preparedSql{0} = $preparedSql{strlen($preparedSql)-1} = ' ';
        }
        if (false !== strpos($preparedSql, 'INSERT')) {
            $isInsert = true;
        }

        $index = 0;
        $preparedSql = PHP_EOL. trim($preparedSql) .PHP_EOL;
        return preg_replace_callback('/(:\w+|\?)/', function ($match) use ($bindParams, &$index, $isInsert) {
            $paramIndex = $isInsert ? ltrim($match[1], ':') : $match[1];
            $replacement = is_array($bindParams[$index]) ? $bindParams[$index][$paramIndex] : $bindParams[$index];
            $index++;
            return "'$replacement'";
        }, $preparedSql);
    }
}

if (!function_exists('fillingMissedFields')) {
    /**
     * 为二位结果数组填充缺失的字段（数据库未返回结果的情形）
     * @param array $result
     * @param array $fields
     * @param mixed $default 默认填充的值
     * @param array $unsetKeys 要去除的多余数据
     * @param Closure|null|string $func
     * @return array
     */
    function fillingMissedFields(Array $result, Array $fields, $default='', $unsetKeys = [], $func=null)
    {
        foreach ($result as &$_item) {
            foreach ($unsetKeys as $_key) {
                unset($_item[$_key]);
            }
            checkAndSet($_item, $fields, $default, $func);
        }

        return $result;
    }
}