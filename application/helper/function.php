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

// ------------------------------------------------------------------------

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
    function get_var_from_conf($filename){
        $filename = basename($filename, '.php');
        $var = Yaf_Registry::get($filename);
		if(!$var){
            Yaf_Loader::import(APPLICATION_PATH .'/conf/'.$filename.'.php');
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
            'header' => array('Connection: keep-alive', 'Expect: ')
        ) ,$args);
        if(strtolower($args['method']) === 'post'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args['data']);
        }else if(strtolower($args['method']) === 'input'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($args['data']) ? json_encode($args['data']) : $args['data']);
        }else{
            $data = array();
            foreach ($args['data'] as $key => $value) {
                $data[] = urlencode($key).'='.urlencode($value);
            }
            $data = implode('&', $data);
            $args['url'].=(strpos($args['url'] ,'?')==false?'?':'&').$data;
        }
        curl_setopt($ch, CURLOPT_URL, $args['url']);
        //跟踪301
        curl_setopt($ch ,CURLOPT_FOLLOWLOCATION ,1);
        //设置 referer
        if(isset($_SERVER['HTTP_REFERER'])){
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
        }
        // 头部信息
        curl_setopt($ch ,CURLOPT_HEADER, true);
        $args['auth'] && curl_setopt($ch, CURLOPT_USERPWD, HTTP_BASIC_AUTH_USER .':'. HTTP_BASIC_AUTH_PASSWD);
        // 返回字符串，而非直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 30秒超时
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        // cookie
        $args['cookie'] = array_merge($request->getCookie(), $args['cookie']);
        if($args['cookie']){
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
        isset($_SERVER['HTTP_USER_AGENT']) && curl_setopt($ch ,CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在 
        $result = curl_exec($ch);

        if($result === false){
            log_message('error', "requrest remote server failed, args: ". print_r($args, true) ."\ninfo: ". print_r(curl_getinfo($ch), true));
            $return = array();
            $return['rtn'] = -1;
            $return['error_msg'] = 'requrest remote server failed';
            return $return;
        }

        curl_close($ch);

        $header_body = preg_split("#\r\n\r\n#" ,$result ,2);//$header_body[0]是返回头的字符串形式，$header_body[1]是返回数据的字符串形式
        $headers = preg_split("#\r\n#", $header_body[0] ,2);//$headers是返回头的数组
        $status = preg_split("# #" ,$headers[0] ,3);//返回头第一行，用空格分隔，形成数组：协议、状态码、状态码对应的英文字符
        $code = $status[1];//状态码，如200、302、404
        $text = $status[2];
        $data = $header_body[1];//返回数据

        if(200 != $code){
            log_message('error', "remote server returns error, args: ". print_r($args, true) ."\nresult: ". print_r($result, true));
            $return = array();
            $return['rtn'] = $code;
            $return['error_msg'] = $text;
            return $return;
        }

        $return = json_decode($data);

        if(!$return){
            log_message('error', "remote server returns a not json formated data, args: ". print_r($args, true) ."\nresult: ". print_r($result, true));
            $return = array();
            $return['rtn'] = -2;
            $return['error_msg'] = '返回数据非json格式';
            return $return;
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
	function create_captcha($data = '', $img_path = '', $img_url = '', $font_path = '')
	{
		$defaults = array(
			'word'		=> '',
			'img_path'	=> '',
			'img_url'	=> '',
			'img_width'	=> 100,
			'img_height'	=> 46,
			'font_path'	=> '',
			'expiration'	=> 7200,
			'word_length'	=> 4,
			'font_size'	=> 16,
			'img_id'	=> '',
			'pool'		=> '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'colors'	=> array(
				'background'	=> array(255,255,255),
				'border'	=> array(153,102,102),
				'text'		=> array(204,153,153),
				'grid'		=> array(255,182,182)
			)
		);

		foreach ($defaults as $key => $val)
		{
			if ( ! is_array($data) && empty($$key))
			{
				$$key = $val;
			}
			else
			{
				$$key = isset($data[$key]) ? $data[$key] : $val;
			}
		}

		if ($img_path === '' OR $img_url === ''
			OR ! is_dir($img_path) OR ! is_really_writable($img_path)
			OR ! extension_loaded('gd'))
		{
			return FALSE;
		}

		// -----------------------------------
		// Remove old images
		// -----------------------------------

		$now = microtime(TRUE);

		$current_dir = @opendir($img_path);
		while ($filename = @readdir($current_dir))
		{
			if (substr($filename, -4) === '.jpg' && (str_replace('.jpg', '', $filename) + $expiration) < $now)
			{
				@unlink($img_path.$filename);
			}
		}

		@closedir($current_dir);

		// -----------------------------------
		// Do we have a "word" yet?
		// -----------------------------------

		if (empty($word))
		{
			$word = '';
			$pool_length = strlen($pool);
			$rand_max = $pool_length - 1;

			// PHP7 or a suitable polyfill
			if (function_exists('random_int'))
			{
				try
				{
					for ($i = 0; $i < $word_length; $i++)
					{
						$word .= $pool[random_int(0, $rand_max)];
					}
				}
				catch (Exception $e)
				{
					// This means fallback to the next possible
					// alternative to random_int()
					$word = '';
				}
			}
		}

		if (empty($word))
		{
			// Nobody will have a larger character pool than
			// 256 characters, but let's handle it just in case ...
			//
			// No, I do not care that the fallback to mt_rand() can
			// handle it; if you trigger this, you're very obviously
			// trying to break it. -- Narf
			if ($pool_length > 256)
			{
				return FALSE;
			}

			// We'll try using the operating system's PRNG first,
			// which we can access through CI_Security::get_random_bytes()
			$security = get_instance()->security;

			// To avoid numerous get_random_bytes() calls, we'll
			// just try fetching as much bytes as we need at once.
			if (($bytes = $security->get_random_bytes($pool_length)) !== FALSE)
			{
				$byte_index = $word_index = 0;
				while ($word_index < $word_length)
				{
					// Do we have more random data to use?
					// It could be exhausted by previous iterations
					// ignoring bytes higher than $rand_max.
					if ($byte_index === $pool_length)
					{
						// No failures should be possible if the
						// first get_random_bytes() call didn't
						// return FALSE, but still ...
						for ($i = 0; $i < 5; $i++)
						{
							if (($bytes = $security->get_random_bytes($pool_length)) === FALSE)
							{
								continue;
							}

							$byte_index = 0;
							break;
						}

						if ($bytes === FALSE)
						{
							// Sadly, this means fallback to mt_rand()
							$word = '';
							break;
						}
					}

					list(, $rand_index) = unpack('C', $bytes[$byte_index++]);
					if ($rand_index > $rand_max)
					{
						continue;
					}

					$word .= $pool[$rand_index];
					$word_index++;
				}
			}
		}

		if (empty($word))
		{
			for ($i = 0; $i < $word_length; $i++)
			{
				$word .= $pool[mt_rand(0, $rand_max)];
			}
		}
		elseif ( ! is_string($word))
		{
			$word = (string) $word;
		}

		// -----------------------------------
		// Determine angle and position
		// -----------------------------------
		$length	= strlen($word);
		$angle	= ($length >= 6) ? mt_rand(-($length-6), ($length-6)) : 0;
		//$x_axis	= mt_rand(6, (360/$length)-16);
        $x_axis	= mt_rand(6, 8);
		$y_axis = ($angle >= 0) ? mt_rand($img_height, $img_width) : mt_rand(6, $img_height);

		// Create image
		// PHP.net recommends imagecreatetruecolor(), but it isn't always available
		$im = function_exists('imagecreatetruecolor')
			? imagecreatetruecolor($img_width, $img_height)
			: imagecreate($img_width, $img_height);

		// -----------------------------------
		//  Assign colors
		// ----------------------------------

		is_array($colors) OR $colors = $defaults['colors'];

		foreach (array_keys($defaults['colors']) as $key)
		{
			// Check for a possible missing value
			is_array($colors[$key]) OR $colors[$key] = $defaults['colors'][$key];
			$colors[$key] = imagecolorallocate($im, $colors[$key][0], $colors[$key][1], $colors[$key][2]);
		}

		// Create the rectangle
		ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $colors['background']);

		// -----------------------------------
		//  Create the spiral pattern
		// -----------------------------------
		$theta		= 1;
		$thetac		= 7;
		$radius		= 16;
		$circles	= 20;
		$points		= 32;

		for ($i = 0, $cp = ($circles * $points) - 1; $i < $cp; $i++)
		{
			$theta += $thetac;
			$rad = $radius * ($i / $points);
			$x = ($rad * cos($theta)) + $x_axis;
			$y = ($rad * sin($theta)) + $y_axis;
			$theta += $thetac;
			$rad1 = $radius * (($i + 1) / $points);
			$x1 = ($rad1 * cos($theta)) + $x_axis;
			$y1 = ($rad1 * sin($theta)) + $y_axis;
			imageline($im, $x, $y, $x1, $y1, $colors['grid']);
			$theta -= $thetac;
		}

		// -----------------------------------
		//  Write the text
		// -----------------------------------

		$use_font = ($font_path !== '' && file_exists($font_path) && function_exists('imagettftext'));
		if ($use_font === FALSE)
		{
			($font_size > 30) && $font_size = 30;
			$x = mt_rand(0, $img_width/$word_length-$font_size/10);
			$y = 0;
		}
		else
		{
			($font_size > 30) && $font_size = 30;
			$x = mt_rand(0, $img_width/$word_length-$font_size/10);
			$y = 0;
		}

		for ($i = 0; $i < $length; $i++)
		{
			if ($use_font === FALSE)
			{
				$y = mt_rand(12 , 16);
				imagestring($im, $font_size, $x, $y, $word[$i], $colors['text']);
				$x += min(array(round($img_width/$word_length-$font_size/12), 20));
			}
			else
			{
				$y = mt_rand(8 , 12);
				imagettftext($im, $font_size, $angle, $x, $y, $colors['text'], $font_path, $word[$i]);
				$x += min(array(round($img_width/$word_length-$font_size/12), 20));
			}
		}

		// Create the border
		imagerectangle($im, 0, 0, $img_width - 1, $img_height - 1, $colors['border']);

		// -----------------------------------
		//  Generate the image
		// -----------------------------------
		$img_url = rtrim($img_url, '/').'/';

		if (function_exists('imagejpeg'))
		{
			$img_filename = $now.'.jpg';
			imagejpeg($im, $img_path.$img_filename);
		}
		elseif (function_exists('imagepng'))
		{
			$img_filename = $now.'.png';
			imagepng($im, $img_path.$img_filename);
		}
		else
		{
			return FALSE;
		}

		//$img = '<img '.($img_id === '' ? '' : 'id="'.$img_id.'"').' src="'.$img_url.$img_filename.'" style="width: '.$img_width.'; height: '.$img_height .'; border: 0;" alt=" " />';
		ImageDestroy($im);
        
        $img = 'data:image/jpg;base64,'.base64_encode(file_get_contents(APPLICATION_PATH.'/captcha/'.$img_filename));
        @unlink(APPLICATION_PATH.'/captcha/'.$img_filename);
		return array('word' => $word, 'time' => $now, 'image' => $img, 'filename' => $img_filename);
		//return array('word' => $word, 'time' => $now, 'image' => $img_url.$img_filename, 'filename' => $img_filename);
	}
}

if ( ! function_exists('is_id_card')){
	/**
	 * @todo 检查身份证号是否有效
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
	 * @todo 根据身份证提取性别
	 * @param int $cid
	 * @return string
	 */
	function get_sex_by_id_card($cid) {
		if (!isIdCard($cid)){
			return 'unknown';
		}

		$sexint = (int)substr($cid,16,1);
		return $sexint % 2 === 0 ? 'woman' : 'man';
	}
}

if ( ! function_exists('get_birthday_by_id_card')){
    /**
     * @todo 根据身份证获取生日
     * @param $cid
     * @return string
     */
    function get_birthday_by_id_card($cid){
        if (!isIdCard($cid)){
			return 'unknown';
		}
        return substr($cid,6,4)."-".substr($cid,10,2).'-'.substr($cid,12,2);
    }
}

if ( ! function_exists('isEmail')){
    /**
     * @todo 是否是邮箱
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
	 * @todo 是否是数字
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
     * @todo 是否是手机
     * @param string $phone 手机号码
     * @return bool
     */
    function is_phone($phone){
        $exp = '/^1[3|4|5|6|7|8|9][0-9]{9}$/';
        return preg_match($exp,$phone);
    }
}
