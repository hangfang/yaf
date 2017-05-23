<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Session {
    private static $_instance = null;
    private function __construct(){}
    private function __clone(){}

    public static function get(){
        $config = Yaf_Registry::get('config');
        $sessionid = cookie($config['application']['sess_cookie_name']);
        if(empty($sessionid)){
            return array();
        }
        
        if($session = Yaf_Registry::get('session')){
            return $session;
        }
        
        $cache = Cache::getInstance();
        $session = $cache->hGetAll($sessionid);
        
        Yaf_Registry::set('session', $session);
        return $session;
    }
    
    public static function set($name, $value=null){
        $config = Yaf_Registry::get('config');
        if(!$sessionid = cookie($config['application']['sess_cookie_name'])){
            $sessionid = md5(microtime(true) .'_'. ip_address() . uniqid());
            cookie($config['application']['sess_cookie_name'], $sessionid);
        }
        
        $cache = Cache::getInstance();
        if(is_null($value)){//设置整个hash值
            try{
                $cache->hMSet($sessionid, $name);
                Yaf_Registry::set('session', $name);
                return true;
            }catch(Exception $e){
                exit(json_encode(array('rtn'=>$e->getCode()+10000, 'error_msg'=>$e->getMessage())));
            }
        }
        
        try{
            $session = $cache->hGetAll($sessionid);
            if(empty($session)){
                $cache->hMSet($sessionid, array());
            }
            
            //设置hash单个field的value
            $cache->hSet($sessionid, $name, $value);
            $cache->expireAt($sessionid);
            $session = Yaf_Registry::get('session');
            $session[$name] = $value;
            Yaf_Registry::set('session', $session);
            return true;
        }catch(Exception $e){
            exit(json_encode(array('rtn'=>$e->getCode()+10000, 'error_msg'=>$e->getMessage())));
        }
    }
    
    public static function del(){
        $config = Yaf_Registry::get('config');
        $sessionid = cookie($config['application']['sess_cookie_name']);
        if(empty($sessionid)){
            return true;
        }
        
        $cache = Cache::getInstance();
        Yaf_Registry::set('session', array());
        return $cache->hMSet($sessionid, array());
    }
}
