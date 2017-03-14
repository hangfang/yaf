<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Database{
    public function __constrct(){
        
    }
    
    public static function getInstance($default_group='db'){
        
        $db = NULL;
        
        if($db = Yaf_Registry::get('db')){
            return $db;
        }

        if(! $ocnfig = Yaf_Registry::get('db_config')){
            $config = new Yaf_Config_Ini(APPLICATION_PATH . '/conf/database.ini', ini_get('yaf.environ'));
            $config = $config->toArray();
            $config = $config['database'][$default_group][rand(0,count($config)-1)];
            Yaf_Registry::set('db_config', $config);
        }
        
        $dbdriver = strtolower($config['dbdriver']);
        if($dbdriver==='mysqli'){
            $driverName = ucfirst($config['dbdriver']);
            $driver = 'Database_Drivers_'.$driverName;
            $db = new $driver($config);

            if(!$db){
                return false;
            }
        }else if($dbdriver==='pdo'){
            $subdriver = empty($config['subdriver']) ? 'mysql' : strtolower($config['subdriver']);
            $driver = 'Database_Drivers_Pdo_'. ucfirst($subdriver);
            // Check for a subdriver
            if (class_exists($driver) ){
                $db = new $driver($config);
            }else{
                log_message('error', 'database subdriver was not surported, need mysql');
                return false;
            }
        }else{
            log_message('error', 'database driver was not surported, need mysqli or pdo');
            return false;
        }
        

        Yaf_Registry::set('db', $db);
        return $db;
    }
}