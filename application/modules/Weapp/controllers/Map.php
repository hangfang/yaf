<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class MapController extends Yaf_Controller_Abstract{
    
    public function txmapAction(){
        
        $data = array();
        $data['clientIP'] = ip_address();
        $data['title'] = '当前位置';
        $data['class'] = 'map';
        
        $wechatModel = new WechatModel();
        $sigObj = $wechatModel->getJsApiSigObj();

        $data = array_merge($data, $sigObj);
        $this->getView()->assign('data', $data);
    }
    
    public function indexAction(){
        
        $data = array();
        $data['clientIP'] = ip_address();
        $data['title'] = '当前位置';
        $data['class'] = 'map';

        $wechatModel = new WechatModel();
        $sigObj = $wechatModel->getJsApiSigObj();

        $data = array_merge($data, $sigObj);
        $this->getView()->assign('data', $data);
    }
}