<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class LotteryController extends Yaf_Controller_Abstract{
    public function checkAction(){
        
        $request = new Yaf_Request_Http();
        $lottery_type = strtolower($request->getQuery('lottery_type'));
        $funcName = $lottery_type .'Action';
        $this->$funcName();
        
        return false;
    }
    
    public function ssqAction(){
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        
        $data = array();
        $data['a'] = str_pad(intval($request->getQuery('a')), 2, 0, STR_PAD_LEFT);
        $data['b'] = str_pad(intval($request->getQuery('b')), 2, 0, STR_PAD_LEFT);
        $data['c'] = str_pad(intval($request->getQuery('c')), 2, 0, STR_PAD_LEFT);
        $data['d'] = str_pad(intval($request->getQuery('d')), 2, 0, STR_PAD_LEFT);
        $data['e'] = str_pad(intval($request->getQuery('e')), 2, 0, STR_PAD_LEFT);
        $data['f'] = str_pad(intval($request->getQuery('f')), 2, 0, STR_PAD_LEFT);
        $data['g'] = str_pad(intval($request->getQuery('g')), 2, 0, STR_PAD_LEFT);
        
        $lotteryModel = new LotteryModel();
        $rt = $lotteryModel->checkSsq($data);
        
        if(empty($rt)){
            $rt = array();
            $rt['rtn'] = 0;
            foreach($data as &$_v){
                $_v = str_pad($_v, 2, '0', STR_PAD_LEFT);
            }
            $rt['msg'] = '<p class="weui_media_desc">很遗憾，<span class="text-primary">'.implode('</span>,<span class="text-primary">', array_slice($data, 0, 6)).'</span>+<span class="text-danger">'. $data['g'] .'</span>未中奖...</p>';
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($rt));
            $response->response();
            return false;
        }
        
        
        
        $str = '';
        foreach($rt as $_k=>$_v){
            if($_k==='二等奖' || $_k==='一等奖'){
                foreach($_v as $_vv){
                    $str .= '<p class="weui_media_desc">'.date('Y-m-d', strtotime($_vv['insert_time'])).'&nbsp;&nbsp;中'. $_k .'，奖金<span class="text-danger">￥'. number_format($_vv['pride_value'], 0, '', ',') .'元</span></p>';
                }
            }else{
                $str .= '<p class="weui_media_desc">中'. $_k .'<span class="text-danger">'. $_v .'次</span></p>';
            }
        }
        
        $rt = array();
        $rt['rtn'] = 0;
        $rt['msg'] = '<p class="weui_media_desc">恭喜你，<span class="text-primary">'.implode('</span>,<span class="text-primary">', array_slice($data, 0, 6)).'</span>+<span class="text-danger">'. $data['g'] .'</span></p>'. $str;
        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($rt));
        $response->response();
        return false;
    }
    
    public function fc3dAction(){
        
        $response = new Yaf_Response_Http();
        $data = array();
        $data['a'] = intval($request->getQuery('a'));
        $data['b'] = intval($request->getQuery('b'));
        $data['c'] = intval($request->getQuery('c'));
        
        $lotteryModel = new LotteryModel();
        $rt = $lotteryModel->checkFc3d($data);
        
        if(empty($rt)){
            $rt = array();
            $rt['rtn'] = 0;
            $rt['msg'] = '<p class="weui_media_desc">很遗憾，<span class="text-primary">'.implode('</span>,<span class="text-primary">', $data).'</span>未中奖...</p>';
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($rt));
            $response->response();
            return false;
        }
        
        
        
        $str = '';
        foreach($rt as $_k=>$_v){
            $str .= '<p class="weui_media_desc">'. $_k .'<span class="text-danger">'. $_v .'次</span></p>';
        }
        
        $rt = array();
        $rt['rtn'] = 0;
        $rt['msg'] = '<p class="weui_media_desc">恭喜你，<span class="text-primary">'.implode('</span>,<span class="text-primary">', $data).'</span></p>'. $str;
        
        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($rt));
        $response->response();
        return false;
    }
    
    public function dltAction(){
        $data = array();
        $data['a'] = intval($request->getQuery('a'));
        $data['b'] = intval($request->getQuery('b'));
        $data['c'] = intval($request->getQuery('c'));
        $data['d'] = intval($request->getQuery('d'));
        $data['e'] = intval($request->getQuery('e'));
        $data['f'] = intval($request->getQuery('f'));
        $data['g'] = intval($request->getQuery('g'));
        
        $rt = array();
        $rt['rtn'] = 0;
        foreach($data as &$_v){
            $_v = str_pad($_v, 2, '0', STR_PAD_LEFT);
        }
        $rt['msg'] = '很遗憾，<span class="text-primary">'.implode('</span>,<span class="text-primary">', array_slice($data, 0, 5)).'</span>+<span class="text-danger">'. implode('</span>&nbsp;&nbsp;<span class="text-danger">', array_slice($data, 5, 2)) .'</span>未中奖...';
        
        $response = new Yaf_Response_Http();
        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($rt));
        $response->response();
        return false;
    }
    
    
    public function indexAction(){
        $data = array();
        $data['title'] = '彩票查询';
        $data['class'] = 'lottery';
        
        
        $lottery = get_var_from_conf('lottery');
        $lottery = array_flip(array_unique(array_flip($lottery)));
        
        $data['lotteryList'] = $lottery;
        $this->getView()->assign('data', $data);
    }
    
    public function pl5Action(){
        $data = array();
        $data['a'] = intval($request->getQuery('a'));
        $data['b'] = intval($request->getQuery('b'));
        $data['c'] = intval($request->getQuery('c'));
        $data['d'] = intval($request->getQuery('d'));
        $data['e'] = intval($request->getQuery('e'));
        
        $rt = array();
        $rt['rtn'] = 0;
        $rt['msg'] = '很遗憾，<span class="text-primary">'.implode('</span>,<span class="text-primary">', $data).'</span>未中奖...';
        
        $response = new Yaf_Response_Http();
        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($rt));
        $response->response();
        return false;
    }
    
    public function pl3Action(){
        $data = array();
        $data['a'] = intval($request->getQuery('a'));
        $data['b'] = intval($request->getQuery('b'));
        $data['c'] = intval($request->getQuery('c'));
        
        $rt = array();
        $rt['rtn'] = 0;
        $rt['msg'] = '很遗憾，<span class="text-primary">'.implode('</span>,<span class="text-primary">', $data).'</span>未中奖...';
        
        $response = new Yaf_Response_Http();
        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($rt));
        $response->response();
        return false;
    }
    
    
    public function prideAction(){
        
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        if(!$request->isXmlHttpRequest()){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['request_not_allowed']));
            $response->response();
            return false;
        }
        
        $data = array();
        $data['lotterycode'] = $request->getQuery('lottery_code');
        $data['lotterycode'] = $data['lotterycode'] ? $data['lotterycode'] : '';

        $data['recordcnt'] = $request->getQuery('recordcnt');
        $data['recordcnt'] = $data['recordcnt'] ? $data['recordcnt'] : 1;

        if(!$data['lotterycode']){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['lottery_lack_of_lotterycode_error']));
            $response->response();
            return false;
        }

        $lotteryModel = new LotteryModel();
        $rt = $lotteryModel->getLottery($data);

        if(empty($rt)){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['get_lottery_no_result_found']));
            $response->response();
            return false;
        }

        $lottery = get_var_from_conf('lottery');
        $lottery = array_flip($lottery);
        
        $msgformat = get_var_from_conf('msgformat');
        $code = array();
        isset($rt['a']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($rt['a'], 2, 0, STR_PAD_LEFT) : $rt['a'];
        isset($rt['b']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($rt['b'], 2, 0, STR_PAD_LEFT) : $rt['b'];
        isset($rt['c']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($rt['c'], 2, 0, STR_PAD_LEFT) : $rt['c'];
        isset($rt['d']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($rt['d'], 2, 0, STR_PAD_LEFT) : $rt['d'];
        isset($rt['e']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($rt['e'], 2, 0, STR_PAD_LEFT) : $rt['e'];
        isset($rt['f']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($rt['f'], 2, 0, STR_PAD_LEFT) : $rt['f'];
        isset($rt['g']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($rt['g'], 2, 0, STR_PAD_LEFT) : $rt['g'];

        $prideInfo = '';
        switch($data['lotterycode']){
            case 'ssq':
                $prideInfo = sprintf($msgformat['ssq_pride'], $rt['first'], $rt['first_num'], $rt['second'], $rt['second_num'], $rt['third'], $rt['third_num'], $rt['forth'], $rt['forth_num'], $rt['fivth'], $rt['fivth_num'], $rt['sixth'], $rt['sixth_num']);
                $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', array_slice($code, 0, 6)).'</span><span class="ballbg_blue">'.$code[6].'</span>';
                break;
            case 'dlt':
                $prideInfo = sprintf($msgformat['dlt_pride'], $rt['first_add'], $rt['first_add_num'], $rt['first'], $rt['first_num'], $rt['second_add'], $rt['second_add_num'], $rt['second'], $rt['second_num'], $rt['third_add'], $rt['third_add_num'], $rt['third'], $rt['third_num'], $rt['forth_add'], $rt['forth_add_num'], $rt['forth'], $rt['forth_num'], $rt['fivth_add'], $rt['fivth_add_num'], $rt['fivth'], $rt['fivth_num'], $rt['sixth'], $rt['sixth_num']);
                $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', array_slice($code, 0, 5)).'</span><span class="ballbg_blue">'.implode('</span><span class="ballbg_blue">', array_slice($code, 5, 2)).'</span>';
                break;
            case 'fc3d':
                $prideInfo = sprintf($msgformat['fc3d_pride'], $rt['first'], $rt['first_num'], $rt['second']>200?'组三':'组六', $rt['second'], $rt['second_num']);
                $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', $code).'</span>';
                break;
            case 'pl3':
                $prideInfo = sprintf($msgformat['pl3_pride'], $rt['first'], $rt['first_num'], $rt['second']>200?'组三':'组六', $rt['second'], $rt['second_num']);
                $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', $code).'</span>';
                break;
            case 'pl5':
                $prideInfo = sprintf($msgformat['pl5_pride'], $rt['first'], $rt['first_num']);
                $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', $code).'</span>';
                break;
            case 'qxc':
                $prideInfo = sprintf($msgformat['qxc_pride'], $rt['first'], $rt['first_num'], $rt['second'], $rt['second_num'], $rt['third'], $rt['third_num'], $rt['forth'], $rt['forth_num'], $rt['fivth'], $rt['fivth_num'], $rt['sixth'], $rt['sixth_num']);
                $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', $code).'</span>';
                break;
        }

        $extra = sprintf($msgformat['msg_lottery_extra'], number_format($rt['remain'], 0, '', ','), number_format($rt['sell'], 0, '', ','), $prideInfo);
        $msg = sprintf($msgformat['msg_lottery_web'], $lottery[$data['lotterycode']], $rt['expect'], substr($rt['insert_time'], 0, 10), $openCode, $extra);

        $data = array();
        $data['rtn'] = 0;
        $data['msg'] = $msg;

        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($data));
        $response->response();
        return false;
    }
    
    public function qxcAction(){
        $data = array();
        $data['a'] = intval($request->getQuery('a'));
        $data['b'] = intval($request->getQuery('b'));
        $data['c'] = intval($request->getQuery('c'));
        $data['d'] = intval($request->getQuery('d'));
        $data['e'] = intval($request->getQuery('e'));
        $data['f'] = intval($request->getQuery('f'));
        $data['g'] = intval($request->getQuery('g'));
        
        $rt = array();
        $rt['rtn'] = 0;
        $rt['msg'] = '很遗憾，<span class="text-primary">'.implode('</span>,<span class="text-primary">', $data).'</span>未中奖...';
        
        $response = new Yaf_Response_Http();
        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($rt));
        $response->response();
        return false;
    }
    
}
