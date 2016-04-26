<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class AppController extends Yaf_Controller_Abstract{
    
    public function expressAction(){
        
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        if(!$request->isXmlHttpRequest()){
            Yaf_Loader::import(APPLICATION_PATH.'/conf/error.php');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['request_not_allowed']));
            $response->response();
            return false;
        }
        
        $com = $request->getPost('com');
        $nu = $request->getPost('nu');

        if(!$com){
            Yaf_Loader::import(APPLICATION_PATH.'/conf/error.php');
            $response->setHeader('Content-Type', 'application/json', true);
            $response->setBody(json_encode($error['express_lack_of_com_error']));
            $response->response();
            return false;
        }

        if(!$nu){
            Yaf_Loader::import(APPLICATION_PATH.'/conf/error.php');
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
    
}