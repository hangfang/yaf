<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class PositionModel extends BaseModel{

    /**
     * ip查询城市
     */
    public function getPosition(){
        $data = array();
        $data['method'] = 'get';
        $data['url'] = sprintf(SINA_IP_LOOKUP_API_URL, $this->input->ip_address());
        return http($data);
    }
    
    /**
     * 经纬度查询位置
     * @param float $lat 纬度
     * @param float $lng 经度
     */
    public function getLocation($lat, $lng){
        $data = array();
        $data['method'] = 'get';
        $data['url'] = sprintf(TENCENT_MAP_APP_URL.'/geocoder/v1/?location=%s,%s&key=%s&get_poi=1', $lat, $lng, TENCENT_MAP_APP_KEY);
        
        return http($data);
    }

    public function searchAround($lastMsg, $msgXml=array()){
        
        $data = array();
        $data['method'] = 'get';
        $data['url'] = sprintf(TENCENT_MAP_APP_URL.'/place/v1/search?boundary=nearby(%s,%s,100000)&keyword=%s&page_size=5&page_index=1&orderby=_distance&key=%s', $lastMsg['Location_X'], $lastMsg['Location_Y'], $msgXml['Content'], TENCENT_MAP_APP_KEY);
        
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        $msgformat = get_var_from_conf('msgformat');
        if($rt['status'] === 0){
            $around_text = '';
            
            foreach($rt['data'] as $v){
                $tmp = <<<EOF
                    
    名称：{$v['title']}
    地址：{$v['address']}
    电话：{$v['tel']}
    距离：{$v['_distance']}米

EOF;
                $around_text .= $tmp;
            }
            
            $data = $msgformat['send_format']['text'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];
            
            $friendlyDate = new FriendlyDate();
            $data['text']['content'] = sprintf($msgformat['msg_around'], $msgXml['Content'], $around_text, $friendlyDate->timeDiff($lastMsg['CreateTime']));
            return $data;
        }
        
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = $rt['message'];
        return $data;
    }
}