<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class LotteryModel extends BaseModel{
    
    public function genSsq($num, $msgXml){
        $blue = array();
        for($i=1; $i<34; $i++){
            $blue[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        
        $red = array();
        for($i=1; $i<17; $i++){
            $red[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        
        $lottery = array();
        for($i=0; $i<$num; $i++){
            $rand = array_rand($blue, 6);
            $blues = array($blue[$rand[0]], $blue[$rand[1]], $blue[$rand[2]], $blue[$rand[3]], $blue[$rand[4]], $blue[$rand[5]]);
            $rand = array_rand($red, 1);
            $reds = $red[$rand];
            
            $lottery[] = implode(' ', $blues) . '+' . $reds;
        }
        
        if(empty($msgXml)){
            $rt = array();
            $rt['rtn'] = 0;
            $rt['msg'] = $lottery;
            return $rt;
        }
        
        
        $num2ch = array(1=>'一', 2=>'二', 2=>'两', 3=>'三', 4=>'四', 5=>'五');
        
        $msgformat = get_var_from_conf('msgformat');
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '随机双色球'.$num2ch[$num].'注'."\n".implode("\n", $lottery);
        return $data;
    }
    
    public function checkSsq($data){
        $num2pride = array('', 'first', 'second', 'third', 'forth', 'fivth', 'sixth');
        $num2info = array('', '一等奖', '二等奖', '三等奖', '四等奖', '五等奖', '六等奖');
        
        $db = Database::getInstance();
        $query = $db->get('app_ssq');
        $result = $query && $query->num_rows()>0 ? $query->result_array() : array();

        $rt = array('一等奖'=>array(), '二等奖'=>array(), '三等奖'=>0, '四等奖'=>0, '五等奖'=>0, '六等奖'=>0);
        foreach($result as $_v){
            $hitBlue = array($_v['a'],$_v['b'],$_v['c'],$_v['d'],$_v['e'],$_v['f']);
            $hitRed = $_v['g'];
            $pride = 1;
            if($hitRed!=$data['g']){
                $pride++;
            }
            
            foreach($data as $_index=>$_num){
                
                
                if($_index!='g' && !in_array($_num, $hitBlue)){
                    if($pride===1){
                        $pride = 3;
                        continue;
                    }
                    $pride++;
                }
            }
            
            if($pride<3){
                $tmp = $_v;
                $tmp['pride_info'] = $num2info[$pride];
                $tmp['pride_value'] = $_v[$num2prdce[$pride]];
                $rt[$num2info[$pride]][] = $tmp;
            }elseif($pride<7){
                $rt[$num2info[$pride]]++;
            }
        }

        return $rt;
    }
    
    public function checkFc3d($data){
        $num2price = array('', 'first', 'second');
        $num2info = array('', '一等奖', '二等奖');
        
        $db = Database::getInstance();
        $query = $db->get('app_fc3d');
        $result = $query && $query->num_rows()>0 ? $query->result_array() : array();
        
        $rt = array('一等奖'=>0, '二等奖'=>0);
        
        $user_code = array($data['a'],$data['b'],$data['c']);
        foreach($result as $_v){
            $code = array($_v['a'],$_v['b'],$_v['c']);
            
            if(array_diff($code, $user_code)){
                continue;
            }

            $pride = 1;
            if(!($data['a']==$_v['a'] && $data['b']==$_v['b'] && $data['c']==$_v['c'])){
                $pride = 2;
            }
            
            $rt[$num2info[$pride]]++;
        }

        return $rt;
    }
    
    public function checkDlt($data){
        return array();
    }
    
    public function checkPl5($data){
        return array();
    }
    
    public function checkPl3($data){
        return array();
    }
    
    public function checkQxc($data){
        return array();
    }
    
    public function getLottery($data){
        $db = Database::getInstance();
        $query = $db->order_by('id', 'desc')->limit($data['recordcnt'], 0)->get('app_'. $data['lotterycode']);
        return $query && $query->num_rows()>0 ? $query->result_array() : array();
    }
}
