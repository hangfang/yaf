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
    private static $_instance = [];
    private function __construct(){}
    private function __clone(){}
    
    /**
     * 获取cache实例
     * @param string $prefix key的前缀,默认/conf/cache.ini配置prefix字段
     * @return boolean
     */
    public static function getInstance($prefix=''){
        $config = new Yaf_Config_Ini(APPLICATION_PATH . '/conf/cache.ini', ini_get('yaf.environ'));
        $config = $config->toArray();
        
        if(empty($prefix)){
            $prefix = eval($config['prefix']);
        }
        
        $index = 'cache_'.$prefix;
        if(isset(self::$_instance[$index]) && self::$_instance[$index]->ping()){
            return self::$_instance[$index];
        }
        
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
        
        self::$_instance[$index] = new $class($prefix);
        return self::$_instance[$index];
    }
}
