<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class JobModel extends BaseModel{
    public function keepPlw($expect){
        $url = sprintf('http://kaijiang.500.com/shtml/plw/%s.shtml', $expect);
        $content = @file_get_contents($url);
        if(!$content){
            echo $url .' error'."\n";
            return false;
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
            
        
        $lottery = array();
        $content = explode('开奖日期', $content);

        $content = explode('开奖号码', $content[1]);
        list($lottery['insert_time'], $_) = explode('--', preg_replace(array('/年|月|日/', '/[^\d-]/'), array('-', ''), $content[0]));
        $lottery['insert_time'] = date('Y-m-d H:i:s', strtotime($lottery['insert_time']));
            
        $content = explode('本期销量', $content[1]);
        preg_match_all('/>(\d+)</', $content[0], $haoma);
        
        if(!isset($haoma[1][0])){
            echo $url .' not selled'."\n";
            return false;
        }
        $lottery['expect'] = $expect;
        $lottery['a'] = $haoma[1][0];
        $lottery['b'] = $haoma[1][1];
        $lottery['c'] = $haoma[1][2];
        $lottery['d'] = $haoma[1][3];
        $lottery['e'] = $haoma[1][4];


        $content = explode('开奖详情', $content[1]);
        preg_match_all('/>([\d,]+)元/', $content[0], $lottery['sell']);
        $lottery['sell'] = str_replace(',', '', $lottery['sell'][1][0]);
        $lottery['remain'] = 0;

        $content = explode('上一期', $content[1]);
        $content = explode('直选', $content[0]);
        $content = explode('走势图', $content[1]);

        list($_, $_, $lottery['first_num'], $_, $lottery['first']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));
        $lottery['first'] = str_replace(',', '', $lottery['first']);

        $db = Database::getInstance();
        
        if($db->where('expect', $lottery['expect'])->get('app_pl5')->num_rows()>0){
            echo $expect . ' exists' . "\n";
            return false;
        }
        
        if($db->insert('app_pl5', $lottery)){
            echo $expect . ' keep ok'. "\n";
        }
    }
    
    public function keepPls($expect){
        $url = sprintf('http://kaijiang.500.com/shtml/pls/%s.shtml', $expect);
        $content = @file_get_contents($url);
        if(!$content){
            echo $url .' error'."\n";
            return false;
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
            
        
        $lottery = array();
        $content = explode('开奖日期', $content);

        $content = explode('开奖号码', $content[1]);
        list($lottery['insert_time'], $_) = explode('--', preg_replace(array('/年|月|日/', '/[^\d-]/'), array('-', ''), $content[0]));
        $lottery['insert_time'] = date('Y-m-d H:i:s', strtotime($lottery['insert_time']));
            
        $content = explode('本期销量', $content[1]);
        preg_match_all('/>(\d+)</', $content[0], $haoma);
        
        if(!isset($haoma[1][0])){
            echo $url .' not selled'."\n";
            return false;
        }
        $lottery['expect'] = $expect;
        $lottery['a'] = $haoma[1][0];
        $lottery['b'] = $haoma[1][1];
        $lottery['c'] = $haoma[1][2];


        $content = explode('开奖详情', $content[1]);
        preg_match_all('/>([\d,]+)元/', $content[0], $lottery['sell']);
        $lottery['sell'] = str_replace(',', '', $lottery['sell'][1][0]);
        $lottery['remain'] = 0;

        $content = explode('上一期', $content[1]);
        $content = explode('直选', $content[0]);
        $keyword = strpos($content[1], '组三')!==false ? '组三' : '组六';
        $content = explode($keyword, $content[1]);

        list($_, $_, $lottery['first_num'], $_, $lottery['first']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));
        $lottery['first'] = str_replace(',', '', $lottery['first']);

        list($_, $_, $lottery['second_num'], $_, $lottery['second']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[1]));
        $lottery['second'] = str_replace(',', '', $lottery['second']);

        $db = Database::getInstance();
        
        if($db->where('expect', $lottery['expect'])->get('app_pl3')->num_rows()>0){
            echo $expect . ' exists' . "\n";
            return false;
        }
        
        if($db->insert('app_pl3', $lottery)){
            echo $expect . ' keep ok'. "\n";
        }
    }
    
    public function keepQxc($expect){
        $url = sprintf('http://kaijiang.500.com/shtml/qxc/%s.shtml', $expect);
        $content = @file_get_contents($url);
        if(!$content){
            echo $url .' error'."\n";
            return false;
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
            
        
        $lottery = array();
        $content = explode('开奖日期', $content);

        $content = explode('开奖号码', $content[1]);
        list($lottery['insert_time'], $_) = explode('--', preg_replace(array('/年|月|日/', '/[^\d-]/'), array('-', ''), $content[0]));
        $lottery['insert_time'] = date('Y-m-d H:i:s', strtotime($lottery['insert_time']));
            
        $content = explode('本期销量', $content[1]);
        preg_match_all('/>(\d+)</', $content[0], $haoma);
        
        if(!isset($haoma[1][0])){
            echo $url .' not selled'."\n";
            return false;
        }
        $lottery['expect'] = $expect;
        
        $lottery['a'] = $haoma[1][0];
        $lottery['b'] = $haoma[1][1];
        $lottery['c'] = $haoma[1][2];
        $lottery['d'] = $haoma[1][3];
        $lottery['e'] = $haoma[1][4];
        $lottery['f'] = $haoma[1][5];
        $lottery['g'] = $haoma[1][6];

        $content = explode('奖池滚存', $content[1]);
        preg_match_all('/>([\d,]+)元/', $content[0], $lottery['sell']);
        $lottery['sell'] = str_replace(',', '', $lottery['sell'][1][0]);

        $content = explode('开奖详情', $content[1]);
        preg_match_all('/>([\d,]+)元/', $content[0], $lottery['remain']);
        $lottery['remain'] = str_replace(',', '', $lottery['remain'][1][0]);

        $content = explode('走势图', $content[1]);
        $content = explode('一等奖', $content[0]);
        $content = explode('二等奖', $content[1]);

        list($_, $_, $lottery['first_num'], $_, $lottery['first']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));

        $content = explode('三等奖', $content[1]);
        list($_, $_, $lottery['second_num'], $_, $lottery['second']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));

        $content = explode('四等奖', $content[1]);
        list($_, $_, $lottery['third_num'], $_, $lottery['third']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));

        $content = explode('五等奖', $content[1]);
        list($_, $_, $lottery['forth_num'], $_, $lottery['forth']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));

        $content = explode('六等奖', $content[1]);
        list($_, $_, $lottery['fivth_num'], $_, $lottery['fivth']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));
        
        list($_, $_, $lottery['sixth_num'], $_, $lottery['sixth']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[1]));

        $db = Database::getInstance();
        
        if($db->where('expect', $lottery['expect'])->get('app_qxc')->num_rows()>0){
            echo $expect . ' exists' . "\n";
            return false;
        }
        
        if($db->insert('app_qxc', $lottery)){
            echo $expect . ' keep ok'. "\n";
        }
    }
    
    public function keepDlt($expect){
        $url = sprintf('http://kaijiang.500.com/shtml/dlt/%s.shtml', $expect);
        $content = @file_get_contents($url);
        if(!$content){
            echo $url .' error'."\n";
            return false;
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
            
        
        $lottery = array();
        $content = explode('开奖日期', $content);

        $content = explode('开奖号码', $content[1]);
        list($lottery['insert_time'], $_) = explode('--', preg_replace(array('/年|月|日/', '/[^\d-]/'), array('-', ''), $content[0]));
        $lottery['insert_time'] = date('Y-m-d H:i:s', strtotime($lottery['insert_time']));
            
        $content = explode('本期销量', $content[1]);
        preg_match_all('/>(\d+)</', $content[0], $haoma);
        
        if(!isset($haoma[1][0])){
            echo $url .' not selled'."\n";
            return false;
        }
        $lottery['expect'] = $expect;
        $lottery['a'] = $haoma[1][0];
        $lottery['b'] = $haoma[1][1];
        $lottery['c'] = $haoma[1][2];
        $lottery['d'] = $haoma[1][3];
        $lottery['e'] = $haoma[1][4];
        $lottery['f'] = $haoma[1][5];
        $lottery['g'] = $haoma[1][6];


        $content = explode('奖池滚存', $content[1]);
        $lottery['sell'] = 0;
        preg_match_all('/>([\d,]+)元/', $content[0], $match);
        isset($match[1][0]) && $lottery['sell'] = str_replace(',', '', $match['sell'][1][0]);

        $content = explode('开奖详情', $content[1]);
        $lottery['remain'] = 0;
        preg_match_all('/>([\d,]+)元/', $content[0], $match);
        isset($match[1][0]) && $lottery['remain'] = str_replace(',', '', $match[1][0]);

        $content = explode('走势图', $content[1]);
        $content = explode('一等奖', $content[0]);
        
        list($pride_first, $content) = explode('二等奖', $content[1]);
        list($pride_second, $content) = explode('三等奖', $content);
        list($pride_third, $content) = explode('四等奖', $content);
        list($pride_forth, $content) = explode('五等奖', $content);
        list($pride_fivth, $content) = explode('六等奖', $content);
        list($pride_sixth) = explode('七等奖', $content);
        
        if(strpos($pride_first, '派奖')!==false){
            $pride_first = explode('<td>', preg_replace('/\/|\s+/', '', $pride_first));
            $lottery['first_num'] = isset($pride_first['4']) ? $pride_first['4'] : 0;
            $lottery['first'] = isset($pride_first['6']) ? $pride_first['6'] : '';
            $lottery['first_add_num'] = isset($pride_first['20']) ? $pride_first['20'] : 0;
            $lottery['first_add'] = isset($pride_first['22']) ? $pride_first['22'] : '';
        }else{
            
            $pride_first = explode('<td>', preg_replace('/\/|\s+/', '', $pride_first));
            $lottery['first_num'] = isset($pride_first['4']) ? $pride_first['4'] : 0;
            $lottery['first'] = isset($pride_first['6']) ? $pride_first['6'] : '';
            $lottery['first_add_num'] = isset($pride_first['10']) ? $pride_first['10'] : 0;
            $lottery['first_add'] = isset($pride_first['12']) ? $pride_first['12'] : '';
        }
        
        
        $pride_second = explode('<td>', preg_replace('/\/|\s+/', '', $pride_second));
        $lottery['second_num'] = isset($pride_second['4']) ? $pride_second['4'] : 0;
        $lottery['second'] = isset($pride_second['6']) ? $pride_second['6'] : '';
        $lottery['second_add_num'] = isset($pride_second['13']) ? $pride_second['13'] : 0;
        $lottery['second_add'] = isset($pride_second['14']) ? $pride_second['14'] : '';
        
        $pride_third = explode('<td>', preg_replace('/\/|\s+/', '', $pride_third));
        $lottery['third_num'] = isset($pride_third['4']) ? $pride_third['4'] : 0;
        $lottery['third'] = isset($pride_third['6']) ? $pride_third['6'] : '';
        $lottery['third_add_num'] = isset($pride_third['12']) ? $pride_third['12'] : 0;
        $lottery['third_add'] = isset($pride_third['14']) ? $pride_third['14'] : '';
        
        $pride_has_add = strpos($pride_forth, '基本')!==false ? true : false;
        $pride_forth = explode('<td>', preg_replace('/\/|\s+/', '', $pride_forth));
        if($pride_has_add){
            $lottery['forth_num'] = isset($pride_forth['4']) ? $pride_forth['4'] : 0;
            $lottery['forth'] = isset($pride_forth['6']) ? $pride_forth['6'] : '';
            $lottery['forth_add_num'] = isset($pride_forth['12']) ? $pride_forth['12'] : 0;
            $lottery['forth_add'] = isset($pride_forth['14']) ? $pride_forth['14'] : '';
        }else{
            $lottery['forth_num'] = isset($pride_forth['2']) ? $pride_forth['2'] : 0;
            $lottery['forth'] = isset($pride_forth['4']) ? $pride_forth['4'] : '';
            $lottery['forth_add_num'] = 0;
            $lottery['forth_add'] = '';
        }
        
        $pride_has_add = strpos($pride_fivth, '基本')!==false ? true : false;
        $pride_fivth = explode('<td>', preg_replace('/\/|\s+/', '', $pride_fivth));
        if($pride_has_add){
            $lottery['fivth_num'] = isset($pride_fivth['4']) ? $pride_fivth['4'] : 0;
            $lottery['fivth'] = isset($pride_fivth['6']) ? $pride_fivth['6'] : '';
            $lottery['fivth_add_num'] = isset($pride_fivth['12']) ? $pride_fivth['12'] : 0;
            $lottery['fivth_add'] = isset($pride_fivth['14']) ? $pride_fivth['14'] : '';
        }else{
            $lottery['fivth_num'] = isset($pride_fivth['2']) ? $pride_fivth['2'] : 0;
            $lottery['fivth'] = isset($pride_fivth['4']) ? $pride_fivth['4'] : '';
            $lottery['fivth_add_num'] = 0;
            $lottery['fivth_add'] = '';
        }
        
        $pride_has_add = strpos($pride_sixth, '基本')!==false ? true : false;
        $pride_sixth = explode('<td>', preg_replace('/\/|\s+/', '', $pride_sixth));
        if($pride_has_add){
            $lottery['sixth_num'] = isset($pride_sixth['4']) ? $pride_sixth['4'] : 0;
            $lottery['sixth'] = isset($pride_sixth['6']) ? $pride_sixth['6'] : '';
            $lottery['sixth_add_num'] = isset($pride_sixth['12']) ? $pride_sixth['12'] : 0;
            $lottery['sixth_add'] = isset($pride_sixth['14']) ? $pride_sixth['14'] : '';
        }else{
            $lottery['sixth_num'] = isset($pride_sixth['2']) ? $pride_sixth['2'] : 0;
            $lottery['sixth'] = isset($pride_sixth['4']) ? $pride_sixth['4'] : '';
            $lottery['sixth_add_num'] = 0;
            $lottery['sixth_add'] = '';
        }
        
        $db = Database::getInstance();
        
        if($db->where('expect', $lottery['expect'])->get('app_dlt')->num_rows()>0){
            echo $expect . ' exists' . "\n";
            return false;
        }
        
        if($db->insert('app_dlt', $lottery)){
            echo $expect . ' keep ok'. "\n";
        }
    }
    
    
    public function keep3D($expect){
        $url = sprintf('http://kaijiang.500.com/shtml/sd/%s.shtml', $expect);
        $content = @file_get_contents($url);
        if(!$content){
            echo $url .' error'."\n";
            return false;
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
            
        
        $lottery = array();
        $content = explode('开奖日期', $content);

        $content = explode('开奖号码', $content[1]);
        list($lottery['insert_time'], $_) = explode('--', preg_replace(array('/年|月|日/', '/[^\d-]/'), array('-', ''), $content[0]));
        $lottery['insert_time'] = date('Y-m-d H:i:s', strtotime($lottery['insert_time']));
            
        $content = explode('本期销量', $content[1]);
        preg_match_all('/>(\d+)</', $content[0], $haoma);
        
        if(!isset($haoma[1][0])){
            echo $url .' not selled'."\n";
            return false;
        }
        $lottery['expect'] = $expect;
        $lottery['a'] = $haoma[1][0];
        $lottery['b'] = $haoma[1][1];
        $lottery['c'] = $haoma[1][2];


        $content = explode('开奖详情', $content[1]);
        preg_match_all('/>([\d,]+)元/', $content[0], $lottery['sell']);
        $lottery['sell'] = str_replace(',', '', $lottery['sell'][1][0]);
        $lottery['remain'] = 0;

        $content = explode('上一期', $content[1]);
        $content = explode('单选', $content[0]);
        $keyword = strpos($content[1], '组三')!==false ? '组三' : '组六';
        $content = explode($keyword, $content[1]);

        list($_, $_, $lottery['first_num'], $_, $lottery['first']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));
        $lottery['first'] = str_replace(',', '', $lottery['first']);

        list($_, $_, $lottery['second_num'], $_, $lottery['second']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[1]));
        $lottery['second'] = str_replace(',', '', $lottery['second']);

        $db = Database::getInstance();
        
        if($db->where('expect', $lottery['expect'])->get('app_fc3d')->num_rows()>0){
            echo $expect . ' exists' . "\n";
            return false;
        }
        
        if($db->insert('app_fc3d', $lottery)){
            echo $expect . ' keep ok'. "\n";
        }
    }
    
    public function keepSsq($expect){
        $url = sprintf('http://kaijiang.500.com/shtml/ssq/%s.shtml', $expect);
        $content = @file_get_contents($url);
        if(!$content){
            echo $url .' error'."\n";
            return false;
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'GBK');
            
        
        $lottery = array();
        $content = explode('开奖日期', $content);

        $content = explode('开奖号码', $content[1]);
        list($lottery['insert_time'], $_) = explode('--', preg_replace(array('/年|月|日/', '/[^\d-]/'), array('-', ''), $content[0]));
        $lottery['insert_time'] = date('Y-m-d H:i:s', strtotime($lottery['insert_time']));
            
        $content = explode('本期销量', $content[1]);
        preg_match_all('/>(\d+)</', $content[0], $haoma);
        
        if(!isset($haoma[1][0])){
            echo $url .' not selled'."\n";
            return false;
        }
        $lottery['expect'] = $expect;
        
        $lottery['a'] = $haoma[1][0];
        $lottery['b'] = $haoma[1][1];
        $lottery['c'] = $haoma[1][2];
        $lottery['d'] = $haoma[1][3];
        $lottery['e'] = $haoma[1][4];
        $lottery['f'] = $haoma[1][5];
        $lottery['g'] = $haoma[1][6];

        $content = explode('奖池滚存', $content[1]);
        preg_match_all('/>([\d,]+)元/', $content[0], $lottery['sell']);
        $lottery['sell'] = str_replace(',', '', $lottery['sell'][1][0]);

        $content = explode('开奖详情', $content[1]);
        preg_match_all('/>([\d,]+)元/', $content[0], $lottery['remain']);
        $lottery['remain'] = str_replace(',', '', $lottery['remain'][1][0]);

        $content = explode('上一期', $content[1]);
        $content = explode('一等奖', $content[0]);
        $content = explode('二等奖', $content[1]);

        list($_, $_, $lottery['first_num'], $_, $lottery['first']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));

        $content = explode('三等奖', $content[1]);
        list($_, $_, $lottery['second_num'], $_, $lottery['second']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));

        $content = explode('四等奖', $content[1]);
        list($_, $_, $lottery['third_num'], $_, $lottery['third']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));

        $content = explode('五等奖', $content[1]);
        list($_, $_, $lottery['forth_num'], $_, $lottery['forth']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));

        $content = explode('六等奖', $content[1]);
        list($_, $_, $lottery['fivth_num'], $_, $lottery['fivth']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[0]));
        
        list($_, $_, $lottery['sixth_num'], $_, $lottery['sixth']) = explode('<td>', preg_replace('/\/|\s+/', '', $content[1]));
        
        $db = Database::getInstance();
        
        if($db->where('expect', $lottery['expect'])->get('app_ssq')->num_rows()>0){
            echo $expect . ' exists' . "\n";
            return false;
        }
        
        if($db->insert('app_ssq', $lottery)){
            echo $expect . ' keep ok'. "\n";
        }
    }
    
}