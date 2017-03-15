<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

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
        $config['upload_path']      = BASE_PATH .'/upload/image/';
        $config['allowed_types']    = 'bmp|jpeg|png|tmp';
        $config['max_size']     = 3072;
        $config['max_width']        = 3264;
        $config['max_height']       = 2448;

        $fileName = @md5_file($_FILES['image']['tmp_name']);
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
            $response->setBody('<script type="text/javascript">window.parent.document.getElementById("loadingToast").style.display="none";var dialog2=window.parent.document.getElementById("dialog2");var div=dialog2.getElementsByTagName("div");div[3].innerHTML="'. $upload->display_errors() .'";dialog2.style.display="";</script>');
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
        $url = $request->getPost('url', '');
        
        $error = get_var_from_conf('error');
        if(!isset($url{10})){
            
            $data = array();
            $data['rtn'] = $error['image_url_empty']['errcode'];
            $data['msg'] = $error['image_url_empty']['errmsg'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }
        
        $tmp = explode('/', preg_replace('/\?.*/', '', $url));
        $filename = array_pop($tmp);
        unset($tmp);
        
        //根据你使用的平台选择一种初始化方式
        //优图开放平台初始化
        Youtu_Conf::setAppInfo(YOUTU_APP_ID, YOUTU_SECRET_ID, YOUTU_SECRET_KEY, YOUTU_USER_ID, Youtu_Conf::API_YOUTU_END_POINT);

        //人脸检测接口调用
        $rt = Youtu_Youtu::faceshapeurl($url, 1);

        if(!$rt){
            $data['rtn'] = $error['service_unavailable']['errcode'];
            $data['msg'] = $error['service_unavailable']['errmsg'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }
        
        if(isset($rt['code']) && $rt['code']>0){
            $data['rtn'] = $rt['code'];
            $data['msg'] = $rt['message'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }
 
        if($rt['errorcode'] != 0){
            $rt['rtn'] = $rt['errorcode'];
            $rt['msg'] = $rt['errormsg'];
            unset($rt['errorcode'], $rt['errormsg']);
            $response->setBody(json_encode($rt));
            $response->response();
            return FALSE;
        }

        $tmp = tempnam(BASE_PATH .'/upload/image/', '');

        if(!copy($url, $tmp)){
            $data = array();
             $data['rtn'] = $error['image_get_from_oss_error']['errcode'];
            $data['msg'] = $error['image_get_from_oss_error']['errmsg'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }
        
        $image = new Imagick($tmp);
        $draw = new ImagickDraw();
        $draw->setStrokeColor('#D82CA7');
        $draw->setFillColor('#D82CA7');

        $draw->setStrokeWidth(2);
        $draw->setFontSize(10);

        
        foreach($rt['face_shape'][0]['face_profile'] as $v){
            $image->annotateImage($draw, $v['x'], $v['y'], 0, '.');
        }
        
        foreach($rt['face_shape'][0]['left_eye'] as $v){
            $image->annotateImage($draw, $v['x'], $v['y'], 0, '.');
        }
        
        foreach($rt['face_shape'][0]['right_eye'] as $v){
            $image->annotateImage($draw, $v['x'], $v['y'], 0, '.');
        }
        
        foreach($rt['face_shape'][0]['left_eyebrow'] as $v){
            $image->annotateImage($draw, $v['x'], $v['y'], 0, '.');
        }
        
        foreach($rt['face_shape'][0]['right_eyebrow'] as $v){
            $image->annotateImage($draw, $v['x'], $v['y'], 0, '.');
        }
        
        foreach($rt['face_shape'][0]['mouth'] as $v){
            $image->annotateImage($draw, $v['x'], $v['y'], 0, '.');
        }
        
        foreach($rt['face_shape'][0]['nose'] as $v){
            $image->annotateImage($draw, $v['x'], $v['y'], 0, '.');
        }

        $image->setImageFormat('jpeg');

        unlink($tmp);

        try{
            $ossClient = new Oss_Client(ALIYUN_OSS_ACCESS_KEY_ID, ALIYUN_OSS_ACCESS_KEY_SECRET, ALIYUN_OSS_END_POINT, false);
            $ossClient->putObject(ALIYUN_OSS_BUNCKET, 'image/'.$filename, $image->getimageblob());
        } catch(Oss_Core_Exception $e) {
            $data = array();
            $data['rtn'] = $e->getCode();
            $data['msg'] = $e->getMessage();

            $response->setBody(json_encode($data));
            $response->response();

            return false;
        }
        
        $rt['img'] = 'http://oss.rbmax.com/image/'. $filename .'?rd='. microtime(true);
        $rt['rtn'] = $rt['errorcode'];
        $rt['msg'] = $rt['errormsg'];
        unset($rt['errorcode'], $rt['errormsg']);
            
        $response->setBody(json_encode($rt));
        $response->response();

        return false;
    }
    
    public function analyseAction(){
        
        $data = array();
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        $response->setHeader('Content-Type', 'application/json');
        $url = $request->getPost('url', '');
        
        $error = get_var_from_conf('error');
        if(!isset($url{10})){
            
            $data = array();
            $data['rtn'] = $error['image_url_empty']['errcode'];
            $data['msg'] = $error['image_url_empty']['errmsg'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }
        
        $tmp = explode('/', preg_replace('/\?.*/', '', $url));
        $filename = array_pop($tmp);
        unset($tmp);
        
        //根据你使用的平台选择一种初始化方式
        //优图开放平台初始化
        Youtu_Conf::setAppInfo(YOUTU_APP_ID, YOUTU_SECRET_ID, YOUTU_SECRET_KEY, YOUTU_USER_ID, Youtu_Conf::API_YOUTU_END_POINT);

        //人脸检测接口调用
        $rt = Youtu_Youtu::detectfaceurl($url, 1);

        if(!$rt){
            $data['rtn'] = $error['service_unavailable']['errcode'];
            $data['msg'] = $error['service_unavailable']['errmsg'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }
        
        if(isset($rt['code']) && $rt['code']>0){
            $data['rtn'] = $rt['code'];
            $data['msg'] = $rt['message'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }
 
        if($rt['errorcode'] != 0){
            $rt['rtn'] = $rt['errorcode'];
            $rt['msg'] = $rt['errormsg'];
            unset($rt['errorcode'], $rt['errormsg']);
            $response->setBody(json_encode($rt));
            $response->response();
            return FALSE;
        }
        
        $face = $rt['face'][0];
        
        $tmp = tempnam(BASE_PATH .'/upload/image/', '');

        if(!copy($url, $tmp)){
            $data = array();
            $data['rtn'] = $error['image_get_from_oss_error']['errcode'];
            $data['msg'] = $error['image_get_from_oss_error']['errmsg'];
            $response->setBody(json_encode($data));
            $response->response();
            return FALSE;
        }

        $image = new Imagick($tmp);
        $draw = new ImagickDraw(); 
        $draw->setStrokeColor('#D82CA7');
        $draw->setFillColor('#D82CA7');

        $draw->setStrokeWidth(1);
        //$draw->setFont(BASE_PATH .'/fonts/texb.ttf');
        $draw->setFontSize(18);

        $xStart = $face['x'];
        $xEnd = $face['x']+$face['width'];
        $yStart = $face['y'];
        $yEnd = $face['y']+$face['height'];
        
        $draw->line($xStart, $yStart, $xEnd, $yStart);
        $draw->line($xEnd, $yStart, $xEnd, $yEnd);
        $draw->line($xEnd, $yEnd, $xStart, $yEnd);
        $draw->line($xStart, $yEnd, $xStart, $yStart);
        
        $text = <<<EOF
gender: %s
age: %s
expression: %s
beauty: %s
glass: %s
EOF;
        $text = sprintf($text, $face['gender']<50?'female':'male', $face['age'], $face['expression']<50?'normal':'smile', $face['beauty'], $face['glass']?'wearing':'no');
        $image->annotateImage($draw, 10, 45, 0, $text);

        $image->setImageFormat('jpeg');
        $image->drawImage($draw);
        unlink($tmp);

        try{
            $ossClient = new Oss_Client(ALIYUN_OSS_ACCESS_KEY_ID, ALIYUN_OSS_ACCESS_KEY_SECRET, ALIYUN_OSS_END_POINT, false);
            $ossClient->putObject(ALIYUN_OSS_BUNCKET, 'image/'.$filename, $image->getimageblob());
        } catch(Oss_Core_Exception $e) {
            $data = array();
            $data['rtn'] = $e->getCode();
            $data['msg'] = $e->getMessage();

            $response->setBody(json_encode($data));
            $response->response();

            return false;
        }
        
        $rt['img'] = 'http://oss.rbmax.com/image/'. $filename .'?rd='. microtime(true);
        $rt['rtn'] = $rt['errorcode'];
        $rt['msg'] = $rt['errormsg'];
        unset($rt['errorcode'], $rt['errormsg']);
            
        $response->setBody(json_encode($rt));
        $response->response();

        return false;
    }
}