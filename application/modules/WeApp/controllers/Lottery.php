<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

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
        
        
        Yaf_Loader::import(APPLICATION_PATH .'/conf/lottery.php');
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
            Yaf_Loader::import(APPLICATION_PATH.'/conf/error.php');
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
            Yaf_Loader::import(APPLICATION_PATH.'/conf/error.php');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['lottery_lack_of_lotterycode_error']));
            $response->response();
            return false;
        }

        $lotteryModel = new LotteryModel();
        $rt = $lotteryModel->getLottery($data);

        if(empty($rt)){
            Yaf_Loader::import(APPLICATION_PATH.'/conf/error.php');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['get_lottery_no_result_found']));
            $response->response();
            return false;
        }

        Yaf_Loader::import(APPLICATION_PATH .'/conf/lottery.php');
        $lottery = array_flip($lottery);
        
        Yaf_Loader::import(APPLICATION_PATH .'/conf/msgformat.php');
        foreach($rt as $_v){
            $code = array();
            isset($_v['a']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($_v['a'], 2, 0, STR_PAD_LEFT) : $_v['a'];
            isset($_v['b']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($_v['b'], 2, 0, STR_PAD_LEFT) : $_v['b'];
            isset($_v['c']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($_v['c'], 2, 0, STR_PAD_LEFT) : $_v['c'];
            isset($_v['d']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($_v['d'], 2, 0, STR_PAD_LEFT) : $_v['d'];
            isset($_v['e']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($_v['e'], 2, 0, STR_PAD_LEFT) : $_v['e'];
            isset($_v['f']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($_v['f'], 2, 0, STR_PAD_LEFT) : $_v['f'];
            isset($_v['g']) && $code[] = in_array($data['lotterycode'], array('ssq', 'dlt', 'qlc')) ? str_pad($_v['g'], 2, 0, STR_PAD_LEFT) : $_v['g'];

            $prideInfo = '';
            switch($data['lotterycode']){
                case 'ssq':
                    $prideInfo = sprintf($_ssq_pride, $_v['first'], $_v['first_num'], $_v['second'], $_v['second_num'], $_v['third'], $_v['third_num'], $_v['forth'], $_v['forth_num'], $_v['fivth'], $_v['fivth_num'], $_v['sixth'], $_v['sixth_num']);
                    $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', array_slice($code, 0, 6)).'</span><span class="ballbg_blue">'.$code[6].'</span>';
                    break;
                case 'dlt':
                    $prideInfo = sprintf($_dlt_pride, $_v['first_add'], $_v['first_add_num'], $_v['first'], $_v['first_num'], $_v['second_add'], $_v['second_add_num'], $_v['second'], $_v['second_num'], $_v['third_add'], $_v['third_add_num'], $_v['third'], $_v['third_num'], $_v['forth_add'], $_v['forth_add_num'], $_v['forth'], $_v['forth_num'], $_v['fivth_add'], $_v['fivth_add_num'], $_v['fivth'], $_v['fivth_num'], $_v['sixth'], $_v['sixth_num']);
                    $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', array_slice($code, 0, 5)).'</span><span class="ballbg_blue">'.implode('</span><span class="ballbg_blue">', array_slice($code, 5, 2)).'</span>';
                    break;
                case 'fc3d':
                    $prideInfo = sprintf($_fc3d_pride, $_v['first'], $_v['first_num'], $_v['second']>200?'组三':'组六', $_v['second'], $_v['second_num']);
                    $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', $code).'</span>';
                    break;
                case 'pl3':
                    $prideInfo = sprintf($_pl3_pride, $_v['first'], $_v['first_num'], $_v['second']>200?'组三':'组六', $_v['second'], $_v['second_num']);
                    $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', $code).'</span>';
                    break;
                case 'pl5':
                    $prideInfo = sprintf($_pl5_pride, $_v['first'], $_v['first_num']);
                    $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', $code).'</span>';
                    break;
                case 'qxc':
                    $prideInfo = sprintf($_qxc_pride, $_v['first'], $_v['first_num'], $_v['second'], $_v['second_num'], $_v['third'], $_v['third_num'], $_v['forth'], $_v['forth_num'], $_v['fivth'], $_v['fivth_num'], $_v['sixth'], $_v['sixth_num']);
                    $openCode = '<span class="ballbg_red">'.implode('</span><span class="ballbg_red">', $code).'</span>';
                    break;
            }

            $extra = sprintf($_msg_lottery_extra, number_format($_v['remain'], 0, '', ','), number_format($_v['sell'], 0, '', ','), $prideInfo);
            $msg = sprintf($_msg_lottery_web, $lottery[$data['lotterycode']], $_v['expect'], substr($_v['insert_time'], 0, 10), $openCode, $extra);
        }

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
