<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @name BasicController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class BasicController extends Yaf_Controller_Abstract {
    public $_error = array();
    private $_post = array();
    private $_post_session = array();
    private $_get = array();
    private $_uri = array();
    
	/** 
     * 执行一些初始化工作
     */
	public function init() {

        $this->_error = get_var_from_conf('error');

        $moduleName = strtolower($this->_request->module);
        $controllerName = strtolower($this->_request->controller);
        $actionName = strtolower($this->_request->action);
        if(Yaf_Registry::get('app')->environ()!=='develop' && $moduleName=='index' && $controllerName=='test'){
            lExit($this->_error[3]);
        }

        if(!is_cli()){
            if($moduleName==='job'){
                lExit($this->_error[3]);
            }

            if($moduleName==='index' || ($moduleName==='exh' && $controllerName==='sty' && $actionName==='getrichdescriptionpage')){
                $viewpath = APPLICATION_PATH.'/template/'.$moduleName.'/';
                $this->setViewpath($viewpath);
                $this->_view->assign('viewPath', $viewpath);
                $this->_view->assign('staticDir', '/static_files/'.$moduleName .'/');
                return true;
            }

            Yaf_Dispatcher::getInstance()->autoRender(false);

            if(BaseModel::whiteList($moduleName.'_'.$controllerName.'_'.$actionName)){
                return true;
            }

            $this->validRequest();
            
            BaseModel::accessible();//校验登录
        }else{//命令行下
            Yaf_Dispatcher::getInstance()->autoRender(FALSE);
            
            $tmp = get_class_methods($this->_request->getControllerName().'Controller');
            $methods = array();
            foreach($tmp as $v){
                if(strtolower(substr($v, -6))==='action'){
                    $methods[] = strtolower(preg_replace('/(.*)action$/i', '\1', $v));
                }
            }

            $action = strtolower($this->_request->getActionName());
            if(!in_array($action, $methods)){
                exit("php: '". $action ."' is not a correct command.\n\nDid you mean one of these?\n\t".implode("\n\t", $methods)."\n");
            }
        }
	}
    
    /**
     * @todo 上传图片
     * @return string 图片存放路径
     * @author fanghang@fujiacaifu.com
     */
    public function imgUpload(){
        if(!isset($_FILES['img']['tmp_name'])){
            log_message('error','图片不存在');
            lExit($this->_error[1100]);
            return false;
        }
        
        if($_FILES['img']['error']){
            lExit(502, ['', '文件大小超过限制', '文件大小超过HTML表单中隐藏域MAX_FILE_SIZE选项指定的值', '文件只有部分被上传', '没有找到上传的文件', '临时文件夹丢失', '找不到临时文件夹', '文件写入失败'][$_FILES['img']['error']]);
        }

        $file = $_FILES['img']['tmp_name'];

        if(!file_exists($file)){
            log_message('error','图片不存在');
            lExit($this->_error[1101]);
            return false;
        }

        if(!isset($_FILES['img']['type'])){
            lExit($this->_error[1102]);
            return false;
        }else{
            switch($_FILES['img']['type']){
                case 'image/jpeg':
                    $fileType = '.jpg';
                    break;
                case 'image/pjpeg':
                    $fileType = '.jpg';
                    break;
                case 'image/png':
                    $fileType = '.png';
                    break;
                default :
                    lExit($this->_error[1106]);
                    return false;
            }
        }

        if(!isset($_FILES['img']['size']) || $_FILES['img']['size'] > 3048000){
            lExit($this->_error[1103]);
            return false;
        }
        //lExit($file);
        //去掉尺寸限制
//        $fileSize = getimagesize($_FILES['img']['tmp_name']);
//        if(!$fileSize || !isset($fileSize[0]) || !isset($fileSize[1])){
//            
//lExit($this->_error[1107]);
//        }else{
//            $width = $fileSize[0];
//            $height = $fileSize[1];
//            if($width > 800 || $height > 800){
//                
//lExit($this->_error[1108]);;
//            }
//        }

        $fileMd5Name = md5_file($_FILES['img']['tmp_name']);
        $filePath = '/upload/headimg/'. substr($fileMd5Name, 0, 2) .'/'. substr($fileMd5Name, -2) .'/';

        if(!file_exists(APPLICATION_PATH . $filePath)){
            $rt = mkdir(APPLICATION_PATH . $filePath, 0777, true);
            if(!$rt){
                lExit($this->_error[1105]);
                return false;
            }
        }

        if(!move_uploaded_file($_FILES['img']['tmp_name'], APPLICATION_PATH . $filePath . $fileMd5Name . $fileType)){
            lExit($this->_error[1104]);
            return false;
        }
        
        return $filePath . $fileMd5Name . $fileType;
    }

    /**
     * @todo 删除图片
     * @return string 图片存放路径
     * @author lihaiyan@fujiacaifu.com
     */
    public function delImgByOss($file)
    {
        $oss = new Oss_Client(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_ENDPOINT);
        $oss->deleteObject(OSS_BUCKET, $file);
    }

    /**
     * @todo 上传图片
     * @return string 图片存放路径
     * @author wusifan@fujiacaifu.com
     */
    public function imgUploadByOss($biz){
        if(!isset($_FILES['img']['tmp_name'])){
            exit(json_encode($this->_error[1100]));
        }
        
        if($_FILES['img']['error']){
            lExit(502, ['', '文件大小超过限制', '文件大小超过HTML表单中隐藏域MAX_FILE_SIZE选项指定的值', '文件只有部分被上传', '没有找到上传的文件', '临时文件夹丢失', '找不到临时文件夹', '文件写入失败'][$_FILES['img']['error']]);
        }

        $file = $_FILES['img']['tmp_name'];
        if(!file_exists($file)){
            exit(json_encode($this->_error[1101]));
        }

        if(!isset($_FILES['img']['type'])){
            exit(json_encode($this->_error[1102]));
        }else{
            switch($_FILES['img']['type']){
                case 'image/jpeg':
                    $fileType = '.jpg';
                    break;
                case 'image/pjpeg':
                    $fileType = '.jpg';
                    break;
                case 'image/png':
                    $fileType = '.png';
                    break;
                default :
                    exit(json_encode($this->_error[1106]));
            }
        }

        if(!isset($_FILES['img']['size']) || $_FILES['img']['size'] > 3048000){
            exit(json_encode($this->_error[1103]));
        }

        if(!in_array($biz,IMAGE_BIZ)){   //图片业务类型是否非法判断
            exit(json_encode($this->_error[1110]));
        }

        $fileMd5Name = md5_file($_FILES['img']['tmp_name']);
        //判断服务器上是否存在此目录,将图片也在服务器上存一份
        $localfilePath = APPLICATION_PATH . '/upload/'. $biz .'/'. date('Y-m-d') .'/';
        if(!file_exists($localfilePath)){
            $rt = mkdir($localfilePath, 0777, true);
            if(!$rt){
                exit(json_encode($this->_error[1105]));
            }
        }

        if(!move_uploaded_file($_FILES['img']['tmp_name'], $localfilePath . $fileMd5Name . $fileType)){
            log_message('error','localfile:'. $localfilePath . $fileMd5Name . $fileType);
            lExit(-1,'上传图片到本地失败');
            return false;
        }

        //oss上的路径
        $filePath = 'g4/' . BaseModel::getDomain() . '/' . $biz .'/'. date('Y-m-d');
        if(!file_exists(OSS_BUCKET .'.'.substr(OSS_ENDPOINT,7).'/'. $filePath.'/')){
            $rt = $this->mkDir($filePath);
            if(!$rt){
                exit(json_encode($this->_error[1105]));
            }
        }

        rename($_FILES['img']['tmp_name'],$filePath . $fileMd5Name . $fileType);
        $path = $this->uploadFile($localfilePath . $fileMd5Name . $fileType,$filePath);
        return $path;
    }

    /**
     * 创建目录
     */
    public function mkDir($dir){
        if(empty($dir)){
            exit('param dir invalid'."\n");
        }

        try {
            $oss = new Oss_Client(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_ENDPOINT);
            $result = $oss->createObjectDir(OSS_BUCKET, $dir);
        } catch (Oss_Core_Exception $e) {
            $msg = __FUNCTION__ . ": FAILED, msg:". $e->getMessage();
            log_message('error', $msg);
            exit($msg."\n");
        }
        //echo 'mkdir done!'."\n";
        return true;
    }

    /**
     * 上传本地文件
     */
    public function uploadFile($local,$filePath){
        if(empty($local)){
            exit('param local invalid'."\n");
        }

        if(!file_exists($local)){
            exit('file not found:'. $local ."\n");
        }

        try {
            $oss = new Oss_Client(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_ENDPOINT);
            $remote = $filePath.'/'.basename($local);
            $result = $oss->uploadFile(OSS_BUCKET, $remote, $local);
        } catch (Oss_Core_Exception $e) {
            $msg = __FUNCTION__ . ": FAILED, msg:". $e->getMessage();
            log_message('error', $msg);
            exit($msg."\n");
        }
        unlink($local);
        //echo 'upload file done!'."\n";
        return $remote;
    }
    
    /**
     * 校验请求头
     */
    private function validRequest(){
        if(empty(BaseModel::getDomain()) || empty(REST_REQUEST_PLATFORM) || empty(REST_TOKEN)){
            $post = BaseModel::getPost();
            $get = BaseModel::getQuery();
            $cookie = $_COOKIE;

            $body = json_encode(['error'=>['code'=>403, 'message'=>'非法请求']], JSON_UNESCAPED_UNICODE);
            log_message('all', 'request_id:'.Yaf_Registry::get('request_id')."\tip:". ip_address() ."\n    ".'response:'.$body."\n");
            exit($body);
        }
    }
    
    /**
     * 上传excel，解析成数组
     * @return array
     */   
    protected function uploadExcel(){
        if(!isset($_FILES['excel']) || !isset($_FILES['excel']["error"]) || $_FILES['excel']['error']!= 0){
            lExit(203);
        }
        
        if($_FILES['excel']['error']){
            lExit(502, ['', '文件大小超过限制', '文件大小超过HTML表单中隐藏域MAX_FILE_SIZE选项指定的值', '文件只有部分被上传', '没有找到上传的文件', '临时文件夹丢失', '找不到临时文件夹', '文件写入失败'][$_FILES['excel']['error']]);
        }
        
        $fileInfo = $_FILES['excel'];
        if(!file_exists($fileInfo['tmp_name'])){
            lExit(204);
        }
        
        if(!isset($fileInfo['size']) || $fileInfo['size'] > 20480000){
            lExit(208);
        }

        $fileMd5Name = md5_file($fileInfo['tmp_name']);
        $filePath = '/upload/excel/'. substr($fileMd5Name, 0, 2) .'/'. substr($fileMd5Name, -2) .'/';

        if(!file_exists(APPLICATION_PATH . $filePath)){
            $rt = mkdir(APPLICATION_PATH . $filePath, 0777, true);
            if(!$rt){
                lExit($this->_error[210]);
            }
        }
        
        $extName = array_pop(explode('.', $fileInfo['name']));
        $file = APPLICATION_PATH . $filePath . $fileMd5Name . '.'. ($extName ? $extName : 'xlsx');
        if(!move_uploaded_file($fileInfo['tmp_name'], $file)){
            lExit($this->_error[209]);
        }
        
        return $file; 
    }
}
