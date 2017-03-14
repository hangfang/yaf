<?php

//QQ登录配置
$qq = array(
    '15000103' => array(
        'UIN' => '210074820',
        'PASSWORD' => '', 
        'U1' => 'http://e.qq.com/index.shtml',
        'PRE_LOGIN_URL' => 'http://xui.ptlogin2.qq.com/cgi-bin/xlogin?appid=15000103&s_url='. urlencode('http://e.qq.com/index.shtml') .'&style=20&border_radius=1&target=top&maskOpacity=40&',
        'CHECK_LOGIN_URL' => 'http://check.ptlogin2.qq.com/check',
        'LOGIN_URL' => 'https://y.qq.com/portal/profile.html',
        'API_ADDR' => 'http://e.qq.com/ec/api.php?',//正式环境
        'LONG_MAX' => 236257279,//最大ip
        'LONG_MIN' => 236191744,//最小ip
    ),
);