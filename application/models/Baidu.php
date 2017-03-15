<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class BaiduModel extends BaseModel{
    
    public function getMusic($param, $msgXml=array()){
        $data = array();
        $data['method'] = 'get';
        $data['header'] = array('apikey: '. BAIDU_API_KEY);
        $data['url'] = sprintf(BAIDU_MUSIC_SEARCH_API_URL);
        $data['data'] = $param;
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        return $rt;
    }
    
    public function getMusicPlayInfo($param, $msgXml=array()){
        $data = array();
        $data['method'] = 'get';
        $data['header'] = array('apikey: '. BAIDU_API_KEY);
        $data['url'] = sprintf(BAIDU_MUSIC_PLAYINFO_API_URL);
        $data['data'] = $param;
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        return $rt;
    }
    
    public function getStock($stockid, $msgXml=array()){
        
        $data = array();
        $data['method'] = 'get';
        $data['header'] = array('apikey: '. BAIDU_API_KEY);
        $data['url'] = sprintf(BAIDU_STOCK_API_URL, $stockid);
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        $msgformat = get_var_from_conf('msgformat');
        if($rt['errNum'] === 0){
                        
            $data = $msgformat['send_format']['text'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];
            
            $stockInfo = $rt['retData']['stockinfo'][0];
            $data['text']['content'] = sprintf($msgformat['msg_stock'], $stockInfo['name'], $stockInfo['code'], $stockInfo['date'], $stockInfo['time'], $stockInfo['OpenningPrice'], $stockInfo['closingPrice'], $stockInfo['currentPrice'], $stockInfo['hPrice'], $stockInfo['lPrice'], $stockInfo['competitivePrice'], $stockInfo['auctionPrice'], number_format($stockInfo['totalNumber']/1000000, 1), number_format($stockInfo['turnover']/100000000, 2), number_format($stockInfo['increase'], 2).'%', $stockInfo['buyOne'], $stockInfo['buyOnePrice'], $stockInfo['buyTwo'], $stockInfo['buyTwoPrice'], $stockInfo['buyThree'], $stockInfo['buyThreePrice'], $stockInfo['buyFour'], $stockInfo['buyFourPrice'], $stockInfo['buyFive'], $stockInfo['buyFivePrice'], $stockInfo['sellOne'], $stockInfo['sellOnePrice'], $stockInfo['sellTwo'], $stockInfo['sellTwoPrice'], $stockInfo['sellThree'], $stockInfo['sellThreePrice'], $stockInfo['sellFour'], $stockInfo['sellFourPrice'], $stockInfo['sellFive'], $stockInfo['sellFivePrice'], $stockInfo['minurl'], $stockInfo['dayurl'], $stockInfo['weekurl'], $stockInfo['monthurl']);
            return $data;
        }
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '糟糕，未查到“'. $stockid .'”';
        return $data;
    }
    
    public function getWeather($cityid, $msgXml=array()){
        $data = array();
        $data['method'] = 'get';
        $data['header'] = array('apikey: '. BAIDU_API_KEY);
        $data['url'] = sprintf(BAIDU_WEATHER_API_URL, $cityid);
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        $msgformat = get_var_from_conf('msgformat');
        if($rt['errNum'] === 0){

            $data = $msgformat['send_format']['text'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];

            $weather = $rt['retData'];

            $data['text']['content'] = sprintf($msgformat['msg_weather'], $weather['city'], $weather['date'], $weather['time'], $weather['weather'], $weather['temp'], $weather['h_tmp'], $weather['l_tmp'], $weather['WD'], $weather['WS'], $weather['sunrise'], $weather['sunset']);
            return $data;
        }
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '咦，你很关心“'. $contents[0] .'”地区？';
        return $data;
    }
    
    
    public function getGirls($param, $msgXml=array()){
        $data = array();
        $data['method'] = 'get';
        $data['header'] = array('apikey: '. BAIDU_API_KEY);
        $data['url'] = sprintf(BAIDU_GIRLS_API_URL);
        $data['data'] = $param;
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        $msgformat = get_var_from_conf('msgformat');
        if($rt['code'] === 200){

            $data = $msgformat['send_format']['news'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];
            
            $girls = array();
            foreach($rt['newslist'] as $_v){
                $tmp = array();
                $tmp['title'] = $_v['title'];
                $tmp['description'] = $_v['description'];
                $tmp['picurl'] = $_v['picUrl'];
                $tmp['url'] = $_v['url'];
                $girls[] = $tmp;
            }
            
            $data['articles'] = $girls;
            return $data;
        }
        
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '悲剧，美女都表示不约...';
        return $data;
    }
    
    public function getNews($param, $msgXml=array()){
        $data = array();
        $data['method'] = 'get';
        $data['header'] = array('apikey: '. BAIDU_API_KEY);
        $data['url'] = BAIDU_NEWS_API_URL;
        $data['data'] = $param;
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        $msgformat = get_var_from_conf('msgformat');
        if($rt['code'] === 200){

            $data = $msgformat['send_format']['news'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];

            $news = array();
            foreach($rt['newslist'] as $_k=>$_v){
                $tmp = array();
                $tmp['title'] = $_v['title'];
                $tmp['description'] = $_v['description'];
                $tmp['picurl'] = $_v['picUrl'];
                $tmp['url'] = $_v['url'];
                $news[] = $tmp;
            }
            $data['articles'] = $news;
            return $data;
        }
        
        return false;
    }
    
    public function getSocials($param, $msgXml=array()){
        $data = array();
        $data['method'] = 'get';
        $data['header'] = array('apikey: '. BAIDU_API_KEY);
        $data['url'] = BAIDU_SOCIALS_API_URL;
        $data['data'] = $param;
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        $msgformat = get_var_from_conf('msgformat');
        if($rt['code'] === 200){

            $data = $msgformat['send_format']['news'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];

            $news = array();
            foreach($rt['newslist'] as $_k=>$_v){
                $tmp = array();
                $tmp['title'] = $_v['title'];
                $tmp['description'] = $_v['description'];
                $tmp['picurl'] = $_v['picUrl'];
                $tmp['url'] = $_v['url'];
                $news[] = $tmp;
            }
            $data['articles'] = $news;
            return $data;
        }
        
        return false;
    }
    
    /**
     * 查询彩票开奖信息
     * @param array $data
     * @param array $msgXml
     * @return string
     */
    public function getLottery($param, $msgXml=array()){
        $data = array();
        $data['method'] = 'get';
        $data['header'] = array('apikey: '. BAIDU_API_KEY);
        $data['data'] = $param;
        $data['url'] = BAIDU_LOTTERY_API_URL;
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        $msgformat = get_var_from_conf('msgformat');
        if($rt['errNum'] === 0){
            $tmp = $rt['retData']['data'][0];
            
            $data = $msgformat['send_format']['text'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];
            
            $lottery = get_var_from_conf('lottery');
            $lottery = array_flip($lottery);
            $data['text']['content'] = sprintf($msgformat['msg_lottery'], $lottery[$rt['retData']['lotteryCode']], $tmp['expect'], $tmp['openCode'], '');
            return $data;
        }
        
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '别着急，还未开奖...';
        return $data;
    }
    
    public function getJoke($param, $msgXml=array()){
        
        $data = array();
        $data['method'] = 'post';
        $data['header'] = array('apikey: '. BAIDU_API_KEY);
        $data['data'] = $param;
        $data['url'] = BAIDU_JOKE_API_URL;
        $rt = http($data);
        
        if(empty($msgXml)){
            return $rt;
        }
        
        $msgformat = get_var_from_conf('msgformat');
        if($rt['res_code'] === 0){
            $tmp = $rt['res_body']['JokeList'][rand(0,19)];
            
            $data = $msgformat['send_format']['text'];
            $data['touser'] = $msgXml['FromUserName'];
            $data['fromuser'] = $msgXml['ToUserName'];
            
            $data['text']['content'] = sprintf($msgformat['msg_joke'], $tmp['JokeTitle'], $tmp['JokeContent']);
            return $data;
        }
        
        $data = $msgformat['send_format']['text'];
        $data['touser'] = $msgXml['FromUserName'];
        $data['fromuser'] = $msgXml['ToUserName'];
        $data['text']['content'] = '准备好，段子即将开讲...';
        return $data;
    }
}