<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @demo $cache = Cache::getInstance();
 * @author fangh@me.com
 * @date 2016-04-27
 */
class Cache {
    /**
     * 缓存对象实例
     * @var Cache_Drivers_Redis
     */
    private static $_instance = null;
    private function __construct(){}
    private function __clone(){}
    
    public static function getInstance(){
        if(self::$_instance && self::$_instance->ping()){
            return self::$_instance;
        }
        
        $config = new Yaf_Config_Ini(APPLICATION_PATH . '/conf/cache.ini', ini_get('yaf.environ'));
        $config = $config->toArray();
        $adapter = isset($config['adapter']) ? $config['adapter'] : 'redis';

        if ( ! extension_loaded($adapter)){
            log_message('error', 'Cache adapter "'. $adapter .'" not supported.');
            return false;
        }

        $class = 'Cache_Drivers_'. ucfirst(strtolower($adapter));
        if(!class_exists($class)){
            log_message('error', $class .' not exists.');
            return false;
        }
        
        self::$_instance = new $class;
        return self::$_instance;
    }
}
