<?php

class Database_Drivers_Mysqli{
    
    private $_conn=null;
    private $_stmt=null;
    private $_result=null;
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
    private $_inTransaction = false;
    
    public function __construct($config){
        // Do we have a socket path?
		if ($config['hostname'][0] === '/'){
			$hostname = NULL;
			$port = NULL;
			$socket = $config['hostname'];
		}else{
			// Persistent connection support was added in PHP 5.3.0
			$hostname = ($config['pconnect'] === TRUE && is_php('5.3')) ? 'p:'.$config['hostname'] : $config['hostname'];
			$port = empty($config['port']) ? NULL : $config['port'];
			$socket = NULL;
		}

		$client_flags = ($config['compress'] == TRUE) ? MYSQLI_CLIENT_COMPRESS : 0;
		$this->_conn = mysqli_init();

		$this->_conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
		$this->_conn->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, TRUE);

		if (isset($config['stricton'])){
			if ($config['stricton']){
				$this->_conn->options(MYSQLI_INIT_COMMAND, 'SET SESSION sql_mode = CONCAT(@@sql_mode, ",", "STRICT_ALL_TABLES")');
			}else{
				$this->_conn->options(MYSQLI_INIT_COMMAND,
					'SET SESSION sql_mode =
					REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
					@@sql_mode,
					"STRICT_ALL_TABLES,", ""),
					",STRICT_ALL_TABLES", ""),
					"STRICT_ALL_TABLES", ""),
					"STRICT_TRANS_TABLES,", ""),
					",STRICT_TRANS_TABLES", ""),
					"STRICT_TRANS_TABLES", "")'
				);
			}
		}

		if (is_array($config['encrypt'])){
			$ssl = array();
			empty($config['encrypt']['ssl_key'])    OR $ssl['key']    = $config['encrypt']['ssl_key'];
			empty($config['encrypt']['ssl_cert'])   OR $ssl['cert']   = $config['encrypt']['ssl_cert'];
			empty($config['encrypt']['ssl_ca'])     OR $ssl['ca']     = $config['encrypt']['ssl_ca'];
			empty($config['encrypt']['ssl_capath']) OR $ssl['capath'] = $config['encrypt']['ssl_capath'];
			empty($config['encrypt']['ssl_cipher']) OR $ssl['cipher'] = $config['encrypt']['ssl_cipher'];

			if ( ! empty($ssl)){
				if (isset($config['encrypt']['ssl_verify'])){
					if ($config['encrypt']['ssl_verify']){
						defined('MYSQLI_OPT_SSL_VERIFY_SERVER_CERT') && $this->_conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, TRUE);
					}
					// Apparently (when it exists), setting MYSQLI_OPT_SSL_VERIFY_SERVER_CERT
					// to FALSE didn't do anything, so PHP 5.6.16 introduced yet another
					// constant ...
					//
					// https://secure.php.net/ChangeLog-5.php#5.6.16
					// https://bugs.php.net/bug.php?id=68344
					elseif (defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT')){
						$this->_conn->options(MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT, TRUE);
					}
				}

				$client_flags |= MYSQLI_CLIENT_SSL;
				$this->_conn->ssl_set(
					isset($ssl['key'])    ? $ssl['key']    : NULL,
					isset($ssl['cert'])   ? $ssl['cert']   : NULL,
					isset($ssl['ca'])     ? $ssl['ca']     : NULL,
					isset($ssl['capath']) ? $ssl['capath'] : NULL,
					isset($ssl['cipher']) ? $ssl['cipher'] : NULL
				);
			}
		}

		if ($this->_conn->real_connect($hostname, $config['username'], $config['password'], $config['database'], $port, $socket, $client_flags)){
			// Prior to version 5.7.3, MySQL silently downgrades to an unencrypted connection if SSL setup fails
			if (
				($client_flags & MYSQLI_CLIENT_SSL)
				&& version_compare($this->_conn->client_info, '5.7.3', '<=')
				&& empty($this->_conn->query("SHOW STATUS LIKE 'ssl_cipher'")->fetch_object()->Value)
			){
				$this->_conn->close();
				$message = 'MySQLi was configured for an SSL connection, but got an unencrypted connection instead!';
				log_message('error', $message);
				return FALSE;
			}

			return $this->_conn;
		}
        
        log_message('error', 'mysqli connect failed, msg: '.$this->_conn->error() .'('. $this->_conn->errno() .')');
		return FALSE;
    }
    
    /**
     * @todo SQL语句条件分组开始:AND (
     * @return 当前类实例
     */
    public function groupStart(){
        $this->_condition[] = array('key'=>'(', 'value'=>'', 'connect'=>'AND', 'op'=>'');
        return $this;
    }
    
    /**
     * @todo SQL语句条件分组开始:OR (
     * @return 当前类实例
     */
    public function orGroupStart(){
        $this->_condition[] = array('key'=>'(', 'value'=>'', 'connect'=>'OR', 'op'=>'');
        return $this;
    }
        
    /**
     * @todo SQL语句条件分组结束:)
     * @return 当前类实例
     */
    public function groupEnd(){
        $this->_condition[] = array('key'=>')', 'value'=>'', 'connect'=>'', 'op'=>'');
        return $this;
    }
    
    /**
     * @todo SQL语句条件:AND column_name = 'xx'
     * @param mixed $where  查询条件键值对:array('id'=>1, 'name'=>'tom')
     * @param mixed $value  条件字段对应的值，$value不是null时，$where为字段名
     * @return 当前类实例
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
     * @todo SQL语句条件:OR column_name = 'xx'
     * @param mixed $where  查询条件键值对:array('id'=>1, 'name'=>'tom')
     * @param mixed $value  条件字段对应的值，$value不是null时，$where为字段名
     * @return 当前类实例
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
     * @todo SQL语句条件:AND column_name IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return 当前类实例
     */
    public function whereIn($field, $list){
        $list = is_array($list) ? implode(',', $list) : $list;
        $this->_condition[] = array('key'=>$field, 'value'=>$list, 'connect'=>'AND', 'op'=>'in');
        return $this;
    }
    
    /**
     * @todo SQL语句条件:OR column_name IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return 当前类实例
     */
    public function orWhereIn($field, $list){
        $list = is_array($list) ? implode(',', $list) : $list;
        $this->_condition[] = array('key'=>$field, 'value'=>$list, 'connect'=>'OR', 'op'=>'in');
        return $this;
    }
    
    /**
     * @todo SQL语句条件:AND column_name NOT IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return 当前类实例
     */
    public function whereNotIn($field, $list){
        $list = is_array($list) ? implode(',', $list) : $list;
        $this->_condition[] = array('key'=>$field, 'value'=>$list, 'connect'=>'AND', 'op'=>'not in');
        return $this;
    }
    
    /**
     * @todo SQL语句条件:OR column_name NOT IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return 当前类实例
     */
    public function orWhereNotIn($field, $list){
        $list = is_array($list) ? implode(',', $list) : $list;
        $this->_condition[] = array('key'=>$field, 'value'=>$list, 'connect'=>'OR', 'op'=>'not in');
        return $this;
    }
    
    /**
     * @todo SQL语句条件:AND column_name LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return 当前类实例
     */
    public function like($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'AND', 'op'=>'like', 'side'=>$side);
        return $this;
    }
    
    /**
     * @todo SQL语句条件:OR column_name LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return 当前类实例
     */
    public function orLike($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'OR', 'op'=>'like', 'side'=>$side);
        return $this;
    }
    
    /**
     * @todo SQL语句条件:AND column_name NOT LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return 当前类实例
     */
    public function notLike($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'AND', 'op'=>'not like', 'side'=>$side);
        return $this;
    }
    
    /**
     * @todo SQL语句条件:OR column_name NOT LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return 当前类实例
     */
    public function orNotLike($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'OR', 'op'=>'not like', 'side'=>$side);
        return $this;
    }
    
    /**
     * @todo SQL语句:HAVING COUNT(column_name) >0
     * @param string $having  having 字句
     * @return 当前类实例
     */
    public function having($having){
        $this->_having[] = $having;
        return $this;
    }
    
    /**
     * @todo SQL语句:LIMIT $offset, $limit
     * @param int $limit  查询记录数
     * @param int $offset  查询偏移量
     * @return 当前类实例
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
     * @todo SQL语句:GROUP BY column_name
     * @param mixed $field  分组字段，支持:array('id', 'name')、'id,name'
     * @return 当前类实例
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
     * @todo SQL语句:ORDER BY column_name desc
     * @param mixed $order  排序字段，支持:array('id desc', 'name asc')、'id asc'
     * @return 当前类实例
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
     * @todo SQL语句:select column_name_a,column_name_b
     * @param mixed $field  排序字段，支持:array('id', 'name')、'id,name'
     * @return 当前类实例
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
     * @todo SQL语句:select column_name_a,column_name_b from table
     * @param mixed $table  查询表名
     * @return 当前类实例
     */
    public function from($table){
        $this->_table = $table;
        
        return $this;
    }
    
    /**
     * @todo 设置更新字段
     * @param mixed $data  需要更新的键值对
     * @param mixed $value  当$value不为null时，$data是待更新的字段名
     * @return 当前类实例
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
     * @todo 查询数据N条数据
     * @param string $table  查询表名
     * @param int $limit  查询记录数
     * @param int $offset  查询偏移量
     * @return 当前类实例
     */
    public function get($table='', $limit=null, $offset=null){
        $this->freeResult();
        !empty($table) && $this->_table = $table;
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
            log_message('error', 'sql prepare error, msg: '. $this->_conn->error);
            return false;
        }
        
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. $this->_stmt->error);
            return false;
        }
        
        $this->_result = $this->_stmt->get_result();
        if(!$this->_result){
            log_message('error', 'sql execute error, msg: '. $this->_stmt->error);
            return false;
        }
                
        return $this;
    }
    
    /**
     * @todo 从结果集拿出一行数据
     * @return array
     */
    public function rowArray(){
        if(!$this->_result){
            log_message('error', 'row array error, msg: got no _result');
            return false;
        }
        return $this->_result->fetch_assoc();
    }
    
    /**
     * @todo 从结果集拿出所有数据
     * @return array
     */
    public function resultArray(){
        if(!$this->_result){
            log_message('error', 'row array error, msg: got no _result');
            return false;
        }
        return $this->_result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * @todo 从结果集拿出一行数据
     * @return object
     */
    public function rowObject(){
        if(!$this->_result){
            log_message('error', 'row array error, msg: got no _result');
            return false;
        }
        return $this->_result->fetch_object();
    }
    
    /**
     * @todo 从结果集拿出所有数据
     * @return object
     */
    public function resultObject(){
        if(!$this->_result){
            log_message('error', 'row array error, msg: got no _result');
            return false;
        }
        
        $rt = array();
        while($tmp=$this->_result->fetch_object()){
            $rt[] = $tmp;
        }
        return $rt;
    }
    
    /**
     * @todo 带条件查询数据N条数据
     * @param string $table  查询表名
     * @param array $where  查询条件
     * @param int $limit  查询记录数
     * @param int $offset  查询偏移量
     * @return 当前类实例
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
     * @todo 更新数据
     * @param string $table  查询表名
     * @param mixed $update  需要更新的键值对
     * @param mixed $where  更新时的条件
     * @return 当前类实例
     */
    public function update($table, $update=array(), $where=array()){
        $this->freeResult();
        !empty($table) && $this->_table = $table;
        
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
            log_message('error', 'sql prepare error, msg: '. $this->_conn->error);
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. $this->_stmt->error);
            return false;
        }
                
        return $this->_stmt->affected_rows;
    }
    
    /**
     * @todo 插入数据
     * @param string $table  查询表名
     * @param mixed $data  需要插入的键值对
     * @return 当前类实例
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
        $this->_table = $table;
        $this->_sql = 'INSERT INTO '. $this->_table .' (';
        foreach($data as $k=>$v){
            $this->_sql .= $k .',';
        }
        $this->_sql = trim($this->_sql, ',');
        
        $this->_sql .= ') VALUES (';
        foreach($data as $k=>$v){
            $this->_sql .= '?,';
            $this->_value[] = $v;
        }
        $this->_sql = trim($this->_sql, ',');
        $this->_sql .= ')';
        
        $this->_stmt = $this->_conn->prepare($this->_sql);
        if(!$this->_stmt){
            log_message('error', 'sql prepare error, msg: '. $this->_conn->error);
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. $this->_stmt->error);
            return false;
        }
                
        return $this->_stmt->insert_id;
    }
    
    /**
     * @todo 删除数据
     * @param string $table  查询表名
     * @param mixed $where  删除条件
     * @param int $limit  删除记录数
     * @return 当前类实例
     */
    public function delete($table='', $where=array(), $limit=0){
        $this->freeResult();
        !empty($table) && $this->_table = $table;
        
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
            log_message('error', 'sql prepare error, msg: '. $this->_conn->error);
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. $this->_stmt->error);
            return false;
        }
                
        return $this->_stmt->affected_rows;
    }
    
    /**
     * @todo 替换已有的数据
     * @param string $table  查询表名
     * @param mixed $data  需要插入的键值对
     * @return 当前类实例
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
        $this->_table = $table;
        $this->_sql = 'REPLACE INTO '. $this->_table .' (';
        foreach($data as $k=>$v){
            $this->_sql .= $k .',';
        }
        $this->_sql = trim($this->_sql, ',');
        
        $this->_sql .= ') VALUES (';
        foreach($data as $k=>$v){
            $this->_sql .= '?,';
            $this->_value[] = $v;
        }
        $this->_sql = trim($this->_sql, ',');
        $this->_sql .= ')';
        echo $this->_sql;
        $this->_stmt = $this->_conn->prepare($this->_sql);
        if(!$this->_stmt){
            log_message('error', 'sql prepare error, msg: '. $this->_conn->error);
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. $this->_stmt->error);
            return false;
        }
                
        return $this->_stmt->affected_rows;
    }
    
    /**
     * @todo 组装查询条件
     * @return 当前类实例
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

                if($v['op']==='like' || $v['op']==='not like'){
                    $this->_sql .= $v['key'] .' '. $v['op'] .' ? ';
                    $tmp = $v['value'];
                    if($v['side']==='both'){
                        $tmp = '%'.$v['value'].'%';
                    }else if($v['side']==='left'){
                        $tmp = '%'.$v['value'];
                    }else{
                        $tmp = $v['value'].'%';
                    }
                    $this->_value[] = $tmp;
                }elseif($v['op']==='in' || $v['op']==='not in'){
                    $this->_sql .= $v['key'] .' '. $v['op'] .' (?) ';
                    $this->_value[] = $v['value'];
                }else if($groupStart || $groupEnd){
                    $this->_sql .= $v['key'] .' ';
                }else{
                    $this->_sql .= $v['key'] .' '. $v['op'] .' ? ';
                    $this->_value[] = $v['value'];
                }
                
                
                $preKey = $v['key'];
            }
        }
        $this->_condition = array();
        
        return $this;
    }
    
    /**
     * @todo 组装having子句
     * @return 当前类实例
     */
    private function __buildHaving(){
        if(!empty($this->_having)){
            $this->_sql .= ' '. $this->_having .' ';
        }
        $this->_having = '';
        
        return $this;
    }
    
    /**
     * @todo 组装group by子句
     * @return 当前类实例
     */
    private function __buildGroup(){
        
        if(!empty($this->_group)){
            $this->_sql .= ' group by '. $this->_group .' ';
        }
        $this->_group = '';
        
        return $this;
    }
    
    /**
     * @todo 组装order by子句
     * @return 当前类实例
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
     * @todo 组装limit子句
     * @return 当前类实例
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
     * @todo 组装set子句
     * @return 当前类实例
     */
    private function __buildSet(){
        
        if(!empty($this->_set)){
            $this->_sql .= ' set ';
            foreach($this->_set as $k=>$v){
                if($k > 0){
                    $this->_sql .= ', ';
                }
                
                $this->_sql .= $v['key'] .' = ? ';
                $this->_value[] = $v['value'];
            }
        }
        $this->_set = array();
        
        return $this;
    }
    
    /**
     * @todo 字段绑定值
     * @return 当前类实例
     */
    private function __bindValue(){
        if(!empty($this->_value)){
            $param = array('');
            
            $tmp = array();
            foreach($this->_value as $v){
                if(is_array($v)){
                    if(is_int($v[0])){
                        $param[0] .= 'i';
                    }else if(is_double($v[0])){
                        $param[0] .= 'd';
                    }else{
                        $param[0] .= 's';
                    }

                    $param[] = &implode(',', $v);
                }else{
                    if(is_int($v)){
                        $param[0] .= 'i';
                    }else if(is_double($v)){
                        $param[0] .= 'd';
                    }else{
                        $param[0] .= 's';
                    }

                    $param[] = &$v;
                }
                unset($v);
            }
            
            $rt = call_user_func_array(array($this->_stmt, 'bind_param'), $param);  
            if(!$rt){
                log_message('error', 'bind value error, msg: '. $this->_stmt->error);
                return false;
            }
        }
        $this->_value = array();
        
        return $this;
    }
    
    /**
     * @todo 开启事务
     * @return 当前类实例
     */
    public function startTransaction(){
        $rt = $this->_conn->autocommit(false);
        if(!$rt){
            log_message('error', 'set auto commit error, msg: '. $this->_conn->error);
            return false;
        }
        
        $rt = $this->_conn->begin_transaction();
        if(!$rt){
            log_message('error', 'start transaction error, msg: '. $this->_conn->error);
            return false;
        }
        
        $this->_conn->_inTransaction = true;
        return $this;
    }
    
    /**
     * @todo 是否再事务中
     * @return 当前类实例
     */
    public function inTransaction(){
        return $this->_conn->_inTransaction;
    }
    
    /**
     * @todo 回滚数据
     * @return 当前类实例
     */
    public function rollBack(){
        $this->_conn->_inTransaction = false;
        $rt = $this->_conn->rollback();
        if(!$rt){
            log_message('error', 'rollback error, msg: '. $this->_conn->error);
            return false;
        }
        
        return true;
    }
    
    /**
     * @todo 提交事务
     * @return 当前类实例
     */
    public function commit(){
        $this->_conn->_inTransaction = false;
        $rt = $this->_conn->commit();
        if(!$rt){
            $rt = $this->_conn->rollback();
            if(!$rt){
                log_message('error', 'rollback error, msg: '. $this->_conn->error);
                return false;
            }
            
            log_message('error', 'commit error, msg: '. $this->_conn->error);
            return false;
        }
        
        return true;
    }
    
    /**
     * @todo 执行一条sql，返回结果因SQL而异:select返回结果集、insert/replace返回插入的id、delete和其他返回受影响行数
     * @param string $sql 需要执行sql语句
     * @return boolean or int or array
     */
    public function query($sql){
        $this->freeResult();
        $this->_stmt = $this->_conn->query($sql);
        if(!$this->_stmt){
            log_message('error', 'sql query error, msg: '. $this->_stmt->error);
            return false;
        }
        
        $sql = strtolower($sql);
        if(strpos($sql, 'select')===0){
            return $this->_stmt->fetch_array(MYSQLI_ASSOC);
        }else if(strpos($sql, 'insert')===0){
            return $this->_stmt->insert_id;
        }else if(strpos($sql, 'replace')===0){
            return $this->_stmt->insert_id;
        }else{
            return $this->_stmt->affected_rows;
        }
    }
    
    /**
     * @todo 释放查询的结果集
     * @return 当前类实例
     */
    public function freeResult(){
        $this->_stmt && $this->_stmt->free_result();
        $this->_stmt = null;
        $this->_result = null;
        return $this;
    }
    
    /**
     * @todo 获取查询记录数
     * @return int
     */
    public function numRows(){
        if($this->_stmt){
            return $this->_stmt->affected_rows;
        }
        
        return false;
    }
}
