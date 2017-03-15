<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class NewsController extends Yaf_Controller_Abstract{
    
    public function girlAction(){
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        
        $data = array();
        $request->getQuery('keyword') && $data['word'] = $request->getQuery('keyword');
        
        $data['page'] = $request->getQuery('page') ? $request->getQuery('page') : 1;
        
        $data['rand'] = 1;
        $data['num'] = 25;
        
        $baiduModel = new BaiduModel();
        $rt = $baiduModel->getGirls($data);

        $data = array();
        $data['rtn'] = 0;
        $data['msg'] = $msg_news_list = $msg_news_banner = '';
        
        $msgformat = get_var_from_conf('msgformat');
        if(!isset($rt['code']) || $rt['code']!==200){
            $msg_news_banner = sprintf($msgformat['msg_news_banner'], 'javascript:void(0)', '/static/public/images/app/1.jpg', 'http://placeholdit.imgix.net/~text?txtsize=18&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=403&h=268&txttrack=0', '美女表示不约');
            $data['msg'] = sprintf($msgformat['msg_news_web'], $msg_news_banner, '');
        }else{
            foreach($rt['newslist'] as $_k=>$_v){
                if($_k%5 === 0){
                    $msg_news_banner = sprintf($msgformat['msg_news_banner'], $_v['url'], $_v['picUrl'], 'http://placeholdit.imgix.net/~text?txtsize=18&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=403&h=268&txttrack=0', $_v['title']);
                }else{
                    $msg_news_list .= sprintf($msgformat['msg_news_list'], $_v['url'], $_v['title'], $_v['picUrl'], 'http://placeholdit.imgix.net/~text?txtsize=6&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=43&h=43&txttrack=0');
                }
                
                if(($_k+1)%5 === 0){
                    $data['msg'] .= sprintf($msgformat['msg_news_web'], $msg_news_banner, $msg_news_list);
                    $msg_news_list = '';
                }
            }
            
        }
        
        $data['title'] = 'WeApp-美图';
        $data['class'] = 'news';
        $this->getView()->assign('data', $data);
    }
    
    public function hotAction(){
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        
        $data = array();
        $request->getQuery('keyword') && $data['word'] = $request->getQuery('keyword');
        
        $data['page'] = $request->getQuery('page') ? $request->getQuery('page') : 1;
        !isset($data['word']) && $data['page'] = rand(1,999);
        
        $data['rand'] = 1;
        $data['num'] = 25;
        
        $baiduModel = new BaiduModel();
        $rt = $baiduModel->getNews($data);

        
        $data = array();
        $data['rtn'] = 0;
        $data['msg'] = $msg_news_list = $msg_news_banner = '';
        
        $msgformat = get_var_from_conf('msgformat');
        if(!isset($rt['code']) || $rt['code']!==200){
            $msg_news_banner = sprintf($msgformat['msg_news_banner'], 'javascript:void(0)', '/static/public/images/app/1.jpg', 'http://placeholdit.imgix.net/~text?txtsize=18&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=403&h=268&txttrack=0', '老夫夜关天象，今日并无大事发生');
            $data['msg'] = sprintf($msgformat['msg_news_web'], $msg_news_banner, '');
        }else{
            foreach($rt['newslist'] as $_k=>$_v){
                if($_k%5 === 0){
                    $msg_news_banner = sprintf($msgformat['msg_news_banner'], $_v['url'], $_v['picUrl'], 'http://placeholdit.imgix.net/~text?txtsize=18&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=403&h=268&txttrack=0', $_v['title']);
                }else{
                    $msg_news_list .= sprintf($msgformat['msg_news_list'], $_v['url'], $_v['title'], $_v['picUrl'], 'http://placeholdit.imgix.net/~text?txtsize=6&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=43&h=43&txttrack=0');
                }
                
                if(($_k+1)%5 === 0){
                    $data['msg'] .= sprintf($msgformat['msg_news_web'], $msg_news_banner, $msg_news_list);
                    $msg_news_list = '';
                }
            }
            
        }
        
        $data['title'] = 'WeApp-热搜';
        $data['class'] = 'news';
        $this->getView()->assign('data', $data);
    }
    
    public function socialAction(){
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        
        $data = array();
        $request->getQuery('keyword') && $data['word'] = $request->getQuery('keyword');
        
        $data['page'] = $request->getQuery('page') ? $request->getQuery('page') : 1;
        
        $data['rand'] = 1;
        $data['num'] = 25;
        
        $baiduModel = new BaiduModel();
        $rt = $baiduModel->getSocials($data);

        
        $data = array();
        $data['rtn'] = 0;
        $data['msg'] = $msg_news_list = $msg_news_banner = '';
        
        $msgformat = get_var_from_conf('msgformat');
        if(!isset($rt['code']) || $rt['code']!==200){
            $msg_news_banner = sprintf($msgformat['msg_news_banner'], 'javascript:void(0)', '/static/public/images/app/1.jpg', 'http://placeholdit.imgix.net/~text?txtsize=18&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=403&h=268&txttrack=0', '资讯正在赶来的路上');
            $data['msg'] = sprintf($msgformat['msg_news_web'], $msg_news_banner, '');
        }else{
            foreach($rt['newslist'] as $_k=>$_v){
                if($_k%5 === 0){
                    $msg_news_banner = sprintf($msgformat['msg_news_banner'], $_v['url'], $_v['picUrl'], 'http://placeholdit.imgix.net/~text?txtsize=18&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=403&h=268&txttrack=0', $_v['title']);
                }else{
                    $msg_news_list .= sprintf($msgformat['msg_news_list'], $_v['url'], $_v['title'], $_v['picUrl'], 'http://placeholdit.imgix.net/~text?txtsize=6&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=43&h=43&txttrack=0');
                }
                
                if(($_k+1)%5 === 0){
                    $data['msg'] .= sprintf($msgformat['msg_news_web'], $msg_news_banner, $msg_news_list);
                    $msg_news_list = '';
                }
            }
            
        }
        
        $data['title'] = 'WeApp-社会资讯';
        $data['class'] = 'news';
        $this->getView()->assign('data', $data);
        
    }
    
    public function wxhotAction(){
        $request = new Yaf_Request_Http();
        $response = new Yaf_Response_Http();
        
        $data = array();
        $request->getQuery('keyword') && $data['word'] = $request->getQuery('keyword');
        
        $data['page'] = $request->getQuery('page') ? $request->getQuery('page') : 1;
        
        $data['rand'] = 1;
        $data['num'] = 25;
        
        $baiduModel = new BaiduModel();
        $rt = $baiduModel->getNews($data);

        
        $data = array();
        $data['rtn'] = 0;
        $data['msg'] = $msg_news_list = $msg_news_banner = '';
        
        $msgformat = get_var_from_conf('msgformat');
        if(!isset($rt['code']) || $rt['code']!==200){
            $msg_news_banner = sprintf($msgformat['msg_news_banner'], 'javascript:void(0)', '/static/public/images/app/1.jpg', 'http://placeholdit.imgix.net/~text?txtsize=18&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=403&h=268&txttrack=0', '新闻飞走了');
            $data['msg'] = sprintf($msgformat['msg_news_web'], $msg_news_banner, '');
        }else{
            foreach($rt['newslist'] as $_k=>$_v){
                if($_k%5 === 0){
                    $msg_news_banner = sprintf($msgformat['msg_news_banner'], $_v['url'], $_v['picUrl'], 'http://placeholdit.imgix.net/~text?txtsize=18&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=403&h=268&txttrack=0', $_v['title']);
                }else{
                    $msg_news_list .= sprintf($msgformat['msg_news_list'], $_v['url'], $_v['title'], $_v['picUrl'], 'http://placeholdit.imgix.net/~text?txtsize=6&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=43&h=43&txttrack=0');
                }
                
                if(($_k+1)%5 === 0){
                    $data['msg'] .= sprintf($msgformat['msg_news_web'], $msg_news_banner, $msg_news_list);
                    $msg_news_list = '';
                }
            }
            
        }
        
        $data['title'] = 'WeApp-微信热门';
        $data['class'] = 'news';
        $this->getView()->assign('data', $data);
    }
}