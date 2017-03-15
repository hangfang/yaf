<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

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
    
    public function getLottery($data, $msgXml=array()){
        $lotteryCode = $data['lotterycode'];
        $db = Database::getInstance();
        $query = $db->order_by('id', 'desc')->limit($data['recordcnt'], 0)->get('app_'. $lotteryCode);
        $rt = $query && $query->num_rows()>0 ? $query->row_array() : array();
        
        if(empty($rt)){
            $baiduModel = new BaiduModel();
            return $baiduModel->getLottery($data, $msgXml);
        }
        
        if(empty($msgXml)){
            return $rt;
        }
        
        switch($lotteryCode){
            case 'ssq':
                $msg_extra = <<<EOF
销量: %s
奖池: %s
一等奖: 奖金%s, 共%s注
二等奖: 奖金%s, 共%s注
三等奖: 奖金%s, 共%s注
四等奖: 奖金%s, 共%s注
五等奖: 奖金%s, 共%s注
六等奖: 奖金%s, 共%s注
EOF;
                $msg_extra = sprintf($msg_extra, $rt['sell'], $rt['remain'], $rt['first'], $rt['first_num'], $rt['second'], $rt['second_num'], $rt['third'], $rt['third_num'], $rt['forth'], $rt['forth_num'], $rt['fivth'], $rt['fivth_num'], $rt['sixth'], $rt['sixth_num']);
                $rt['openCode'] = str_pad($rt['a'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['b'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['c'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['d'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['e'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['f'], 2, '0', STR_PAD_LEFT).'+'.str_pad($rt['g'], 2, '0', STR_PAD_LEFT);
                break;
            case 'fc3d':
                $msg_extra = <<<EOF
销量: %s
奖池: %s
一等奖: 奖金%s, 共%s注
二等奖: 奖金%s, 共%s注
EOF;
                $msg_extra = sprintf($msg_extra, $rt['sell'], $rt['remain'], $rt['first'], $rt['first_num'], $rt['second'], $rt['second_num']);
                $rt['openCode'] = $rt['a'].','.$rt['b'].','.$rt['c'];
                break;
            case 'dlt':
                $msg_extra = <<<EOF
销量: %s
奖池: %s
一等奖: 奖金%s, 共%s注
追加: 奖金%s, 共%s注
二等奖: 奖金%s, 共%s注
追加: 奖金%s, 共%s注
三等奖: 奖金%s, 共%s注
追加: 奖金%s, 共%s注
四等奖: 奖金%s, 共%s注
追加: 奖金%s, 共%s注
五等奖: 奖金%s, 共%s注
追加: 奖金%s, 共%s注
六等奖: 奖金%s, 共%s注
EOF;
                $msg_extra = sprintf($msg_extra, $rt['sell'], $rt['remain'], $rt['first'], $rt['first_num'], $rt['first_add'], $rt['first_add_num'], $rt['second'], $rt['second_num'], $rt['second_add'], $rt['second_add_num'], $rt['third'], $rt['third_num'], $rt['third_add'], $rt['third_add_num'], $rt['forth'], $rt['forth_num'], $rt['forth_add'], $rt['forth_add_num'], $rt['fivth'], $rt['fivth_num'], $rt['fivth_add'], $rt['fivth_add_num'], $rt['sixth'], $rt['sixth_num']);
                $rt['openCode'] = str_pad($rt['a'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['b'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['c'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['d'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['e'], 2, '0', STR_PAD_LEFT).'+'.str_pad($rt['f'], 2, '0', STR_PAD_LEFT).' '.str_pad($rt['g'], 2, '0', STR_PAD_LEFT);
                break;
            case 'pls':
                $msg_extra = <<<EOF
销量: %s
奖池: %s
一等奖: 奖金%s, 共%s注
二等奖: 奖金%s, 共%s注
EOF;
                $msg_extra = sprintf($msg_extra, $rt['sell'], $rt['remain'], $rt['first'], $rt['first_num'], $rt['second'], $rt['second_num']);
                $rt['openCode'] = $rt['a'].','.$rt['b'].','.$rt['c'];
                break;
            case 'plw':
                $msg_extra = <<<EOF
销量: %s
奖池: %s
一等奖: 奖金%s, 共%s注
EOF;
                $msg_extra = sprintf($msg_extra, $rt['sell'], $rt['remain'], $rt['first'], $rt['first_num']);
                $rt['openCode'] = $rt['a'].','.$rt['b'].','.$rt['c'].','.$rt['d'].','.$rt['e'];
                break;
            case 'qxc':
                $msg_extra = <<<EOF
销量: %s
奖池: %s
一等奖: 奖金%s, 共%s注
二等奖: 奖金%s, 共%s注
三等奖: 奖金%s, 共%s注
四等奖: 奖金%s, 共%s注
五等奖: 奖金%s, 共%s注
六等奖: 奖金%s, 共%s注
EOF;
                $msg_extra = sprintf($msg_extra, $rt['sell'], $rt['remain'], $rt['first'], $rt['first_num'], $rt['second'], $rt['second_num'], $rt['third'], $rt['third_num'], $rt['forth'], $rt['forth_num'], $rt['fivth'], $rt['fivth_num'], $rt['sixth'], $rt['sixth_num']);
                $rt['openCode'] = $rt['a'].','.$rt['b'].','.$rt['c'].','.$rt['d'].','.$rt['e'].','.$rt['f'].','.$rt['g'];
                break;
        }
        
        $msgformat = get_var_from_conf('msgformat');
            
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];

        $lottery = get_var_from_conf('lottery');
        $lottery = array_flip($lottery);
        $data['text']['content'] = sprintf($msgformat['msg_lottery'], $lottery[$lotteryCode], $rt['expect'], $rt['openCode'], $msg_extra);
        return $data;
    }
}
