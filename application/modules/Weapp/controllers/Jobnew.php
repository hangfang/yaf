<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class JobnewController extends Yaf_Controller_Abstract {
    public function dltAction(){
        $request = new Yaf_Request_Http();
        $expect = $request->getQuery('eventname', '');
        
        $url = $expect ? 'http://www.ticaidgg.com/wx_interface/tcd/getDetailData.do?eventname='.$expect : 'http://www.ticaidgg.com/wx_interface/tcd/getDetailData.do';
        $html = file_get_contents($url);
        $start = trim(strpos($html, '('));
        $info = json_decode(substr($html, $start+1, -2), true);
        
        
        if(!$info || !isset($info['detail']) || !isset($info['detail']['EventName']{4})){
            echo date('Y-m-d H:i:s'). ' dlt '. $expect .' not sell ' . "\n";
            return false;
        }
        $info = $info['detail'];
        
        $jobLotteryModel = new JobLotteryModel();
        if($jobLotteryModel->isLoaded($info['EventName'], 'dlt')){
            echo date('Y-m-d H:i:s'). ' dlt '. $info['EventName'] . ' exists' . "\n";
            return false;
        }
        
        $pride = array();
        $draws = explode('-', $info['DrawContent']);
        list($pride['a'], $pride['b'], $pride['c'], $pride['d'], $pride['e']) = explode(' ', $draws[0]);
        list($pride['f'], $pride['g']) = explode(' ', $draws[1]);
        $pride['first'] = $info['DrawDetailss'][1];
        $pride['first_num'] = $info['DrawDetailss'][0];
        
        $pride['first_add'] = $info['DrawDetailss'][3];
        $pride['first_add_num'] = $info['DrawDetailss'][2];
        
        $pride['second'] = $info['DrawDetailss'][5];
        $pride['second_num'] = $info['DrawDetailss'][4];
        
        $pride['second_add'] = $info['DrawDetailss'][7];
        $pride['second_add_num'] = $info['DrawDetailss'][6];
        
        $pride['third'] = $info['DrawDetailss'][9];
        $pride['third_num'] = $info['DrawDetailss'][8];
        
        $pride['third_add'] = $info['DrawDetailss'][11];
        $pride['third_add_num'] = $info['DrawDetailss'][10];
        
        $pride['forth'] = $info['DrawDetailss'][13];
        $pride['forth_num'] = $info['DrawDetailss'][12];
        
        $pride['forth_add'] = $info['DrawDetailss'][15];
        $pride['forth_add_num'] = $info['DrawDetailss'][14];
        
        $pride['fivth'] = $info['DrawDetailss'][17];
        $pride['fivth_num'] = $info['DrawDetailss'][16];
        
        $pride['fivth_add'] = $info['DrawDetailss'][19];
        $pride['fivth_add_num'] = $info['DrawDetailss'][18];
        
        $pride['sixth'] = $info['DrawDetailss'][21];
        $pride['sixth_num'] = $info['DrawDetailss'][20];
        
        $pride['sixth_add'] = $info['DrawDetailss'][23];
        $pride['sixth_add_num'] = $info['DrawDetailss'][22];
        
        $pride['insert_time'] = str_replace(array('年', '月', '日'), array('-', '-', ''), $info['DrawDate'].":00");
        $pride['expect'] = $info['EventName'];
        
        if(!$jobLotteryModel->load($pride, 'dlt')){
            echo date('Y-m-d H:i:s'). ' dlt '. $pride['expect'] . ' load error' . "\n";
            return false;
        }
        
        echo date('Y-m-d H:i:s'). ' dlt '. $pride['expect'] . ' load ok' . "\n";
        return false;
    }
    
    public function ssqAction(){
        $url = 'http://www.zhcw.com/ssq/kjgg/';
        
        include BASE_PATH .'/application/library/SimpleHtmlDomNode.php';
        $html = file_get_html($url);
        $nlink = $html->find('.Nlink', 0)->find('a', 0);
        $uri = $nlink->attr['href'];
        preg_match_all('/(\d+)/', $nlink->innertext, $match);
        $expect = $match[1][0];
        $html->clear();
        
        if(!isset($expect{6})){
            echo date('Y-m-d H:i:s'). ' ssq '. 'get expect error: '. print_r($expect) . "\n";
            return false;
        }
        $jobLotteryModel = new JobLotteryModel();
        if($jobLotteryModel->isLoaded($expect, 'ssq')){
            echo date('Y-m-d H:i:s'). ' ssq '. $expect . ' exists' . "\n";
            return false;
        }

        $html = file_get_html('http://www.zhcw.com'. $uri);
        //$info = json_decode($html->find('#currentScript', 0)->innertext, true);
        $info = $html->find('#currentScript', 0)->innertext;
        $info = json_decode(html_entity_decode($info), true)[0];
        $html->clear();
        
        $pride = array();
        list($pride['a'], $pride['b'], $pride['c'], $pride['d'], $pride['e'], $pride['f']) = explode(' ', $info['KJ_Z_NUM']);
        $pride['g'] = $info['KJ_T_NUM'];
        $pride['first'] = $info['ONE_J'];
        $pride['first_num'] = $info['ONE_Z'];
        
        $pride['second'] = $info['TWO_J'];
        $pride['second_num'] = $info['TWO_Z'];
        
        $pride['third'] = $info['THREE_J'];
        $pride['third_num'] = $info['THREE_Z'];
        
        $pride['forth'] = $info['FOUR_J'];
        $pride['forth_num'] = $info['FOUR_Z'];
        
        $pride['fivth'] = $info['FIVE_J'];
        $pride['fivth_num'] = $info['FIVE_Z'];
        
        $pride['sixth'] = $info['SIX_J'];
        $pride['sixth_num'] = $info['SIX_Z'];
        
        $pride['remain'] = $info['JC_MONEY'];
        $pride['sell'] = $info['TZ_MONEY'];
        $pride['insert_time'] = date('Y-m-d H:i:s', strtotime($info['KJ_DATE']));
        $pride['expect'] = $info['KJ_ISSUE'];
        
        if(!$jobLotteryModel->load($pride, 'ssq')){
            echo date('Y-m-d H:i:s'). ' ssq '. $expect . ' load error' . "\n";
            return false;
        }
        
        echo date('Y-m-d H:i:s'). ' ssq '. $expect . ' load ok' . "\n";
        return false;
    }
}