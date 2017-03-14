<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Database{
    private function __constrct(){}
    private function __clone(){}
    
    public static function getInstance($default_group='db'){
        
        $instance = NULL;
        
        if($instance = Yaf_Registry::get($default_group)){
            return $instance;
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
            $instance = new $driver($config);

            if(!$instance){
                return false;
            }
        }else if($dbdriver==='pdo'){
            $subdriver = empty($config['subdriver']) ? 'mysql' : strtolower($config['subdriver']);
            $driver = 'Database_Drivers_Pdo_'. ucfirst($subdriver);
            // Check for a subdriver
            if (class_exists($driver) ){
                $instance = new $driver($config);
            }else{
                log_message('error', 'database subdriver was not surported, need mysql');
                return false;
            }
        }else{
            log_message('error', 'database driver was not surported, need mysqli or pdo');
            return false;
        }
        

        Yaf_Registry::set($default_group, $instance);
        return $instance;
    }
}