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
        
        $driverName = ucfirst($config['dbdriver']);
        $driver = 'Database_Drivers_'.$driverName.'_'.$driverName.'Driver';
        $db = new $driver($config);

        // Check for a subdriver
        if ( ! empty($db->subdriver))
        {
            $driver = 'Database_Drivers_Pdo_Subdrivers_'.ucfirst($db->dbdriver).''.ucfirst($db->subdriver).'Driver';
            $db = new $driver($config);
        }

        $db->initialize();
        Yaf_Registry::set('db', $db);
        return $db;
    }
}
