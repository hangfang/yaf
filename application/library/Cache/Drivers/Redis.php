<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Cache_Drivers_Redis extends Redis{
    /**
     * Default config
     *
     * @static
     * @var	array
     */
    private $_config = array(
        'socket_type' => 'tcp',
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => 6379,
        'timeout' => 0
    );

    public function __construct(){
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
            if($this->auth($this->_config['password'])){
                log_message('error', 'redis auth failed: '. print_r($this->_config, true));
                return false;
            }
        }
        
        $this->setOption(Redis::OPT_PREFIX, $this->_config['prefix']);
    }
    
    public function setTimeout($key, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'];
        }
        return parent::setTimeout($key, $expire);
    }
    
    public function expire($key, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'];
        }
        return parent::expire($key, $expire);
    }
    
    public function expireAt($key, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'] + time();
        }
        return parent::expireAt($key, $expire);
    }
    
    public function setex($key, $value, $expire=null){
        if(is_null($expire)){
            $expire = $this->_config['ttl'];
        }
        return parent::setex($key, $expire, $value);
    }
}
