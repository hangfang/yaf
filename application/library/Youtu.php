<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

/**
 * @todo 腾讯优图API
 * @author hangfang
 * @date 2016-04-28
 */
class Youtu{
    
    private $_app_id = 1006952;
    private $_secret_id = 'AKIDatt8wGamPiolphD0VdqUWupR35oEYeOk';
    private $_secret_key = 'JKxyKHjeGVr75CHTYULPfj0d3mu36KeD';
    private $_api_url = array(
                                'detectface' => 'http://api.youtu.qq.com/youtu/api/detectface',//人脸检测
                                'faceshape' => 'http://api.youtu.qq.com/youtu/api/faceshape',//人脸定位
                                'facecompare' => 'http://api.youtu.qq.com/youtu/api/facecompare',//人脸对比
                                'faceverify' => 'http://api.youtu.qq.com/youtu/api/faceverify',//人脸验证
                                'faceidentify' => 'http://api.youtu.qq.com/youtu/api/faceidentify',//人脸识别
                                'newperson' => 'http://api.youtu.qq.com/youtu/api/newperson',//个体创建
                                'delperson' => 'http://api.youtu.qq.com/youtu/api/delperson',//删除个体
                                'addface' => 'http://api.youtu.qq.com/youtu/api/addface',//增加人脸
                                'delface' => 'http://api.youtu.qq.com/youtu/api/delface',//删除人脸
                                'setinfo' => 'http://api.youtu.qq.com/youtu/api/setinfo',//设置信息
                                'getinfo' => 'http://api.youtu.qq.com/youtu/api/getinfo',//获取信息
                                'getgroupids' => 'http://api.youtu.qq.com/youtu/api/getgroupids',//获取组列表
                                'getpersonids' => 'http://api.youtu.qq.com/youtu/api/getpersonids',//获取人列表
                                'getfaceids' => 'http://api.youtu.qq.com/youtu/api/getfaceids',//获取人脸列表
                                'getfaceinfo' => 'http://api.youtu.qq.com/youtu/api/getfaceinfo',//获取人脸信息
                                'fuzzydetect' => 'http://api.youtu.qq.com/youtu/imageapi/fuzzydetect',//模糊图片检测
                                'fooddetect' => 'http://api.youtu.qq.com/youtu/imageapi/fooddetect',//美食图片识别
                                'imagetag' => 'http://api.youtu.qq.com/youtu/imageapi/imagetag',//图像标签识别
                                'imageporn' => 'http://api.youtu.qq.com/youtu/imageapi/imageporn',//色情图像检测
                            );
    
    /**
     * @todo 入口
     * @param string $type 接口类型
     * @param array $params
     * @param array $header 请求头
     * @return array
     * @see http://open.youtu.qq.com/welcome/developer
     */
    public function request($type, $params, $header=array()){
        if(!in_array($type, array_keys($this->_api_url))){
            log_message('error', $type .' api not supported');
            return false;
        }
        
        $data = array();
        $data['url'] = $this->_api_url[$type];
        $data['data'] = $params;
        $data['header'] = $header;
        $data['method'] = 'post';
        
        return http($data);
    }
}