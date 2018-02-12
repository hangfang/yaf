<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * 模拟CI数据库类的Mysqli封装
 * @author fangh@me.com
 */
class Database_Drivers_Mysqli{
    /**
     * 数据库连接句柄
     * @var object
     */
    protected $_conn=null;
    /**
     * 预编译对象
     * @var object
     */
    protected $_stmt=null;
    /**
     * 是否出错
     * @var boolean
     */
    protected $_error = false;
    /**
     * 查询的结果集
     * @var result set
     */
    private $_result=null;
    /**
     * 数据库连接属性
     * @var array
     */
    protected $_options = array();
    /**
     * 查询条件 
     * @var array
     */
    protected $_condition = array();
    /**
     * SQL语句having子句
     * @var string
     */
    protected $_having = '';
    /**
     * UPDATE语句的待更新数据
     * @var array
     */
    protected $_set = array();
    /**
     * 分页查询
     * @var array
     */
    protected $_limit = array();
    /**
     * SQL语句分组
     * @var array or string
     */
    protected $_group = '';
    /**
     * 排序字段
     * @var array or string
     */
    protected $_order = array();
    /**
     * SQL语句的表名
     * @var string
     */
    protected $_table = '';
    /**
     * SELECT的返回列名
     * @var string
     */
    protected $_select = '';
    /**
     * 连表查询
     * @var string
     */
    protected $_join = '';
    /**
     * 拼接的SQL语句
     * @var string
     */
    protected $_sql = '';
    /**
     * 等待绑定的值
     * @var array
     */
    protected $_value = array();
    protected $_batchValue = array();
    /**
     * 表名的前缀
     * @var string
     */
    protected $_prefix = '';
    /**
     * 是否在事务中
     * @var boolean
     */
    private $_inTransaction = false;
    /**
     * 上次执行的SQL语句
     * @var string
     */
    private $_last_sql = '';
    /**
     * 上次执行的SQL语句绑定的值
     * @var string
     */
    private $_last_value = array();

    /**
     *配置数据的集合
     * @var array
     */
    private  $_config = array();
    private  $_default_group = '';
    
    public final function __construct($config, $default_group){
        $this->_config = $config;
        $this->_default_group = $default_group;
        // Do we have a socket path?
		if ($config['hostname'][0] === '/'){
			$hostname = NULL;
			$port = NULL;
			$socket = $config['hostname'];
		}else{
			// Persistent connection support was added in PHP 5.3.0
			$hostname = ($config['pconnect'] == TRUE && is_php('5.3')) ? 'p:'.$config['hostname'] : $config['hostname'];
			$port = empty($config['port']) ? NULL : $config['port'];
			$socket = NULL;
		}

		$client_flags = ($config['compress'] == TRUE) ? MYSQLI_CLIENT_COMPRESS : 0;
		$conn = mysqli_init();

		$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
		$conn->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, false);

		if (isset($config['stricton'])){
			if ($config['stricton']){
				$conn->options(MYSQLI_INIT_COMMAND, 'SET SESSION sql_mode = CONCAT(@@sql_mode, ",", "STRICT_ALL_TABLES")');
			}else{
				$conn->options(MYSQLI_INIT_COMMAND,
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
						defined('MYSQLI_OPT_SSL_VERIFY_SERVER_CERT') && $conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, TRUE);
					}
					// Apparently (when it exists), setting MYSQLI_OPT_SSL_VERIFY_SERVER_CERT
					// to FALSE didn't do anything, so PHP 5.6.16 introduced yet another
					// constant ...
					//
					// https://secure.php.net/ChangeLog-5.php#5.6.16
					// https://bugs.php.net/bug.php?id=68344
					elseif (defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT')){
						$conn->options(MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT, TRUE);
					}
				}

				$client_flags |= MYSQLI_CLIENT_SSL;
				$conn->ssl_set(
					isset($ssl['key'])    ? $ssl['key']    : NULL,
					isset($ssl['cert'])   ? $ssl['cert']   : NULL,
					isset($ssl['ca'])     ? $ssl['ca']     : NULL,
					isset($ssl['capath']) ? $ssl['capath'] : NULL,
					isset($ssl['cipher']) ? $ssl['cipher'] : NULL
				);
			}
		}

		if (@$conn->real_connect($hostname, $config['username'], $config['password'], $config['database'], $port, $socket, $client_flags)){
			// Prior to version 5.7.3, MySQL silently downgrades to an unencrypted connection if SSL setup fails
			if (
				($client_flags & MYSQLI_CLIENT_SSL)
				&& version_compare($conn->client_info, '5.7.3', '<=')
				&& empty($conn->query("SHOW STATUS LIKE 'ssl_cipher'")->fetch_object()->Value)
			){
				$conn->close();
				log_message('error', $message = 'MySQLi was configured for an SSL connection, but got an unencrypted connection instead!');
                throw new Exception('error', $message, '-1');
				return FALSE;
			}
            
            $this->_prefix = empty($config['prefix']) ? '' : $config['prefix'];
            
            if(!empty($config['char_set'])){
                $conn->set_charset($config['char_set']);
            }
            Yaf_Registry::set($this->_default_group, $conn);
			return true;
		}
        
        log_message('error', $message = 'mysqli connect failed, msg: '.$conn->error .'('. $conn->errno .')');
        throw new Exception($message, 9999);
		return FALSE;
    }
    
    /**
     * SQL语句条件分组开始:AND (
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function groupStart(){
        $this->_condition[] = array('key'=>'(', 'value'=>'', 'connect'=>'AND', 'op'=>'');
        return $this;
    }
    
    /**
     * SQL语句条件分组开始:OR (
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function orGroupStart(){
        $this->_condition[] = array('key'=>'(', 'value'=>'', 'connect'=>'OR', 'op'=>'');
        return $this;
    }
        
    /**
     * SQL语句条件分组结束:)
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function groupEnd(){
        $this->_condition[] = array('key'=>')', 'value'=>'', 'connect'=>'', 'op'=>'');
        return $this;
    }
    
    /**
     * SQL语句条件:AND column_name = 'xx'
     * @param mixed $where  查询条件键值对:array('id'=>1, 'name'=>'tom')
     * @param mixed $value  条件字段对应的值，$value不是null时，$where为字段名
     * @param string $connect 字句连接符 AND/OR
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    private function __where($where, $value, $connect='AND'){
        if(empty($where)){
            return $this;
        }
        
        if(is_array($where)){
            foreach($where as $k=>$v){
                if(is_array($k) || is_object($k)){
                    log_message('error', 'column name need string, '. gettype($k) . 'given');
                    $this->_condition = array();
                    $this->_error = true;
                    return false;
                }else{
                    $op = preg_replace('/[`0-9a-z_\s\.]/i', '', $k);

                    if(is_array($v)){
                        $op = (empty($op) || $op==='=') ? 'in' : 'not in';
                    }else{
                        switch($op){
                            case '!%':
                                $tmp = explode('!%', $k);
                                $side = empty($tmp[0]) ? 'left' : 'right';
                                $op = 'not like';
                                break;
                            case '!%!%':
                                $op = 'not like';
                                $side = 'both';
                                break;
                            case '%':
                                $tmp = explode('%', $k);
                                $side = empty($tmp[0]) ? 'left' : 'right';
                                $op = 'like';
                                break;
                            case '%%':
                                $op = 'like';
                                $side = 'both';
                                break;
                            case '':
                                $op = '=';
                                break;
                        }
                    }
                    
                    $k = preg_replace('/[\s><=!%]/i', '', $k);
                    $this->_condition[] = array('key'=>$k, 'value'=>$v, 'connect'=>$connect, 'op'=>$op, 'side'=>$side);
                }
            }
        }else{
            $op = preg_replace('/[`0-9a-z_\s\.]/i', '', $where);

            if(is_array($value)){
                $op = (empty($op) || $op==='=') ? 'in' : 'not in';
            }else{
                switch($op){
                    case '!%':
                        $tmp = explode('!%', $where);
                        $side = empty($tmp[0]) ? 'left' : 'right';
                        $op = 'not like';
                        break;
                    case '!%!%':
                        $op = 'not like';
                        $side = 'both';
                        break;
                    case '%':
                        $tmp = explode('%', $where);
                        $side = empty($tmp[0]) ? 'left' : 'right';
                        $op = 'like';
                        break;
                    case '%%':
                        $op = 'like';
                        $side = 'both';
                        break;
                    case '':
                        $op = '=';
                        break;
                }
            }

            $where = preg_replace('/[\s><=!%]/i', '', $where);
            $this->_condition[] = array('key'=>$where, 'value'=>$value, 'connect'=>$connect, 'op'=>$op, 'side'=>$side);
        }
        
        return $this;
    }
    
    /**
     * SQL语句条件:AND column_name = 'xx'
     * @param mixed $where  查询条件键值对:array('id'=>1, 'name'=>'tom')
     * @param mixed $value  条件字段对应的值，$value不是null时，$where为字段名
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function where($where, $value=null){
        return $this->__where($where, $value);
    }
    
    /**
     * SQL语句条件:OR column_name = 'xx'
     * @param mixed $where  查询条件键值对:array('id'=>1, 'name'=>'tom')
     * @param mixed $value  条件字段对应的值，$value不是null时，$where为字段名
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function orWhere($where, $value=null){
        return $this->__where($where, $value, 'OR');
    }
    
    /**
     * SQL语句条件:AND column_name IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @param string $connect 字句连接符 AND/OR
     * @param string $op 运算符 !=/=
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    private function __in($field, $list, $connect='AND', $op='='){
        $this->_condition[] = array('key'=>'(', 'value'=>'', 'connect'=>$connect);
        foreach($list as $v){
            $this->_condition[] = array('key'=>$field, 'value'=>$v, 'connect'=>'OR', 'op'=>$op);
        }
        $this->_condition[] = array('key'=>')', 'value'=>'');
        return $this;
    }
    
    /**
     * SQL语句条件:AND column_name IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function whereIn($field, $list){
        $list = is_array($list) ? $list : explode(',', $list);
        return $this->__in($field, $list);
    }
    
    /**
     * SQL语句条件:OR column_name IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function orWhereIn($field, $list){
        $list = is_array($list) ? $list : explode(',', $list);
        return $this->__in($field, $list, 'OR');
    }
    
    /**
     * SQL语句条件:AND column_name NOT IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function whereNotIn($field, $list){
        $list = is_array($list) ? $list : explode(',', $list);
        return $this->__in($field, $list, 'AND', '!=');
    }
    
    /**
     * SQL语句条件:OR column_name NOT IN ()
     * @param string $field  表字段名
     * @param mixed $list  查询字段的值，数组或单个值
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function orWhereNotIn($field, $list){
        $list = is_array($list) ? $list : explode(',', $list);
        return $this->__in($field, $list, 'OR', '!=');
    }
    
    /**
     * SQL语句条件:AND column_name LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function like($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'AND', 'op'=>'like', 'side'=>$side);
        return $this;
    }
    
    /**
     * SQL语句条件:OR column_name LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function orLike($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'OR', 'op'=>'like', 'side'=>$side);
        return $this;
    }
    
    /**
     * SQL语句条件:AND column_name NOT LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function notLike($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'AND', 'op'=>'not like', 'side'=>$side);
        return $this;
    }
    
    /**
     * SQL语句条件:OR column_name NOT LIKE '%xx%'
     * @param string $field  表字段名
     * @param mixed $like  搜索值
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function orNotLike($field, $like, $side='both'){
        $this->_condition[] = array('key'=>$field, 'value'=>$like, 'connect'=>'OR', 'op'=>'not like', 'side'=>$side);
        return $this;
    }
    
    /**
     * SQL语句:HAVING COUNT(column_name) >0
     * @param string $having  having 字句
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function having($having){
        $this->_having = $having;
        return $this;
    }
    
    /**
     * SQL语句:LIMIT $offset, $limit
     * @param int $limit  查询记录数
     * @param int $offset  查询偏移量
     * @return mixed boolean || Database_Drivers_Mysqli
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
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function groupBy($field){
        if(empty($field)){
            return $this;
        }
        
        $field = is_array($field) ? $field : explode(',', $field);
        $this->_group = strpos($field[0], '`')===false ? '`'.implode('`,`', $field).'`' : implode(',', $field);
    }
    
    /**
     * SQL语句:ORDER BY column_name desc
     * @param mixed $order  排序字段，支持:array('id desc', 'name asc')、'id asc'
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function orderBy($order){
        if(empty($order)){
            return $this;
        }
        
        if(is_array($order)){
            foreach($order as $v){
                $this->_order[] = $v;
            }
        }else{
            $this->_order = explode(',', $order);
            return $this;
        }
        
        return $this;
    }
    
    /**
     * SQL语句:select column_name_a,column_name_b
     * @param mixed $field  排序字段，支持:array('id', 'name')、'id,name'
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function select($field){
        if(is_string($field) && preg_match('/[`()\*]/', $field)){
            $this->_select = $field;
            return $this;
        }
        $field = is_array($field) ? $field : explode(',', $field);
        
        $this->_select = '';
        foreach($field as $_field){
            if(preg_match('/\s+as\s+/i', $_field)){
                list($origin, $alias) = preg_split('/\s+as\s+/i', $_field);
                $this->_select .= preg_match('/[`()]/', $origin) ? $origin : '`'.trim($origin).'`';
                $this->_select .= ' as ';
                $this->_select .= preg_match('/[`()]/', $alias) ? $alias : '`'.trim($alias).'`,';
                continue;
            }
            $this->_select .= preg_match('/[`()\*]/', $_field) ? trim($_field).',' : '`'.trim($_field).'`,';
        }
        
        $this->_select = trim($this->_select, ',');
        
        return $this;
    }

    /**
     * SQL语句:select count(id) as `cid`, left(...) as `lv`
     * @param $field
     * @return mixed
     * @desc 使用该方法时，应该给相应字段加上反引号
     */
    public function selectRaw($field)
    {
        if (!is_string($field)) {
            log_message('error', 'param error, selectRaw: need string');
            return false;
        }
        $this->_select = $field;
        return $this;
    }
    
    /**
     * SQL语句:select column_name_a,column_name_b from table
     * @param mixed $table  查询表名
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function from($table){
        $this->_table = !empty($this->_prefix) && strpos($table, $this->_prefix)!==0 ? $this->_prefix.$table : $table;
        
        return $this;
    }
    
    /**
     * SQL语句:inner join tablename on a.name=b.name
     * @param string $table 表名
     * @param string $on 连表条件
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function join($table, $on, $type='inner'){
        if(empty($table)){
            log_message('error', 'sql join table name can not be empty, table:'. print_r($table, true));
            return false;
        }
        
        $on = trim($on);
        if(preg_match('/^[a-z_0-9]+\s*\=\s*[a-z_0-9]+$/', $on)){
            log_message('error', 'sql join condition illegal, on:'. print_r($on, true));
            return false;
        }
        
        if(!in_array(strtolower($type), ['left', 'right', 'inner', 'outer'])){
            log_message('error', 'sql join type error, type can be: left/right/inner/outer, type:'. print_r($type, true));
            return false;
        }
        $this->_join .= ' '. $type .' join '. $table .' on '. $on .' ';
        return $this;
    }
        
    /**
     * SQL语句:select * from a where id=1 union select * from a where id=2
     * @param boolean $all 是否union all
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function union($all=false){
        $this->_sql = '(select '. $this->_select .' from '. $this->_table . $this->_join;
        
        $this->__buildWhere();
        $this->__buildGroup();
        $this->__buildHaving();
        $this->__buildOrder();
        $this->__buildLimit();

        if($this->_error){
            return false;
        }
        
        $this->_sql .= $all ? ') union all ' : ') union ';
        $this->_select = '';
        $this->_table = '';
        $this->_join = '';
        return $this;
    }
    
    /**
     * 设置更新字段
     * @param mixed $data  需要更新的键值对
     * @param mixed $value  当$value不为null时，$data是待更新的字段名
     * @return mixed boolean || Database_Drivers_Mysqli
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
     * 从结果集拿出一行数据
     * @return mixed array or boolean
     */
    public function rowArray(){
        if(!$this->_result){
            log_message('error', 'row array error, msg: got no _result');
            return false;
        }
        return $this->_result->fetch_assoc();
    }
    
    /**
     * 从结果集拿出所有数据
     * @return mixed array or boolean
     */
    public function resultArray(){
        if(!$this->_result){
            log_message('error', 'row array error, msg: got no _result');
            return false;
        }
        return $this->_result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * 从结果集拿出一行数据
     * @return mixed object or boolean
     */
    public function rowObject(){
        if(!$this->_result){
            log_message('error', 'row array error, msg: got no _result');
            return false;
        }
        return $this->_result->fetch_object();
    }

    private function ping(){
        $conn = @Yaf_Registry::get($this->_default_group);
        if(!$conn->ping()){
            log_message('error', 'mysqli lose connection with mysql server...');
            $conn->close();
            Yaf_Registry::del($this->_default_group);
            new self($this->_config, $this->_default_group);
            log_message('error', 'mysqli auto connected!');
        }
    }
    
    /**
     * 查询数据N条数据
     * @param string $table  查询表名
     * @param int $limit  查询记录数
     * @param int $offset  查询偏移量
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function get($table='', $limit=null, $offset=null){
        $this->freeResult();
        !empty($table) && $this->_table = !empty($this->_prefix) && strpos($table, $this->_prefix)!==0 ? $this->_prefix.$table : $table;
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
        
        $this->_sql .= ' (select '. $this->_select .' from '. $this->_table . $this->_join;
        $this->_select = '';
        $this->_table = '';
        $this->_join = '';

        $this->__buildWhere();
        $this->__buildGroup();
        $this->__buildHaving();
        $this->__buildOrder();
        $this->__buildLimit();

        if($this->_error){
            return false;
        }
        
        $this->_sql .= ')';

        $this->ping();
        $this->_stmt = Yaf_Registry::get($this->_default_group)->prepare($this->_sql);

        log_message('debug', 'sql: '. buildSql($this->_sql, $this->_value));
        
        $this->_last_sql = $this->_sql;
        $this->_sql = '';
        if(!$this->_stmt){
            $this->_condition = $this->_value = array();
            $this->__log_message(Yaf_Registry::get($this->_default_group));
            log_message('error', 'sql prepare error, msg: '. Yaf_Registry::get($this->_default_group)->error);
            return false;
        }
        
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            $this->__log_message($this->_stmt);
            return false;
        }
        
        $this->_result = $this->_stmt->get_result();
        if(!$this->_result){
            $this->__log_message($this->_stmt);
            return false;
        }
        return $this;
    }
    
    /**
     * 带条件查询数据N条数据
     * @param string $table  查询表名
     * @param array $where  查询条件
     * @param int $limit  查询记录数
     * @param int $offset  查询偏移量
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function getWhere($table='', $where=array(), $limit=null, $offset=null){
        if(!empty($where)){
            foreach($where as $k=>$v){
                if(is_array($v)){
                    foreach($v as $_field=>$_value){
                        $op = preg_replace('/[`0-9a-z_\s\.]/i', '', $_field);
                        $op = empty($op) ? '=' : $op;
                        $this->_condition[] = array('key'=>preg_replace('/[\s><=!]/i', '', $_field), 'value'=>$_value, 'connect'=>'AND', 'op'=>$op);
                    }
                }else{
                    $op = preg_replace('/[`0-9a-z_\s\.]/i', '', $k);
                    $op = empty($op) ? '=' : $op;
                    $this->_condition[] = array('key'=>preg_replace('/[\s><=!]/i', '', $k), 'value'=>$v, 'connect'=>'AND', 'op'=>$op);
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
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function update($table, $update=array(), $where=array()){
        $this->freeResult();
        !empty($table) && $this->_table = !empty($this->_prefix) && strpos($table, $this->_prefix)!==0 ? $this->_prefix.$table : $table;
        
        if(empty($this->_table)){
            log_message('error', 'sql error, UPDATE: need table name');
            return false;
        }
        
        !empty($update) && $this->set($update);
        if(empty($this->_set)){
            log_message('error', 'sql error, UPDATE: need data to update');
            return false;
        }
        
        !empty($where) && $this->where($where);
        if(empty($this->_condition)){
            log_message('error', 'sql error, UPDATE: need condition');
            return false;
        }
        
        $this->_sql = 'UPDATE '. $this->_table .' ';
        $this->_table = '';
        
        $this->__buildSet();
        $this->__buildWhere();
        
        if($this->_error){
            return false;
        }

        $this->ping();
        $this->_stmt = Yaf_Registry::get($this->_default_group)->prepare($this->_sql);

        log_message('debug', 'sql: '. buildSql($this->_sql, $this->_value));
        
        $this->_last_sql = $this->_sql;
        $this->_sql = '';
        if(!$this->_stmt){
            $this->_condition = $this->_value = array();
            $this->__log_message(Yaf_Registry::get($this->_default_group));
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            $this->__log_message($this->_stmt);
            return false;
        }
                
        return $this->_stmt->affected_rows;
    }
    
    /**
     * 插入数据
     * @param string $table  查询表名
     * @param mixed $data  需要插入的键值对
     * @return mixed boolean || Database_Drivers_Mysqli
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
        $this->_table = !empty($this->_prefix) && strpos($table, $this->_prefix)!==0 ? $this->_prefix.$table : $table;
        $this->_sql = 'INSERT INTO '. $this->_table .' (';
        $this->_table = '';
        
        foreach($data as $k=>$v){
            $k = strpos($k, '`')===false ? '`'.$k.'`' : $k;
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

        $this->ping();
        $this->_stmt = Yaf_Registry::get($this->_default_group)->prepare($this->_sql);

        log_message('debug', 'sql: '. buildSql($this->_sql, $this->_value));
        
        $this->_last_sql = $this->_sql;
        $this->_sql = '';
        if(!$this->_stmt){
            $this->_condition = $this->_value = array();
            $this->__log_message(Yaf_Registry::get($this->_default_group));
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            $this->__log_message($this->_stmt);
            return false;
        }

        $tmp = $this->_stmt->insert_id;
        return is_bool($tmp) ? $tmp : ($tmp==0 ? true : $tmp);//主键非自增id，会返回0
    }

    /**
     * 批量插入数据
     * @param string $table  查询表名
     * @param array $fields ['f1', 'f2', ...]               或   [ ['f1'=>$v1, 'f2'=>$v2], ['f1'=>$v1, 'f2'=>$v2], ... ]
     * @param mixed $data [ [$v1, $v2], [$v1, $v2], ... ]   或   null
     * @param string $insertOrUpdateKey 用于执行批量更新（如果记录不存在会插入数据！）
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function batchInsert($table, Array $fields, $data=null, $insertOrUpdateKey='') {
        $this->freeResult();

        if(empty($table)){
            log_message('error', 'sql error, batchInsert: need table name');
            return false;
        }

        if(empty($fields)) {
            log_message('error', 'sql error, batchInsert: need table fields');
            return false;
        }

        if (empty($data) && is_array($fields[0])) {
            array_walk($fields, function (&$value) {
                ksort($value);
            });
            $tmpFields = array_keys($fields[0]);
            foreach ($fields as $item) {
                $data[] = array_values($item);
            }
            $fields = $tmpFields;
        }

        if(empty($data)){
            log_message('error', 'sql error, batchInsert: need data');
            return false;
        }

        if (!is_array($fields) || empty($data[0]) || !is_array($data[0])) {
            log_message('error', 'sql error, batchInsert: fields|data format is not correct');
            return false;
        }

        $this->_table = !empty($this->_prefix) && strpos($table, $this->_prefix)!==0 ? $this->_prefix.$table : $table;
        $this->_sql = 'INSERT INTO '. $this->_table .' (';
        $this->_table = '';

        foreach($fields as $k){
            $k = strpos($k, '`')===false ? '`'.$k.'`' : $k;
            $this->_sql .= $k .',';
        }
        $this->_sql = trim($this->_sql, ',');

        $this->_sql .= ') VALUES ';
        foreach($data as $k=>$value){
            $this->_sql .= '(';
            foreach ($value as $_v) {
                $this->_sql .= '?,';
                $this->_value[] = $_v;
            }
            $this->_sql = rtrim($this->_sql, ',');
            $this->_sql .= '), ';
        }
        $this->_sql = rtrim($this->_sql, ', ');  // 尾部的空格也要去除

        // 如果是更新
        if (!empty($insertOrUpdateKey)) {
            $this->_sql .= ' ON DUPLICATE KEY UPDATE ';
            foreach ($fields as $_field) {
                if ($_field==$insertOrUpdateKey) continue;
                $this->_sql .= '`'.$_field.'`=VALUES(`'.$_field.'`), ';
            }
            $this->_sql  = rtrim($this->_sql, ', ');
        }


        $this->ping();
        $this->_stmt = Yaf_Registry::get($this->_default_group)->prepare($this->_sql);

        log_message('debug', 'sql: '. buildSql($this->_sql, $this->_value));

        $this->_last_sql = $this->_sql;
        $this->_sql = '';
        if(!$this->_stmt){
            $this->_condition = $this->_value = array();
            $this->__log_message(Yaf_Registry::get($this->_default_group));
            return false;
        }
        $this->__bindValue();
        $rt = $this->_stmt->execute();
        if(!$rt){
            $this->__log_message($this->_stmt);
            return false;
        }

        return $this->_stmt->affected_rows;
    }
    
    /**
     * 删除数据
     * @param string $table  查询表名
     * @param mixed $where  删除条件
     * @param int $limit  删除记录数
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function delete($table='', $where=array(), $limit=0){
        $this->freeResult();
        !empty($table) && $this->_table = !empty($this->_prefix) && strpos($table, $this->_prefix)!==0 ? $this->_prefix.$table : $table;
        
        if(empty($this->_table)){
            log_message('error', 'sql error, UPDATE: need table name');
            return false;
        }
        
        $where && $this->where($where);
        !empty($limit) && $this->_limit['limit'] = $limit;
        
        $this->_sql = 'DELETE FROM '. $this->_table;
        $this->_table = '';
        
        $this->__buildWhere();
        $this->__buildLimit();
        
        if($this->_error){
            return false;
        }

        $this->ping();
        $this->_stmt = Yaf_Registry::get($this->_default_group)->prepare($this->_sql);
        
        log_message('debug', 'sql: '. buildSql($this->_sql, $this->_value));
        
        $this->_last_sql = $this->_sql;
        $this->_sql = '';
        if(!$this->_stmt){
            $this->_condition = $this->_value = array();
            $this->__log_message(Yaf_Registry::get($this->_default_group));
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            log_message('error', 'sql execute error, msg: '. $this->_stmt->error);
            $this->__log_message($this->_stmt);
            return false;
        }
                
        return $this->_stmt->affected_rows;
    }
    
    /**
     * 替换已有的数据
     * @param string $table  查询表名
     * @param mixed $data  需要插入的键值对
     * @return mixed boolean || Database_Drivers_Mysqli
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
        $this->_table = !empty($this->_prefix) && strpos($table, $this->_prefix)!==0 ? $this->_prefix.$table : $table;
        $this->_sql = 'REPLACE INTO '. $this->_table .' (';
        $this->_table = '';
        
        foreach($data as $k=>$v){
            $k = strpos($k, '`')===false ? '`'.$k.'`' : $k;
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

        $this->ping();
        $this->_stmt = Yaf_Registry::get($this->_default_group)->prepare($this->_sql);
        
        log_message('debug', 'sql: '. buildSql($this->_sql, $this->_value));
        
        $this->_last_sql = $this->_sql;
        $this->_sql = '';
        if(!$this->_stmt){
            $this->_condition = $this->_value = array();
            $this->__log_message(Yaf_Registry::get($this->_default_group));
            return false;
        }
        $this->__bindValue($this->_stmt);
        $rt = $this->_stmt->execute();
        if(!$rt){
            $this->__log_message($this->_stmt);
            return false;
        }

        $tmp = $this->_stmt->insert_id;
        return is_bool($tmp) ? $tmp : ($tmp==0 ? true : $tmp);//主键非自增id，会返回0
    }
    
    /**
     * 组装查询条件
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    private function __buildWhere(){

        if(!empty($this->_condition)){
            $this->_sql .= ' where ';
            $preKey = '';
            foreach($this->_condition as $k=>$v){
                $this->_sql .= ' ';
                $groupStart = $v['key']==='(';
                $groupEnd = $v['key']===')';
                if(isset($v['connect']) && $k > 0 && !in_array($preKey, array('(', ''))){
                    $this->_sql .= $v['connect'] .' ';
                }

                !is_null($v['value']) && $v['value'] = str_replace('%', '\%', $v['value']);

                if($groupStart || $groupEnd){
                    $this->_sql .= $v['key'] .' ';
                }else if($v['op']==='like' || $v['op']==='not like'){
                    if($v['side']==='both'){
                        //$tmp = '%?%';
                        $v['value'] = '%'. $v['value'] .'%';
                    }else if($v['side']==='left'){
                        //$tmp = '%?';
                        $v['value'] = '%'. $v['value'];
                    }else{
                        //$tmp = '?%';
                        $v['value'] = $v['value'] .'%';
                    }
                    
                    $v['key'] = strpos($v['key'], '`')===false ? '`'.$v['key'].'`' : $v['key'];
                    $this->_sql .= $v['key'] .' '. $v['op'] .' ? ';
                    $this->_value[] = $v['value'];
                }elseif($v['op']==='in' || $v['op']==='not in'){
                    !is_array($v['value']) && $v['value'] = explode(',', $v['value']);
                    foreach($v['value'] as &$_tmp){
                        $this->_value[] = $_tmp;
                    }

                    $repeat = rtrim(str_repeat('?,', count($v['value'])), ',');
                    $v['key'] = strpos($v['key'], '`')===false ? '`'.$v['key'].'`' : $v['key'];
                    $this->_sql .= $v['key'] .' '. $v['op'] .' ('. $repeat .') ';
                }else if(is_null($v['value']) || strtoupper($v['value'])==='NULL'){
                    if($v['op']==='='){
                        $v['op'] = ' IS ';
                    }else{
                        $v['op'] = ' IS NOT ';
                    }
                    
                    $v['key'] = strpos($v['key'], '`')===false ? '`'.$v['key'].'`' : $v['key'];
                    $this->_sql .= $v['key'] .' '. $v['op'] .' NULL ';
                }else{
                    $v['key'] = strpos($v['key'], '`')===false ? '`'.$v['key'].'`' : $v['key'];
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
     * 组装having子句
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    private function __buildHaving(){
        if(!empty($this->_having)){
            $this->_sql .= ' having '. $this->_having .' ';
        }
        $this->_having = '';
        
        return $this;
    }
    
    /**
     * 组装group by子句
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    private function __buildGroup(){
        if(!empty($this->_group)){
            $this->_group = strpos($this->_group, '`')===false ? '`'.$this->_group.'`' : $this->_group;
            $this->_sql .= ' group by '. $this->_group .' ';
        }
        $this->_group = '';
        
        return $this;
    }
    
    /**
     * 组装order by子句
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    private function __buildOrder(){
        
        if(!empty($this->_order)){
            $this->_sql .= ' order by ';
            foreach($this->_order as &$v){
                list($field, $direction) = explode(' ', trim($v));
                
                $field = strpos($field, '`')===false ? '`'.$field.'`' : $field;
                $v = $field .' '.$direction;
            }
            $this->_sql .= implode(',', $this->_order);
        }
        $this->_order = array();
        
        return $this;
    }
    
    /**
     * 组装limit子句
     * @return mixed boolean || Database_Drivers_Mysqli
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
        $this->_limit = array();
        
        return $this;
    }
    
    /**
     * 组装set子句
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    private function __buildSet(){
        
        if(!empty($this->_set)){
            $this->_sql .= ' set ';
            foreach($this->_set as $k=>$v){
                if($k > 0){
                    $this->_sql .= ', ';
                }
                
                $v['key'] = strpos($v['key'], '`')===false ? '`'.$v['key'].'`' : $v['key'];
                $this->_sql .= $v['key'] .' = ? ';
                $this->_value[] = $v['value'];
            }
        }
        $this->_set = array();
        
        return $this;
    }
    
    /**
     * 字段绑定值
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    private function __bindValue(){
        if(!empty($this->_value)){
            $param = array('');
            
            foreach($this->_value as $v){
                
                if(is_int($v)){
                    $param[0] .= 'i';
                }else if(is_double($v)){
                    $param[0] .= 'd';
                }else{
                    $param[0] .= 's';
                }

                $param[] = &$v;
                unset($v);
            }
            
            $this->_last_value = $this->_value;
            $this->_value = array();

            $rt = call_user_func_array(array($this->_stmt, 'bind_param'), $param);

            if(!$rt){
                log_message('error', 'bind value error, msg: '. $this->_stmt->error);
                return false;
            }
        }

        return $this;
    }
    
    /**
     * 记录错误日志
     * @return true
     */
    public function __log_message($obj){
        $message = '';
        if($obj instanceof mysqli_stmt){
            $message .= 'mysqli execute sql error, sql: '. $this->_last_sql .' value: '. json_encode($this->_last_value) .' msg: '. json_encode($obj->error);
        }else{
            $message .= 'mysqli prepare sql error, sql: '. $this->_last_sql .' msg: '. $obj->error;
        }

        $stack = debug_backtrace();
        $stack = array_shift($stack);
        $message .= "\n".'error from: '. $stack['file'] .' @line: '. $stack['line']."\n";
        
        return log_message('error', $message);
    }
    
    /**
     * 开启事务
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function startTransaction(){
        $rt = Yaf_Registry::get($this->_default_group)->autocommit(false);
        if(!$rt){
            log_message('error', 'set auto commit error, msg: '. Yaf_Registry::get($this->_default_group)->error);
            return false;
        }
        
        $rt = Yaf_Registry::get($this->_default_group)->begin_transaction();
        if(!$rt){
            log_message('error', 'start transaction error, msg: '. Yaf_Registry::get($this->_default_group)->error);
            return false;
        }
        
        Yaf_Registry::get($this->_default_group)->_inTransaction = true;
        return $this;
    }
    
    /**
     * 是否再事务中
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function inTransaction(){
        return Yaf_Registry::get($this->_default_group)->_inTransaction;
    }
    
    /**
     * 回滚数据
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function rollBack(){
        Yaf_Registry::get($this->_default_group)->_inTransaction = false;
        $rt = Yaf_Registry::get($this->_default_group)->rollback();
        if(!$rt){
            log_message('error', 'rollback error, msg: '. Yaf_Registry::get($this->_default_group)->error);
            return false;
        }
        
        return true;
    }
    
    /**
     * 提交事务
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function commit(){
        Yaf_Registry::get($this->_default_group)->_inTransaction = false;
        $rt = Yaf_Registry::get($this->_default_group)->commit();
        if(!$rt){
            $rt = Yaf_Registry::get($this->_default_group)->rollback();
            if(!$rt){
                log_message('error', 'rollback error, msg: '. Yaf_Registry::get($this->_default_group)->error);
                return false;
            }
            
            log_message('error', 'commit error, msg: '. Yaf_Registry::get($this->_default_group)->error);
            return false;
        }
        
        $rt = Yaf_Registry::get($this->_default_group)->autocommit(true);
        return true;
    }
    
    /**
     * 执行一条sql，返回结果因SQL而异:select返回结果集、insert/replace返回插入的id、delete和其他返回受影响行数
     * @param string $sql 需要执行sql语句
     * @return boolean or int or array
     */
    public function query($sql){
        $this->freeResult();
        $this->ping();
        $this->_stmt = Yaf_Registry::get($this->_default_group)->prepare($sql);
        $this->_last_sql = $sql;
        
        log_message('debug', 'sql: '. $sql);
        
        if(!$this->_stmt){
            $this->__log_message(Yaf_Registry::get($this->_default_group));
            return false;
        }

        $rt = $this->_stmt->execute();
        if(!$rt){
            $this->__log_message($this->_stmt);
            return false;
        }

        $sql = strtolower($sql);
        if(strpos($sql, 'select')===0 || strpos($sql, 'desc')===0){
            return $this->_stmt->fetch_all(MYSQLI_ASSOC);
        }else if(strpos($sql, 'insert')===0){
            return $this->_stmt;
        }else if(strpos($sql, 'replace')===0){
            return $this->_stmt;
        }else{
            return $this->_stmt->affected_rows;
        }
    }
    
    /**
     * 执行一条sql，返回成功或者失败
     * @param string $sql 需要执行sql语句
     * @return mixed boolean or int or array
     */
    public function exec($sql){
        $this->freeResult();
        $this->ping();
        $this->_last_sql = $sql;
        
        log_message('debug', 'sql: '. $sql);

        $rt = Yaf_Registry::get($this->_default_group)->multi_query($sql);
        if($rt===false){
            $this->__log_message(Yaf_Registry::get($this->_default_group));
            return false;
        }

        return true;
    }
    
    /**
     * 释放查询的结果集
     * @return mixed boolean || Database_Drivers_Mysqli
     */
    public function freeResult(){
        try{
            $this->_stmt && ($this->_stmt instanceof mysqli_stmt) && $this->_stmt->free_result();
        }catch(Exception $e){
            Yaf_Registry::set('ping_error',1);
            log_message('error', 'mysqli free_result error, code:'. $e->getCode() .' msg: '.$e->getMessage());
        }
        #$this->_stmt = null;//会有报错send of 9 bytes failed with errno=32 Broken pipe
        $this->_result = null;
        $this->_error = false;
        return $this;
    }
    
    /**
     * 获取查询记录数
     * @return mix int or boolean
     */
    public function numRows(){
        if($this->_stmt){
            return $this->_stmt->affected_rows;
        }
        
        return false;
    }
    
    /**
     * 对查询输入转义
     * @param mixed $v 查询字段的值
     * @return string
     */
    private function quote($value){
        $tmp = $this;
        if(is_array($value)){
            $value = array_map(function($v) use($tmp){
                return "'". Yaf_Registry::get($this->_default_group)->real_escape_string($v) ."'";
            }, $value);
            return implode(',', $value);
        }
        return "'". Yaf_Registry::get($this->_default_group)->real_escape_string($value) ."'";
    }
    
    /**
     * 上次执行的SQL语句
     * @return string
     */
    public function lastQuery(){
        return $this->_last_sql;
    }
    
    /**
     * 上次执行的SQL语句绑定的值
     * @return array
     */
    public function lastValue(){
        return $this->_last_value;
    }
}
