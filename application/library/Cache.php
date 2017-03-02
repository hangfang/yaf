<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @demo $cache = new Cache('redis');
 * @author hangfang
 * @date 2016-04-27
 */
class Cache {
    public function __construct(){
        $cache = Yaf_Registry::get('cache');
        $adapter = isset($cache['adapter']) ? $cache['adapter'] : 'redis';

        if ( ! extension_loaded($adapter)){
            log_message('error', 'Cache adapter "'. $adapter .'" not supported.');
            return false;
        }

        $class = 'Cache_Drivers_'. ucfirst(strtolower($adapter));
        if(!class_exists($class)){
            log_message('error', $class .' not exists.');
            return false;
        }
        
        Yaf_Registry::set('cache', new $class);
    }
}
