<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class AppController extends Yaf_Controller_Abstract{
    
    public function expressAction(){
        
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        if(!$request->isXmlHttpRequest()){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['request_not_allowed']));
            $response->response();
            return false;
        }
        
        $com = $request->getPost('com');
        $nu = $request->getPost('nu');

        if(!$com){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['express_lack_of_com_error']));
            $response->response();
            return false;
        }

        if(!$nu){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['express_lack_of_nu_error']));
            $response->response();
            return false;
        }

        $kuaidiModel = new KuaidiModel();
        $rt = $kuaidiModel->kdniao($com, $nu);

        if(isset($rt['Reason']) && strlen($rt['Reason']) > 0){
            $data = array();
            $data['rtn'] = 1;
            $data['errmsg'] = $rt['Reason'];
            
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($data));
            $response->response();
            return false;
        }


        $_trace = "";
        foreach($rt['Traces'] as $_v){
            $_trace .= '<p class="weui_media_desc">'. date('m月d日 H:i:s', strtotime($_v['AcceptTime'])) .'<br/>'. $_v['AcceptStation'] .'</p>';
        }

        $data = array();
        $data['rtn'] = 0;
        $data['msg'] = $_trace;

        
        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($data));
        $response->response();
        return false;
    }
    
    public function stockAction(){
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        if(!$request->isXmlHttpRequest()){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['request_not_allowed']));
            $response->response();
            return false;
        }
        
        $stockid = $request->getPost('stockid');
        if(!$stockid){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['stock_lack_of_stockid_error']));
            $response->response();
            return false;
        }

        if(preg_match('/^6[\d]{5}$/i', $stockid) === 1){
            $stockid = 'sh'. $stockid;//上海
        }elseif(preg_match('/^0[\d]{5}|3[\d]{5}$/i', $stockid) === 1){
            $stockid = 'sz'. $stockid;//深圳
        }else{
            $stockid = $stockid;
        }

        $baiduModel = new BaiduModel();
        $rt = $baiduModel->getStock($stockid);

        if(isset($rt['errNum']) && $rt['errNum']-0 > 0){
            $data = array();
            $data['rtn'] = 1;
            $data['errmsg'] = $rt['errMsg'];

            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($data));
            $response->response();
            return false;
        }

        $stockInfo = $rt['retData']['stockinfo'][0];
        $msgformat = get_var_from_conf('msgformat');
        $msg = sprintf($msgformat['msg_stock_web'], $stockInfo['name'], $stockInfo['code'], $stockInfo['date'], $stockInfo['time'], $stockInfo['OpenningPrice'], $stockInfo['closingPrice'], $stockInfo['currentPrice'], $stockInfo['hPrice'], $stockInfo['lPrice'], $stockInfo['competitivePrice'], $stockInfo['auctionPrice'], number_format($stockInfo['totalNumber']/1000000, 1), number_format($stockInfo['turnover']/100000000, 2), number_format($stockInfo['increase']-0, 2).'%', number_format($stockInfo['buyOne']/100, 0), $stockInfo['buyOnePrice'], number_format($stockInfo['buyTwo']/100, 0), $stockInfo['buyTwoPrice'], number_format($stockInfo['buyThree']/100, 0), $stockInfo['buyThreePrice'], number_format($stockInfo['buyFour']/100, 0), $stockInfo['buyFourPrice'], number_format($stockInfo['buyFive']/100, 0), $stockInfo['buyFivePrice'], number_format($stockInfo['sellOne']/100, 0), $stockInfo['sellOnePrice'], number_format($stockInfo['sellTwo']/100, 0), $stockInfo['sellTwoPrice'], number_format($stockInfo['sellThree']/100, 0), $stockInfo['sellThreePrice'], number_format($stockInfo['sellFour']/100, 0), $stockInfo['sellFourPrice'], number_format($stockInfo['sellFive']/100, 0), $stockInfo['sellFivePrice'], $stockInfo['minurl'], $stockInfo['dayurl'], $stockInfo['weekurl'], $stockInfo['monthurl']);

        $data = array();
        $data['rtn'] = 0;
        $data['msg'] = $msg;

        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($data));
        $response->response();
        return false;
    }
    
    public function weatherAction(){
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        if(!$request->isXmlHttpRequest()){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['request_not_allowed']));
            $response->response();
            return false;
        }
        
        $cityid = $request->getPost('cityid');

        if(!$cityid){
            $error = get_var_from_conf('error');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['weather_lack_of_cityid_error']));
            $response->response();
            return false;
        }


        $weather = get_var_from_conf('weather');
        $baiduModel = new BaiduModel();
        $rt = $baiduModel->getWeather($weather[$cityid]);

        if(isset($rt['Reason']) && strlen($rt['Reason']) > 0){
            $data = array();
            $data['rtn'] = 1;
            $data['errmsg'] = $rt['Reason'];

            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($data));
            $response->response();
            return false;
        }
        
        $weather = $rt['retData'];
        $msgformat = get_var_from_conf('msgformat');
        $msg = sprintf($msgformat['msg_weather_web'], $weather['city'], $weather['date'], $weather['time'], $weather['weather'], $weather['temp'], $weather['h_tmp'], $weather['l_tmp'], $weather['WD'], $weather['WS'], $weather['sunrise'], $weather['sunset']);

        $data = array();
        $data['rtn'] = 0;
        $data['msg'] = $msg;

        $response->setHeader('Content-Type', 'application/json', true);
        $response->setBody(json_encode($data));
        $response->response();
        return false;
    }
}