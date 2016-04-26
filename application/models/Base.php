<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class BaseModel {
    public $_send_format = array(
            'text' => array('touser'=>'', 'msgtype'=>'text', 'text'=>array('content'=>'')),
            'image' => array('touser'=>'', 'msgtype'=>'image', 'image'=>array('media_id'=>'')),
            'voice' => array('touser'=>'', 'msgtype'=>'voice', 'voice'=>array('media_id'=>'')),
            'video' => array('touser'=>'', 'msgtype'=>'video', 'video'=>array('media_id'=>'', 'thumb_media_id'=>'', 'title'=>'', 'description'=>'')),
            'music' => array('touser'=>'', 'msgtype'=>'music', 'music'=>array('title'=>'', 'description'=>'', 'musicurl'=>'', 'hqmusicurl'=>'', 'thumb_media_id'=>'')),
            'news' => array('touser'=>'', 'msgtype'=>'news', 'articles'=>array(array('title'=>'', 'description'=>'', 'url'=>'', 'picurl'=>''))),
        );
    
    public function http($args = array()){
        $ch = curl_init();

        $args = array_merge(array(
            'header'=> array(),
            'data' => array(),
            'method' => 'get',
        ) ,$args);
        if(strtolower($args['method']) === 'post'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch ,CURLOPT_POSTFIELDS, is_array($args['data']) ? http_build_query($args['data']) : $args['data']);
        }else{
            if(is_array($args['data'])){
                 $data = array();
                 foreach ($args['data'] as $key => $value) {
                     $data[] = urlencode($key).'='.urlencode($value);
                 }
                 $data = implode('&', $data);
            }else{
                $data = $args['data'];
            }
            $args['url'] .=(strpos($args['url'] ,'?')==false?'?':'&').$data;
            $args['url'] = trim($args['url'], '&');
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在 
        curl_setopt($ch, CURLOPT_URL, $args['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        !empty($args['header']) && curl_setopt($ch ,CURLOPT_HTTPHEADER, $args['header']);
        $result = curl_exec($ch);

        if($result === false){
            return array(
                'rtn' => 1,
                'errmsg' => 'request interface fatal'
            );
        }

        curl_close($ch);

        $return = json_decode($result ,true);

        if(!$return){
            $return = array();
            $return['rtn'] = 2;
            $return['errmsg'] = '返回数据非json格式';
            return $return;
        }

        return $return;
   }
}