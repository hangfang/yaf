<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class BaseModel {
    public static $_error = '';
    public static $_table = '';
    public static $_database = '';
    
    public static function error(){
        return static::$_error;
    }
    
    /**
     * @todo 请求远程接口
     * @return json
     * @author fanghang@fujiacaifu.com
     */
    public static function call($method, $args) {
        $api = get_var_from_conf('api');
        if(!isset($api[$method])){
            $error = get_var_from_conf('error');
            lExit(404, $error[404]['message']);
            return false;
        }
        // var_dump($args);die;
        $param = array_merge($api[$method], $args);
        return  http($param);
    }

    /**
     * 返回单条记录
     * @param array $where 查询条件
     * @param string $field 返回字段
     * @param array $limit 分页
     * @param mixed $order 排序
     * @return mixed
     */
    public static function getRow($where, $field='*', $limit=array(), $order='', $group='') {
        $method = 'getList';
        if (method_exists(get_called_class(), $method)) {
            $result = static::$method($where, $field, $limit, $order, $group);
            return empty($result) ? [] : $result[0];
        }
        
        log_message('error', get_called_class().'->'. $method .' not exists!');
        return false;
    }
    
    /**
     * 更新数据库记录
     * @param array $update 要更新的数据
     * @param array $where 更新数据的条件
     * @return boolean
     */
    public static function update($update, $where){
        if(empty(static::$_database) || empty(static::$_table)){
            return [];
        }
        
        if(empty($update)){
            log_message('error', 'sql: update '.static::$_database.'.'.static::$_table.' failed, param $update empty.');
            return false;
        }
        $db = Database::getInstance(static::$_database);
        
        !empty($where) && $db->where($where);
        $query = $db->set($update)->update(static::$_table);
        log_message('info', 'sql: update '.static::$_database.'.'.static::$_table.( $query===false ? 'failed' : 'succ') ."\nwhere: ".print_r($where, true)."\nupdate: ".print_r($update, true));
        if($query===false){
            self::$_error = 501;
            return false;
        }
        
        return $query;
    }
    
    /**
     * 删除数据库记录
     * @param array $where 删除数据的条件
     * @return boolean
     */
    public static function delete($where){
        if(empty(static::$_database) || empty(static::$_table)){
            return [];
        }
        
        $db = Database::getInstance(static::$_database);
        $query = $db->where($where)->delete(static::$_table);
        log_message('info', 'sql: delete from '.static::$_database.'.'.static::$_table.( $query===false ? 'failed' : 'succ') ."\nwhere: ".print_r($where, true));
        if($query===false){
            static::$_error = 501;
            return false;
        }
        
        return true;
    }
    
    /**
     * 替换数据库记录
     * @param array $data 要替换的数据
     * @return boolean
     */
    public static function replace($data){
        if(empty(static::$_database) || empty(static::$_table)){
            return [];
        }
        
        if(empty($data)){
            log_message('error', 'sql: replace into '.static::$_database.'.'.static::$_table.' failed, param $data empty.');
            return false;
        }
        
        $db = Database::getInstance(static::$_database);
        $query = $db->replace(static::$_table, $data);
        log_message('info', 'sql: replace into '.static::$_database.'.'.static::$_table.( $query===false ? 'failed' : 'succ') ."\ndata: ".print_r($data, true));
        if($query===false){
            static::$_error = 501;
            return false;
        }
        
        return true;
    }
    
    /**
     * 插入数据库记录
     * @param array $data 要插入的数据
     * @return boolean
     */
    public static function insert($data){
        if(empty(static::$_database) || empty(static::$_table)){
            return [];
        }
        
        if(empty($data)){
            log_message('error', 'sql: insert into '.static::$_database.'.'.static::$_table.' failed, param $data empty.');
            return false;
        }
        
        $db = Database::getInstance(static::$_database);
        $query = $db->insert(static::$_table, $data);
        log_message('info', 'sql: insert into '.static::$_database.'.'.static::$_table.( $query===false ? 'failed' : 'succ') ."\ndata: ".print_r($data, true));
        if($query===false){
            static::$_error = 501;
            return false;
        }
        
        return true;
    }

    /**
     * 批量插入
     * @param array $fields ['f1', 'f2', ...]               或   [ ['f1'=>$v1, 'f2'=>$v2], ['f1'=>$v1, 'f2'=>$v2], ... ]
     * @param mixed $data [ [$v1, $v2], [$v1, $v2], ... ]   或   null
     * @param string $insertOrUpdateKey 用于执行批量更新（如果记录不存在会插入数据！）
     * @return array|bool|mixed
     */
    public static function batchInsert(array $fields, $data = null, $insertOrUpdateKey = '')
    {
        if (empty(static::$_database) || empty(static::$_table)) {
            return [];
        }

        if (empty($fields) || !is_array($fields)) {
            log_message('error', 'sql: insert into ' . static::$_database . '.' . static::$_table . ' failed, param $field error.');
            return false;
        }

        /** @var Database_Drivers_Mysqli $db */
        if (static::$_database === 'g3') {
            $clientId = self::domain2Id();
            static::$_database = 'g3';
            $db = Database::getInstance(static::$_database = 'g3', $clientId);
        } else if (is_numeric(static::$_database)) {
            $clientId = static::$_database;
            static::$_database = 'g3';
            $db = Database::getInstance(static::$_database, $clientId);
        } else {
            $db = Database::getInstance(static::$_database);
        }

        $query = $db->batchInsert(static::$_table, $fields, $data, $insertOrUpdateKey);
        if ($query === false) {
            static::$_error = 501;
            return false;
        }

        return $query;
    }
    
    /**
     * 查询数据库记录
     * @param array $where 查询条件
     * @param mixed $field 返回字段名
     * @param mixed $limit 分页字段
     * @param mixed $order 排序字段
     * @param mixed $group 分组字段
     * @return boolean
     */
    public static function getList($where=[], $field='*', $limit=array(), $order='', $group=''){
        if(empty(static::$_database) || empty(static::$_table)){
            return [];
        }
        
        $db = Database::getInstance(static::$_database);
        
        !empty($where) && $db->where($where);

        if(!empty($group)){
            if(is_array($group)){
                $group = implode(',', $group);
            }
            
            $db->groupBy($group);
        }

        if(!empty($order)){
            if(is_array($order)){
                $group = implode(',', $order);
            }
            
            $db->orderBy($order);
        }
        
        if(!empty($limit)){
            if(!is_array($limit)){
                $limit = explode(',', $limit);
            }
            
            if(isset($limit['limit']) && isset($limit['offset'])){
                $db->limit($limit['limit'], $limit['offset']);
            }
            
            if(isset($limit[0]) && isset($limit[1])){
                $db->limit($limit[1], $limit[0]);
            }
        }

        if($field!=='*'){
            if(is_array($field)){
                $field = implode(',', $field);
            }
            
            $db->select($field);
        }

        $query = $db->get(static::$_table);
        if($query===false){
            static::$_error = 501;
            return false;
        }

        return $db->resultArray();
    }
    
    /**
     * @todo 查询表字段定义
     * @return array
     */
    public static function columns(){
        if(empty(static::$_table)){
            return [];
        }
        
        $db = Database::getInstance('information_schema');
        $where = ['`TABLE_NAME`'=>static::$_table, 'TABLE_SCHEMA'=>static::$_database];
        
        $rt = $db->where($where)->get('`COLUMNS`');
        if($rt===false){
            static::$_error = 501;
            return false;
        }
        
        return $rt->resultArray();
    }
    
    /**
     * @todo 查询表定义
     * @return array
     */
    public static function tables(){
        if(empty(static::$_database)){
            return [];
        }
        
        $db = Database::getInstance('information_schema');
        $where = ['TABLE_SCHEMA'=>static::$_database];
        
        $rt = $db->where($where)->get('`TABLES`');
        if($rt===false){
            static::$_error = 501;
            return false;
        }
        
        return $rt->resultArray();
    }
    
    /**
     * 统计
     * @param array $where
     * @param string $field
     * @param string $funcName
     * @return int
     */
    public static function count($where = [], $field = '*', $funcName = 'count')
    {
        $fieldName = 'fieldAggregate';
        $rt = self::getRow($where, "$funcName($field) as $fieldName");
        return empty($rt) ? 0 : $rt[$fieldName];
    }
}