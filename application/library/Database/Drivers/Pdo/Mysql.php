<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * 模拟CI数据库类的Pdo_Mysql封装
 * @author fangh@me.com
 */
class Database_Drivers_Pdo_Mysql extends Database_Drivers_Pdo{

    public $_config = array();
    public $_default_group = '';
    private $_options = array();
    public final function __construct($config, $default_group){
        $this->_default_group = $default_group;
        $this->_config = $config;
		if (empty($config['dsn'])){
			$config['dsn'] = 'mysql:host='.(empty($config['hostname']) ? '127.0.0.1' : $config['hostname']);

			empty($config['port']) OR $config['dsn'] .= ';port='.$config['port'];
			empty($config['database']) OR $config['dsn'] .= ';dbname='.$config['database'];
			empty($config['char_set']) OR $config['dsn'] .= ';charset='.$config['char_set'];
		}elseif (!empty($config['char_set']) && strpos($config['dsn'], 'charset=', 6) === FALSE && is_php('5.3.6')){
			$config['dsn'] .= ';charset='.$config['char_set'];
		}
        
        /* Prior to PHP 5.3.6, even if the charset was supplied in the DSN
		 * on connect - it was ignored. This is a work-around for the issue.
		 *
		 * Reference: http://www.php.net/manual/en/ref.pdo-mysql.connection.php
		 */
		if ( ! is_php('5.3.6') && ! empty($config['char_set'])){
			$this->_options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES '.$config['char_set']
				.(empty($config['dbcollat']) ? '' : ' COLLATE '.$config['dbcollat']);
		}

		if (isset($config['stricton'])){
			if ($config['stricton']){
				$sql = 'CONCAT(@@sql_mode, ",", "STRICT_ALL_TABLES")';
			}else{
				$sql = 'REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                                        @@sql_mode,
                                        "STRICT_ALL_TABLES,", ""),
                                        ",STRICT_ALL_TABLES", ""),
                                        "STRICT_ALL_TABLES", ""),
                                        "STRICT_TRANS_TABLES,", ""),
                                        ",STRICT_TRANS_TABLES", ""),
                                        "STRICT_TRANS_TABLES", "")';
			}

			if ( ! empty($sql)){
				if (empty($this->_options[PDO::MYSQL_ATTR_INIT_COMMAND])){
					$this->_options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET SESSION sql_mode = '.$sql;
				}else{
					$this->_options[PDO::MYSQL_ATTR_INIT_COMMAND] .= ', @@session.sql_mode = '.$sql;
				}
			}
		}

		if ($config['compress'] == TRUE){
			$this->_options[PDO::MYSQL_ATTR_COMPRESS] = TRUE;
		}

		// SSL support was added to PDO_MYSQL in PHP 5.3.7
		if (is_array($config['encrypt']) && is_php('5.3.7'))
		{
			$ssl = array();
			empty($config['encrypt']['ssl_key'])    OR $ssl[PDO::MYSQL_ATTR_SSL_KEY]    = $config['encrypt']['ssl_key'];
			empty($config['encrypt']['ssl_cert'])   OR $ssl[PDO::MYSQL_ATTR_SSL_CERT]   = $config['encrypt']['ssl_cert'];
			empty($config['encrypt']['ssl_ca'])     OR $ssl[PDO::MYSQL_ATTR_SSL_CA]     = $config['encrypt']['ssl_ca'];
			empty($config['encrypt']['ssl_capath']) OR $ssl[PDO::MYSQL_ATTR_SSL_CAPATH] = $config['encrypt']['ssl_capath'];
			empty($config['encrypt']['ssl_cipher']) OR $ssl[PDO::MYSQL_ATTR_SSL_CIPHER] = $config['encrypt']['ssl_cipher'];

			// DO NOT use array_merge() here!
			// It re-indexes numeric keys and the PDO_MYSQL_ATTR_SSL_* constants are integers.
			empty($ssl) OR $this->_options += $ssl;
		}
		// Prior to version 5.7.3, MySQL silently downgrades to an unencrypted connection if SSL setup fails
        $this->_options[PDO::ATTR_PERSISTENT] = $config['pconnect'];
		$this->_options[PDO::ATTR_STRINGIFY_FETCHES] = true;   //ATTR_STRINGIFY_FETCHES 提取的时候将数值转换为字符串
        $this->_options[PDO::ATTR_EMULATE_PREPARES] = false;

        $_conn = new PDO($config['dsn'], $config['username'], $config['password'], $this->_options);
        Yaf_Registry::set($this->_default_group, $_conn);

		if (! empty($ssl)
			&& version_compare($_conn->getAttribute(PDO::ATTR_CLIENT_VERSION), '5.7.3', '<=')
			&& empty($_conn->query("SHOW STATUS LIKE 'ssl_cipher'")->fetchObject()->Value)
		){
			log_message('error', $message = 'PDO_MYSQL was configured for an SSL connection, but got an unencrypted connection instead!');
            throw new Exception($message, '-1');
			return false;
		}
        
        $this->_prefix = empty($config['prefix']) ? '' : $config['prefix'];
    }

    public function ping(){
        $conn = Yaf_Registry::get($this->_default_group);
        if(!$conn->query('SELECT 1')){
            log_message('error', 'pdo mysql lose connection with mysql server...');
            $conn->setAttribute(PDO::ATTR_PERSISTENT, false);
            $conn = null;
            Yaf_Registry::del($this->_default_group);
            new self($this->_config, $this->_default_group);
            log_message('error', 'pdo mysql auto connected!');
        }
    }
}
