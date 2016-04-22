<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Database{
    public function __constrct(){
        
    }
    
    public static function getInstance($default_group='default'){
        
        $db = NULL;
        
        if($db = Yaf\Registry::get('db')){
            return $db;
        }

        if(! $ocnfig = Yaf\Registry::get('db_config')){
            $config = new Yaf\Config\Ini(APPLICATION_PATH . '/conf/database.ini', ini_get('yaf.environ'));
            Yaf\Registry::set('db_config', $config);
        }
        
        $config = $config->toArray();
        $driverName = ucfirst($config['database'][$default_group]['dbdriver']);
        $driver = 'Database\\Drivers\\'.$driverName.'\\'.$driverName.'Driver';
        $db = new $driver($config['database'][$default_group]);

        // Check for a subdriver
        if ( ! empty($db->subdriver))
        {
            $driver = 'Database\\Drivers\\Pdo\\Subdrivers\\'.ucfirst($db->dbdriver).''.ucfirst($db->subdriver).'Driver';
            $db = new $driver($config['database'][$default_group]);
        }

        $db->initialize();
        Yaf\Registry::set('db', $db);
        return $db;
    }
}