<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

$error = array(
    'express_lack_of_com_error' => array('errcode'=>1,'errmsg'=>'请选择快递公司名称'),
    'express_lack_of_nu_error' => array('errcode'=>2,'errmsg'=>'请输入快递单号'),
    'music_lack_of_hash_error' => array('errcode'=>4,'errmsg'=>'缺少音乐hash'),
    'music_lack_of_name_error' => array('errcode'=>5,'errmsg'=>'请输入音乐信息'),
    'ssq_got_price_before' => array('errcode'=>6,'errmsg'=>'此号码已中大奖'),
    'stock_lack_of_stockid_error' => array('errcode'=>7,'errmsg'=>'请输入股票diamante'),
    'weather_lack_of_cityid_error' => array('errcode'=>8,'errmsg'=>'请选择城市'),
    'get_lottery_no_result_found' => array('errcode'=>9,'errmsg'=>'未查询到开奖信息'),
    'request_not_allowed' => array('errcode'=>10,'errmsg'=>'请求非法'),
);