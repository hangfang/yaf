<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class JobLotteryModel extends BaseModel{
    public function load($pride, $type){
        
        $db = Database::getInstance();
        
        if($db->where('expect', $pride['expect'])->get('app_'. $type)->num_rows()>0){
            log_message('error', $pride['expect'] .' '. $type .' exists');
            return false;
        }
        
        if($db->insert('app_'. $type, $pride)){
            return true;
        }
        
        log_message('error', $expect .' '. $type .' keep error: '. $db->last_query());
        return false;
    }
    
    public function isLoaded($expect, $type){
        $db = Database::getInstance();
        if($db->where('expect', $expect)->get('app_'. $type)->num_rows()>0){
            return true;
        }
        
        return false;
    }
}