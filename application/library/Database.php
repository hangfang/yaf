<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Database{
    /**
     * @var Database_Drivers_Pdo_Mysql 
     */
    public static $_instance;
    private function __construct(){}
    private function __clone(){}
    
    /**
     * @todo 获取Mysql驱动类的实例
     * @param string $default_group 数据库组名
     * @return Mysql驱动类的实例
     */
    public static function getInstance($default_group='db'){
        
        if(self::$_instance = Yaf_Registry::get($default_group)){
            return self::$_instance;
        }

        if(! $config = Yaf_Registry::get('db_config')){
            $config = new Yaf_Config_Ini(APPLICATION_PATH . '/conf/database.ini', ini_get('yaf.environ'));
            $config = $config->toArray();
            $config = $config['database'][$default_group][rand(0,count($config)-1)];
            Yaf_Registry::set('db_config', $config);
        }
        
        $dbdriver = strtolower($config['dbdriver']);
        if($dbdriver==='mysqli'){
            $driverName = ucfirst($config['dbdriver']);
            $driver = 'Database_Drivers_'.$driverName;
            self::$_instance = new $driver($config);

            if(!self::$_instance){
                return false;
            }
        }else if($dbdriver==='pdo'){
            $subdriver = empty($config['subdriver']) ? 'mysql' : strtolower($config['subdriver']);
            $driver = 'Database_Drivers_Pdo_'. ucfirst($subdriver);
            // Check for a subdriver
            if (class_exists($driver) ){
                self::$_instance = new $driver($config);
            }else{
                log_message('error', 'database subdriver was not surported, need mysql');
                return false;
            }
        }else{
            log_message('error', 'database driver was not surported, need mysqli or pdo');
            return false;
        }
        

        Yaf_Registry::set($default_group, self::$_instance);
        return self::$_instance;
    }
}