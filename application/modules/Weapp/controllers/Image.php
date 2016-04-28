<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class ImageController extends Yaf_Controller_Abstract{
    public function indexAction(){
        
        $data = array();
        $data['title'] = '图像识别';
        $data['class'] = 'map';
        
        $wechatModel = new WechatModel();
        $sigObj = $wechatModel->getJsApiSigObj();
        $data = array_merge($data, $sigObj);
        
        $this->getView()->assign('data', $data);
    }
    
    public function uploadAction(){
        $config['upload_path']      = APPLICATION_PATH .'./upload/image/';
        $config['allowed_types']    = 'bmp|jpeg|png|tmp';
        $config['max_size']     = 3072;
        $config['max_width']        = 3264;
        $config['max_height']       = 2448;

        $fileName = md5_file($_FILES['image']['tmp_name']);
        $tmp = explode('.', basename($_FILES['image']['name']));
        $ext = array_pop($tmp);
        $config['file_name'] = $fileName . '.'. $ext;

        $response = new Yaf_Response_Http();
        $response->setHeader('Content-Type', 'text/html');
        $response->setBody('<script type="text/javascript">window.parent.document.getElementById("img-responsive").src="/upload/image/'. $config['file_name'] .'";window.parent.document.getElementById("loadingToast").style.display="none";</script>');
        
        if(file_exists($config['upload_path'].$config['file_name'])){
            $response->response();
            return false;
        }

        $upload = new Upload($config);
        if (!$upload->do_upload('image')){
            $response->clearBody();
            $response->setBody('<script type="text/javascript">window.parent.document.getElementById("loadingToast").style.display="none";var dialog=window.parent.document.getElementById("dialog2");var div=dialog2.getElementsByTagName("div");div[3].innerHTML="'. $upload->display_errors() .'";dialog2.style.display="";</script>');
        }else{
            $data = array('upload_data' => $upload->data());
        }
        
        
        $response->response();
        return false;
    }
    
    public function shapeAction(){
        $data = array();
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        $response->setHeader('Content-Type', 'application/json');
        $data['url'] = $request->getPost('url', '');
        
        $error = get_var_from_conf('error');
        if(!isset($data['url']{10})){
            
            $data = array();
            $data['rtn'] = $error['image_url_empty']['errcode'];
            $data['msg'] = $error['image_url_empty']['errmsg'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }
        
        $data['app_id'] = YOUTU_APP_ID;
        $data['mode'] = 0;
        
        $youtu = new Youtu();
        $rt = $youtu->request('faceshape', $data);

        if(!$rt){
            $data['rtn'] = $error['service_unavailable']['errcode'];
            $data['msg'] = $error['service_unavailable']['errmsg'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }
        
        $rt['errcode'] = $rt['errorcode'];
        $rt['errmsg'] = $rt['errormsg'];
        unset($rt['errorcode'], $rt['errormsg']);
        $response->setBody(json_encode($data));
        $response->response();
        return FALSE;
    }
}