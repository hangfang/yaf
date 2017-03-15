<?php
/**
 * 模拟CI数据库类的Pdo_Mysql封装
 * @author fangh@me.com
 */
class Database_Drivers_Pdo_Mysql{
    
    private $_conn=null;
    private $_stmt=null;
    private $_options = array();
    private $_condition = array();
    private $_having = '';
    private $_set = array();
    private $_limit = array();
    private $_group = '';
    private $_order = '';
    private $_table = '';
    private $_select = '';
    private $_query = null;
    private $_sql = '';
    private $_value = array();
    private $_prefix = '';
    
    public function __construct($config){
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

		if ($config['compress'] === TRUE){
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
		$this->_options[PDO::ATTR_STRINGIFY_FETCHES] = false;
        $this->_options[PDO::ATTR_EMULATE_PREPARES] = false;

		try{
			$this->_conn = new PDO($config['dsn'], $config['username'], $config['password'], $this->_options);
		}catch (PDOException $e){
			log_message('error', $e->getMessage());
			return FALSE;
		}
        
		if (! empty($ssl)
			&& version_compare($this->_conn->getAttribute(PDO::ATTR_CLIENT_VERSION), '5.7.3', '<=')
			&& empty($this->_conn->query("SHOW STATUS LIKE 'ssl_cipher'")->fetchObject()->Value)
		){
			$message = 'PDO_MYSQL was configured for an SSL connection, but got an unencrypted connection instead!';
			log_message('error', $message);
			return false;
		}
        
        $this->_prefix = empty($config['prefix']) ? '' : $config['prefix'];
    }
    
    /**
     * SQL语句条件分组开始:AND (
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function groupStart(){
        $this->_condition[] = array('key'=>'(', 'value'=>'', 'connect'=>'AND');
        return $this;
    }
    
    /**
     * SQL语句条件分组开始:OR (
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function orGroupStart(){
        $this->_condition[] = array('key'=>'(', 'value'=>'', 'connect'=>'OR');
        return $this;
    }
        
    /**
     * SQL语句条件分组结束:)
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function groupEnd(){
        $this->_condition[] = array('key'=>')', 'value'=>'');
        return $this;
    }
    
    /**
     * SQL语句条件:AND column_name = 'xx'
     * @param mixed $where  查询条件键值对:array('id'=>1, 'name'=>'tom')
     * @param mixed $value  条件字段对应的值，$value不是null时，$where为字段名
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function where($where, $value=null){
        if(is_null($value)){
            if(!empty($where)){
                foreach($where as $k=>$v){
                    if(is_array($v)){
                        foreach($v as $_field=>$_value){
                            $op = preg_replace('/[0-9a-z_]/', '', $_field);
                            $op = empty($op) ? '=' : $op;
                            $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $_field), 'value'=>$_value, 'connect'=>'AND', 'op'=>$op);
                        }
                    }else{
                        $op = preg_replace('/[0-9a-z_]/', '', $k);
                        $op = empty($op) ? '=' : $op;
                        $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $k), 'value'=>$v, 'connect'=>'AND', 'op'=>$op);
                    }
                }
            }
        }else{
            if(!empty($where)){
                $op = preg_replace('/[0-9a-z_]/', '', $where);
                $op = empty($op) ? '=' : $op;
                $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $where), 'value'=>$value, 'connect'=>'AND', 'op'=>$op);
            }
        }
        
        return $this;
    }
    
    /**
     * SQL语句条件:OR column_name = 'xx'
     * @param mixed $where  查询条件键值对:array('id'=>1, 'name'=>'tom')
     * @param mixed $value  条件字段对应的值，$value不是null时，$where为字段名
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function orWhere($where, $value=null){
        if(is_null($value)){
            if(!empty($where)){
                if(count($where)>1){
                    $this->_condition[] = array('key'=>'(', 'value'=>'', 'connect'=>'OR');
                    foreach($where as $k=>$v){
                        if(is_array($v)){
                            foreach($v as $_field=>$_value){
                                $op = preg_replace('/[0-9a-z_]/', '', $_field);
                                $op = empty($op) ? '=' : $op;
                                $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $_field), 'value'=>$_value, 'connect'=>'AND', 'op'=>$op);
                            }
                        }else{
                            $op = preg_replace('/[0-9a-z_]/', '', $k);
                            $op = empty($op) ? '=' : $op;
                            $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $k), 'value'=>$v, 'connect'=>'AND', 'op'=>$op);
                        }
                    }
                    $this->_condition[] = array('key'=>')', 'value'=>'');
                }else{
                    foreach($where as $k=>$v){
                        if(is_array($v)){
                            foreach($v as $_field=>$_value){
                                $op = preg_replace('/[0-9a-z_]/', '', $_field);
                                $op = empty($op) ? '=' : $op;
                                $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $_field), 'value'=>$_value, 'connect'=>'OR', 'op'=>$op);
                            }
                        }else{
                            $op = preg_replace('/[0-9a-z_]/', '', $k);
                            $op = empty($op) ? '=' : $op;
                            $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $k), 'value'=>$v, 'connect'=>'AND', 'op'=>$op);
                        }
                    }
                }
            }
        }else{
            if(is_array($value)){
                $this->_condition[] = array('key'=>$where, 'value'=>$value, 'connect'=>'OR', 'op'=>'in');
            }else{
                $op = preg_replace('/[0-9a-z_]/', '', $where);
                $op = empty($op) ? '=' : $op;
                $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $where), 'value'=>$value, 'connect'=>'OR', 'op'=>$op);
            }
        }
        return $this;
    }
    
    /**
     * SQL语句条件:AND column_name IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function whereIn($field, $list){
        $list = is_array($list) ? implode(',', $list) : $list;
        $this->_condition[] = array('key'=>$field, 'value'=>$list, 'connect'=>'AND', 'op'=>'in');
        return $this;
    }
    
    /**
     * SQL语句条件:OR column_name IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function orWhereIn($field, $list){
        $list = is_array($list) ? implode(',', $list) : $list;
        $this->_condition[] = array('key'=>$field, 'value'=>$list, 'connect'=>'OR', 'op'=>'in');
        return $this;
    }
    
    /**
     * SQL语句条件:AND column_name NOT IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function whereNotIn($field, $list){
        $list = is_array($list) ? implode(',', $list) : $list;
        $this->_condition[] = array('key'=>$field, 'value'=>$list, 'connect'=>'AND', 'op'=>'not in');
        return $this;
    }
    
    /**
     * SQL语句条件:OR column_name NOT IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function orWhereNotIn($field, $list){
        $list = is_array($list) ? implode(',', $list) : $list;
        $this->_condition[] = array('key'=>$field, 'value'=>$list, 'connect'=>'OR', 'op'=>'not in');
        return $this;
    }
    
    /**
     * SQL语句条件:AND column_name LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function like($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'AND', 'op'=>'like', 'side'=>$side);
        return $this;
    }
    
    /**
     * SQL语句条件:OR column_name LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function orLike($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'OR', 'op'=>'like', 'side'=>$side);
        return $this;
    }
    
    /**
     * SQL语句条件:AND column_name NOT LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function notLike($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'AND', 'op'=>'not like', 'side'=>$side);
        return $this;
    }
    
    /**
     * SQL语句条件:OR column_name NOT LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function orNotLike($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'OR', 'op'=>'not like', 'side'=>$side);
        return $this;
    }
    
    /**
     * SQL语句:HAVING COUNT(column_name) >0
     * @param string $having  having 字句
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function having($having){
        $this->_having[] = $having;
        return $this;
    }
    
    /**
     * SQL语句:LIMIT $offset, $limit
     * @param int $limit  查询记录数
     * @param int $offset  查询偏移量
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function limit($limit=null, $offset=null){
        if(is_null($limit)){
            return $this;
        }
        $this->_limit['limit'] = $limit;

        if(is_null($offset)){
            return $this;
        }
        $this->_limit['offset'] = $offset;
        
        return $this;
    }
    
    /**
     * SQL语句:GROUP BY column_name
     * @param mixed $field  分组字段，支持:array('id', 'name')、'id,name'
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function groupBy($field){
        if(is_array($field)){
            $this->_group = implode(',', $field);
            return $this;
        }
        $this->_group = $field;
        
        return $this;
    }
    
    /**
     * SQL语句:ORDER BY column_name desc
     * @param mixed $order  排序字段，支持:array('id desc', 'name asc')、'id asc'
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function orderBy($order){
        if(empty($order)){
            return $this;
        }
        
        if(is_string($order)){
            $this->_order[] = $order;
            return $this;
        }
        
        foreach($order as $v){
            $this->_order[] = $v;
        }
        
        return $this;
    }
    
    /**
     * SQL语句:select column_name_a,column_name_b
     * @param mixed $field  排序字段，支持:array('id', 'name')、'id,name'
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function select($field){
        if(is_array($field)){
            $this->_select = trim(implode(',', $field), ',');
            return $this;
        }
        $this->_select = trim($field, ',');
        
        return $this;
    }
    
    /**
     * SQL语句:select column_name_a,column_name_b from table
     * @param mixed $table  查询表名
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function from($table){
        $this->_table = strpos($table, $this->_prefix)===false ? $this->_prefix.$table : $table;
        
        return $this;
    }
    
    /**
     * 设置更新字段
     * @param mixed $data  需要更新的键值对
     * @param mixed $value  当$value不为null时，$data是待更新的字段名
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function set($data, $value=null){
        if(is_null($value)){
            if(!empty($data)){
                foreach($data as $k=>$v){
                    if(is_array($v)){
                        foreach($v as $_field=>$_value){
                            $this->_set[] = array('key'=>$_field, 'value'=>$_value);
                        }
                    }else{
                        $this->_set[] = array('key'=>$k, 'value'=>$v);
                    }
                }
            }
        }else{
            if(!empty($data)){
                if(is_array($data)){
                    log_message('error', 'sql error, data type error');
                    return false;
                }
                $this->_set[] = array('key'=>$data, 'value'=>$value);
            }
        }
        
        return $this;
    }
    
    /**
     * 查询数据N条数据
     * @param string $table  查询表名
     * @param int $limit  查询记录数
     * @param int $offset  查询偏移量
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function get($table='', $limit=null, $offset=null){
        $this->freeResult();
        !empty($table) && $this->_table = strpos($table, $this->_prefix)===false ? $this->_prefix.$table : $table;
        if(!is_null($offset)){
            $this->_limit['offset'] = $offset;
        }

        if(!is_null($limit)){
            $this->_limit['limit'] = $limit;
        }
        
        if(empty($this->_select)){
            $this->_select = '*';
        }
        
        if(empty($this->_table)){
            log_message('error', 'sql error, select: need table name');
            return false;
        }
        
        $this->_sql = 'select '. $this->_select .' from '. $this->_table;
        
        $this->__buildWhere();
        $this->__buildHaving();
        $this->__buildGroup();
        $this->__buildOrder();
        $this->__buildLimit();

        $this->_stmt = $this->_conn->prepare($this->_sql);
        if(!$this->_stmt){
            log_message('error', 'sql prepare error, msg: '. json_encode($this->_conn->errorInfo()));
            return false;
        }
        
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
                
        return $this;
    }
    
    /**
     * 从结果集拿出一行数据
     * @return mixed boolean || array
     */
    public function rowArray(){
        $rt = $this->_stmt->fetch(PDO::FETCH_ASSOC);
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
                
        return $rt;
    }
    
    /**
     * 从结果集拿出所有数据
     * @return mixed boolean || array
     */
    public function resultArray(){
        $rt = $this->_stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
                
        return $rt;
    }
    
    /**
     * 从结果集拿出一行数据
     * @return mixed boolean ||　object
     */
    public function rowObject(){
        $rt = $this->_stmt->fetch(PDO::FETCH_OBJ);
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
                
        return $rt;
    }
    
    /**
     * 从结果集拿出所有数据
     * @return mixed boolean ||　object
     */
    public function resultObject(){
        $rt = $this->_stmt->fetchAll(PDO::FETCH_OBJ);
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
                
        return $rt;
    }
    
    /**
     * 带条件查询数据N条数据
     * @param string $table  查询表名
     * @param array $where  查询条件
     * @param int $limit  查询记录数
     * @param int $offset  查询偏移量
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function getWhere($table='', $where=array(), $limit=null, $offset=null){
        if(!empty($where)){
            foreach($where as $k=>$v){
                if(is_array($v)){
                    foreach($v as $_field=>$_value){
                        $op = preg_replace('/[0-9a-z_]/', '', $_field);
                        $op = empty($op) ? '=' : $op;
                        $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $_field), 'value'=>$_value, 'connect'=>'AND', 'op'=>$op);
                    }
                }else{
                    $op = preg_replace('/[0-9a-z_]/', '', $k);
                    $op = empty($op) ? '=' : $op;
                    $this->_condition[] = array('key'=>preg_replace('/[^0-9a-z_]/', '', $k), 'value'=>$v, 'connect'=>'AND', 'op'=>$op);
                }
            }
        }
        
        return $this->get($table, $limit, $offset);
    }
    
    /**
     * 更新数据
     * @param string $table  查询表名
     * @param mixed $update  需要更新的键值对
     * @param mixed $where  更新时的条件
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function update($table, $update=array(), $where=array()){
        $this->freeResult();
        !empty($table) && $this->_table = strpos($table, $this->_prefix)===false ? $this->_prefix.$table : $table;
        
        if(empty($this->_table)){
            log_message('error', 'sql error, UPDATE: need table name');
            return false;
        }
        
        $this->set($update);
        if(empty($this->_set)){
            log_message('error', 'sql error, UPDATE: need data to update');
            return false;
        }
        
        $this->where($where);
        if(empty($this->_condition)){
            log_message('error', 'sql error, UPDATE: need condition');
            return false;
        }
        
        $this->_sql = 'UPDATE '. $this->_table .' ';
        
        $this->__buildSet();
        $this->__buildWhere();
        
        $this->_stmt = $this->_conn->prepare($this->_sql);
        if(!$this->_stmt){
            log_message('error', 'sql prepare error, msg: '. json_encode($this->_conn->errorInfo()));
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
                
        return $this->_stmt->rowCount();
    }
    
    /**
     * 插入数据
     * @param string $table  查询表名
     * @param mixed $data  需要插入的键值对
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function insert($table, $data){
        $this->freeResult();
        
        if(empty($table)){
            log_message('error', 'sql error, INSERT: need table name');
            return false;
        }
        
        if(empty($data)){
            log_message('error', 'sql error, INSERT: need data');
            return false;
        }
        $this->_table = strpos($table, $this->_prefix)===false ? $this->_prefix.$table : $table;
        $this->_sql = 'INSERT INTO '. $this->_table .' (';
        foreach($data as $k=>$v){
            $this->_sql .= $k .',';
        }
        $this->_sql = trim($this->_sql, ',');
        
        $this->_sql .= ') VALUES (';
        foreach($data as $k=>$v){
            $key = $k.count($this->_value);
            $this->_sql .= ':'. $key .',';
            $this->_value[] = array($key=>$v);
        }
        $this->_sql = trim($this->_sql, ',');
        $this->_sql .= ')';
        
        $this->_stmt = $this->_conn->prepare($this->_sql);
        if(!$this->_stmt){
            log_message('error', 'sql prepare error, msg: '. json_encode($this->_conn->errorInfo()));
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
                
        return $this->_conn->lastInsertId();
    }
    
    /**
     * 删除数据
     * @param string $table  查询表名
     * @param mixed $where  删除条件
     * @param int $limit  删除记录数
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function delete($table='', $where=array(), $limit=0){
        $this->freeResult();
        !empty($table) && $this->_table = strpos($table, $this->_prefix)===false ? $this->_prefix.$table : $table;
        
        if(empty($this->_table)){
            log_message('error', 'sql error, UPDATE: need table name');
            return false;
        }
        
        $this->where($where);
        !empty($limit) && $this->_limit['limit'] = $limit;
        
        $this->_sql = 'DELETE FROM '. $this->_table;
        $this->__buildWhere();
        $this->__buildLimit();

        $this->_stmt = $this->_conn->prepare($this->_sql);
        if(!$this->_stmt){
            log_message('error', 'sql prepare error, msg: '. json_encode($this->_conn->errorInfo()));
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
                
        return $this->_stmt->rowCount();
    }
    
    /**
     * 替换已有的数据
     * @param string $table  查询表名
     * @param mixed $data  需要插入的键值对
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function replace($table, $data){
        $this->freeResult();
        if(empty($table)){
            log_message('error', 'sql error, REPLACE: need table name');
            return false;
        }
        
        if(empty($data)){
            log_message('error', 'sql error, REPLACE: need data');
            return false;
        }
        $this->_table = strpos($table, $this->_prefix)===false ? $this->_prefix.$table : $table;
        $this->_sql = 'REPLACE INTO '. $this->_table .' (';
        foreach($data as $k=>$v){
            $this->_sql .= $k .',';
        }
        $this->_sql = trim($this->_sql, ',');
        
        $this->_sql .= ') VALUES (';
        foreach($data as $k=>$v){
            $key = $k.count($this->_value);
            $this->_sql .= ':'. $key .',';
            $this->_value[] = array($key=>$v);
        }
        $this->_sql = trim($this->_sql, ',');
        $this->_sql .= ')';
        
        $this->_stmt = $this->_conn->prepare($this->_sql);
        if(!$this->_stmt){
            log_message('error', 'sql prepare error, msg: '. json_encode($this->_conn->errorInfo()));
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
                
        return $this->_conn->lastInsertId();
    }
    
    /**
     * 组装查询条件
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    private function __buildWhere(){
        
        if(!empty($this->_condition)){
            $this->_sql .= ' where ';
            $preKey = '';
            foreach($this->_condition as $k=>$v){
                $this->_sql .= ' ';
                $groupStart = $v['key']==='(';
                $groupEnd = $v['key']===')';
                if($k > 0 && !in_array($preKey, array('(', ''))){
                    $this->_sql .= $v['connect'] .' ';
                }
                
                $key = ':'. $v['key'].count($this->_value);
                if($v['op']==='like' || $v['op']==='not like'){
                    $this->_sql .= $v['key'] .' '. $v['op'] .' '. $key .' ';
                    $tmp = $v['value'];
                    if($v['side']==='both'){
                        $tmp = '%'.$v['value'].'%';
                    }else if($v['side']==='left'){
                        $tmp = '%'.$v['value'];
                    }else{
                        $tmp = $v['value'].'%';
                    }
                    $this->_value[] = array($key=>$tmp);
                }elseif($v['op']==='in' || $v['op']==='not in'){
                    $this->_sql .= $v['key'] .' '. $v['op'] .' ('. $key .') ';
                    $this->_value[] = array($key=>$v['value']);
                }else if($groupStart || $groupEnd){
                    $this->_sql .= $v['key'] .' ';
                }else{
                    $this->_sql .= $v['key'] .' = '. $key .' ';
                    $this->_value[] = array($key=>$v['value']);
                }
                
                
                $preKey = $v['key'];
            }
        }
        $this->_condition = array();
        
        return $this;
    }
    
    /**
     * 组装having子句
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    private function __buildHaving(){
        if(!empty($this->_having)){
            $this->_sql .= ' '. $this->_having .' ';
        }
        $this->_having = '';
        
        return $this;
    }
    
    /**
     * 组装group by子句
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    private function __buildGroup(){
        
        if(!empty($this->_group)){
            $this->_sql .= ' group by '. $this->_group .' ';
        }
        $this->_group = '';
        
        return $this;
    }
    
    /**
     * 组装order by子句
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    private function __buildOrder(){
        
        if(!empty($this->_order)){
            $this->_sql .= ' order by ';
            $this->_sql .= implode(',', $this->_order);
        }
        $this->_order = '';
        
        return $this;
    }
    
    /**
     * 组装limit子句
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    private function __buildLimit(){
        if(isset($this->_limit['limit'])){
            $this->_sql .= ' limit ';
            if(!isset($this->_limit['offset'])){
                $this->_sql .= $this->_limit['limit'];
            }else{
                $this->_sql .= $this->_limit['offset'] .','. $this->_limit['limit'];
            }
        }
        $this->_limit = '';
        
        return $this;
    }
    
    /**
     * 组装set子句
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    private function __buildSet(){
        
        if(!empty($this->_set)){
            $this->_sql .= ' set ';
            $preKey = '';
            foreach($this->_set as $k=>$v){
                if($k > 0){
                    $this->_sql .= ', ';
                }
                
                $key = ':'.$v['key'].count($this->_value);
                $this->_sql .= $v['key'] .' = '. $key .' ';
                $this->_value[] = array($key=>$v['value']);
            }
        }
        $this->_set = array();
        
        return $this;
    }
    
    /**
     * 字段绑定值
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    private function __bindValue(){
        
        if(!empty($this->_value)){
            foreach($this->_value as $v){
                foreach($v as $key=>$value){
                    if(is_array($value)){
                        $rt = $this->_stmt->bindValue($key, implode(',', $value), PDO::PARAM_STR);
                    }else{
                        $rt = $this->_stmt->bindValue($key, $value);
                    }
                    if(!$rt){
                        log_message('error', 'bind value error, msg: '. json_encode($this->_stmt->errorInfo()));
                        return false;
                    }
                }
            }
        }
        $this->_value = array();
        
        return $this;
    }
    
    /**
     * 开启事务
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function startTransaction(){
        if($this->_conn->inTransaction()){
            return $this;
        }
        $rt = $this->_conn->beginTransaction();
        if(!$rt){
            log_message('error', 'start transaction error, msg: '. json_encode($this->_conn->errorInfo()));
            return false;
        }
        
        return $this;
    }
    
    /**
     * 是否再事务中
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function inTransaction(){
        return $this->_conn->inTransaction();
    }
    
    /**
     * 回滚数据
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function rollBack(){
        $rt = $this->_conn->rollBack();
        if(!$rt){
            log_message('error', 'rollback error, msg: '. json_encode($this->_conn->errorInfo()));
            return false;
        }
        
        return true;
    }
    
    /**
     * 提交事务
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function commit(){
        $rt = $this->_conn->commit();
        if(!$rt){
            $rt = $this->_conn->rollBack();
            if(!$rt){
                log_message('error', 'rollback error, msg: '. json_encode($this->_conn->errorInfo()));
                return false;
            }
            
            log_message('error', 'commit error, msg: '. json_encode($this->_conn->errorInfo()));
            return false;
        }
        
        return true;
    }
    
    /**
     * 执行一条sql，返回结果因SQL而异:select返回结果集、insert/replace返回插入的id、delete和其他返回受影响行数
     * @param string $sql 需要执行sql语句
     * @return mixed boolean or int or array
     */
    public function query($sql){
        $this->freeResult();
        $this->_stmt = $this->_conn->query($sql);
        if(!$this->_stmt){
            log_message('error', 'sql query error, msg: '. json_encode($this->_conn->errorInfo()));
            return false;
        }

        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. json_encode($this->_stmt->errorInfo()));
            return false;
        }
        
        $sql = strtolower($sql);
        if(strpos($sql, 'select')===0){
            return $this->_stmt->fetchAll();
        }else if(strpos($sql, 'insert')===0){
            return $this->_conn->lastInsertId();
        }else if(strpos($sql, 'replace')===0){
            return $this->_conn->lastInsertId();
        }else{
            return $this->_stmt->rowCount();
        }
    }
    
    /**
     * 释放查询的结果集
     * @return mixed boolean || Database_Drivers_Pdo_Mysql
     */
    public function freeResult(){
        $this->_stmt = null;
        return $this;
    }
    
    /**
     * 获取查询记录数
     * @return mixed int || boolean
     */
    public function numRows(){
        if($this->_stmt){
            return $this->_stmt->rowCount();
        }
        
        return false;
    }
}
