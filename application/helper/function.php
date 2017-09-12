<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

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
                lExit(json_encode(array('rtn'=>501, 'error_msg'=>'file not exists, file:'. $filepath)));
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
            'auth'=>isset($args['auth']) ? $args['auth'] : false,
            'header' => array('Connection: keep-alive', 'Expect: '),
            'withheader' => isset($args['withheader']) ? $args['withheader'] : true,
        ) ,$args);
        if(strtolower($args['method']) === 'post'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args['data']);
        }else if(strtolower($args['method']) === 'put'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: PUT"));
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($args['data']) ? json_encode($args['data'], JSON_UNESCAPED_UNICODE) : $args['data']);
        }else{
            $data = array();
            foreach ($args['data'] as $key => $value) {
                $data[] = $key.'='.$value;
            }
            $data = implode('&', $data);
            $args['url'] .= trim((strpos($args['url'] ,'?')==false?'?':'&').$data, '&');
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
        $args['auth'] && curl_setopt($ch, CURLOPT_USERPWD, HTTP_BASIC_AUTH_USER .':'. HTTP_BASIC_AUTH_PASSWD);
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        
        log_message('debug', 'request remote server'. "\n" .'url: ['. $args['url'] .']'."\n".'param: ['. json_encode($args['data'], JSON_UNESCAPED_UNICODE) .']');
        $result = curl_exec($ch);
        
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
        }else{
            $code = 200;
            $data = $result;
        }

        if($args['type']==='json'){
            if($result === false){
                $return = array();
                $return['err'] = 1;
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
                $return['err'] = 2;
                $return['message'] = '返回数据非json格式';
                return $return;
            }
            
            if(isset($return['error'])){
                $return = array();
                $return['err'] = 9999;
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
                $return['err'] = 2;
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
            $return['err'] = $code==200 ? 0 : $code;
            $return['source_code'] = $data;
        }else{
            $return = array();
            $return['err'] = 0;
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
                cookie($k, null, -86400, SERVER_NAME, '/', 'pre_demo_');
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
 
 if(!function_exists('is_mobile')){
    /*移动端判断*/
    function is_mobile(){ 
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        } 
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])){ 
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        } 
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])){
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
                ); 
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            } 
        } 
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])){ 
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
                return true;
            } 
        } 
        return false;
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

if ( ! function_exists('getStructure'))
{
    function getStructure($node, $staff){
        foreach($staff as $sub_k=>$sub_v){
            if($node['s_id']==$sub_v['s_sid']){
                $node['item'][$sub_v['s_id']] = getStructure($sub_v, $staff);
            }
        }
        
        return $node;
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

if ( ! function_exists('isEmail')){
    /**
     * 是否是邮箱
     * @param string $email
     * @return bool
     */
    function isEmail($email){
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
    function lExit($body){
        if($tmp = json_decode($body, true)){
            $tmp['env'] = COOKIE_MIDDEL_FIX;
            $body = json_encode($tmp, JSON_UNESCAPED_UNICODE);
        }else{
            header('content-type:text/html;charset=utf-8', true);
        }
        log_message('all', 'request_id:'.Yaf_Registry::get('request_id')."\n    ".'response:'.$body."\n");
        exit($body);
    }
}

//方便调试
if(!function_exists('dd')){
    function dd($param){
        var_dump($param);
        exit;
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