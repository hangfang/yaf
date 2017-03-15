<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

define('BASE_URL', 'http://new.gxq168.net');//当前系统地址
define('NEED_SIGN', true);//是否需要验签
define('URL', 'http://interface.gxq168.net');//后端接口地址
define('NONCE_TIME', 300);//请求5分钟内有效

define('ORDER_STATUS', array(
        0 => 0,//投标申请
        
        1 => 1,//投标成功
        6 => 1,//赎回中
        8 => 1,//赎回失败
        
        2 => 2,//投标失败，订单无效
        
        3 => 3,//订单取消中
        4 => 3,//订单取消失败
        5 => 3,//订单取消成功
        
        7 => 4,//赎回成功
    )
);

define('WITHDRAW_STATUS', array(
        'INIT' => 'INIT',//提现申请
        'CONFIRMING' => 'INIT',//待确认
        
        'ACCEPT' => 'ING',//已受理
        'REMITING' => 'ING',//出款中
        'PAUSE' => 'ING',//暂停
        
        'ACCEPT_FAIL' => 'FAIL',//审核不通过
        'FAIL' => 'FAIL',//提现失败
        
        'SUCCESS' => 'SUCCESS',//提现成功
        
        'BACKROLL_RECHARGE' => 'BACKROLL',//提现退回
    )
);

define('MIN_WITHDRAW_AMOUNT', 100);