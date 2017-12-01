<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * 对Redis的封装，添加默认时间支持
 * @author fangh@me.com
 */
class Cache_Drivers_Redis extends Redis{
    /**
     * Default config
     * @access private
     * @var	array
     */
    private $_config = array(
            'socket_type' => 'tcp',
            'host' => '127.0.0.1',
            'password' => NULL,
            'port' => 6379,
            'timeout' => 0
    );

    /**
     * 初始化Redis实例：连接服务器，如有必要则进行授权
     * @return boolean
     */
    public function __construct($prefix){
        $config = new Yaf_Config_Ini(APPLICATION_PATH . '/conf/cache.ini', ini_get('yaf.environ'));
        $config = $config->toArray();
        if ($config){
            $this->_config = array_merge($this->_config, $config);
        }
        
        if(isset($this->_config['pconnect']) && $this->_config['pconnect']){
            $conn = $this->pconnect($this->_config['host'], $this->_config['port']);
        }else{
            $conn = $this->connect($this->_config['host'], $this->_config['port']);
        }
        
        if(!$conn){
            log_message('error', 'connect redis server failed: '. print_r($this->_config, true));
            return false;
        }
        if(isset($this->_config['password']) && $this->_config['password']){
            $auth = $this->auth($this->_config['password']);
            if(!$auth){
                log_message('error', 'redis auth failed: '. print_r($this->_config, true));
                return false;
            }
        }
        
        $prefix && $this->_config['prefix'] = $prefix;
        $this->setOption(Redis::OPT_PREFIX, $this->_config['prefix']);
    }
    
    /**
     * 设置key的生存时间
     * @param string $key 键名
     * @param int $expire 可选.生存时间，默认取配置文件的ttl字段值
     * @return boolean
     */
    public function setTimeout($key, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'];
        }
        return parent::setTimeout($key, $expire);
    }
    
    /**
     * 设置key的生存时间
     * @param string $key 键名
     * @param int $expire 可选.生存时间，默认取配置文件的ttl字段值
     * @return boolean
     */
    public function expire($key, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'];
        }
        return parent::expire($key, $expire);
    }
    
    /**
     * 设置key的到期时间
     * @param string $key 键名
     * @param int $expire 可选.到期时间，默认取配置文件的ttl字段值
     * @return boolean
     */
    public function expireAt($key, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'] + time();
        }
        return parent::expireAt($key, $expire);
    }
    
    /**
     * 设置key的值和生存时间
     * @param string $key 键名
     * @param string $value 值
     * @param int $expire 可选.生存时间，默认取配置文件的ttl字段值
     * @return boolean
     */
    public function setex($key, $value, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'];
        }
        return parent::setex($key, $expire, $value);
    }
    
    /**
     * 设置key的值
     * @param string $key 键名
     * @param string|array $value 值
     * @return boolean
     */
    public function set($key, $value, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'];
        }
        $value = is_string($value) ? $value : json_encode($value);
        return parent::setex($key, $expire, $value);
    }
    
    /**
     * 设置HASH表
     * @param string $key 键名
     * @param string $value 值
     * @param int $expire 可选.生存时间，默认取配置文件的ttl字段值
     * @return boolean
     */
    public function hMset($key, $value, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'];
        }
        parent::hMset($key, $value);
        return $this->expire($key, $expire);
    }
    
    /**
     * 发布消息
     * @param string $channel 消息主题
     * @param string $msg 消息内容
     * @return boolean
     */
    public function publish($channel, $msg){
        return parent::publish($channel, $msg);
    }
}
