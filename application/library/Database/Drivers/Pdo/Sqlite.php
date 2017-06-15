<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * 模拟CI数据库类的Pdo_Sqlite封装
 * @author fangh@me.com
 */
class Database_Drivers_Pdo_Sqlite extends Database_Drivers_Pdo{
    public final function __construct($config){
		if (empty($config['dsn'])){
			$config['dsn'] = 'sqlite:'.(empty($config['hostname']) ? BASE_PATH .'/cache/sqlite.db' : $config['hostname']);
		}

        $this->_options[PDO::ATTR_PERSISTENT] = $config['pconnect'];
		$this->_options[PDO::ATTR_STRINGIFY_FETCHES] = false;
        $this->_options[PDO::ATTR_EMULATE_PREPARES] = false;

		try{
			$this->_conn = new PDO($config['dsn'], null, null, $this->_options);
		}catch (PDOException $e){
			log_message('error', $message = 'connect sqlite failed, msg:'. $e->getMessage());
            throw new Exception($message, $e->getCode());
            return FALSE;
		}
        
        $this->_prefix = empty($config['prefix']) ? '' : $config['prefix'];
    }
}
