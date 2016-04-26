<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

define('BASE_URL', 'http://rbmax.com');

define('WX_CGI_ADDR', 'https://api.weixin.qq.com/cgi-bin');
define('WX_APP_ID', 'wxd7739816540194cb');
define('WX_APP_SECRET', '82c0a8cdfc3daf060ceccd96016d43ed');

define('WX_ADMIN_OPENID', 'ohwjvw1QPmm0YLy3yKhjGYg4qS_g');

define('WX_TOKEN', 'fanghang2016ieg926400');
define('WX_ENCODING_AES_KEY', 'TkFBgCr7fcI7EPqCd0lPV48vaV5c49dkE0vhbXHHXLH');

define('WX_HK_ACCOUNT', 'WangLin-ling');
define('WX_JSAPI_DEBUG', 'false');

/*快递100*/
define('KUAIDI_100_APP_ID', 'b653f3a448ef4540');
define('KUAIDI_100_API_URL', 'http://api.kuaidi100.com/api?id='. KUAIDI_100_APP_ID .'&com=%s&nu=%s&show=%s&muti=%s&order=%s');

/*新浪ip查询*/
define('SINA_IP_LOOKUP_API_URL', 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=%s');

/*天气查询*/
define('BAIDU_WEATHER_API_URL', 'http://apis.baidu.com/apistore/weatherservice/cityid?cityid=%s');
/*股票查询*/
define('BAIDU_STOCK_API_URL', 'http://apis.baidu.com/apistore/stockservice/stock?stockid=%s&list=1');
define('BAIDU_API_KEY', 'babdf9f16f7b77baaa806eb302210ce9');

/*快递鸟*/
define('KD_NIAO_APP_ID', '1256662');
define('KD_NIAO_APP_KEY', '998e72f8-d8f2-4b56-9b55-4c3510d23275');
define('KD_NIAO_API_URL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');

/*腾讯地图*/
define('TENCENT_MAP_APP_KEY', 'J7CBZ-YV43X-PVS4E-ZGYVP-KF2T3-A3BQZ');
define('TENCENT_MAP_APP_URL', 'http://apis.map.qq.com/ws');

/*百度音乐*/
define('BAIDU_MUSIC_SEARCH_API_URL', 'http://apis.baidu.com/geekery/music/query');
define('BAIDU_MUSIC_PLAYINFO_API_URL', 'http://apis.baidu.com/geekery/music/playinfo');
define('BAIDU_MUSIC_SINGER_API_URL', 'http://apis.baidu.com/geekery/music/singer');
define('BAIDU_MUSIC_KRC_API_URL', 'http://apis.baidu.com/geekery/music/krc');

/*网易音乐*/
define('MUSIC_163_SEARCH_API_URL', 'http://music.163.com/api/search/suggest/web');
define('MUSIC_163_ARTIST_API_URL', 'http://music.163.com/api/artist');
define('MUSIC_163_ALBUM_API_URL', 'http://music.163.com/api/album');
define('MUSIC_163_SONG_DETAIL_API_URL', 'http://music.163.com/api/song/detail');
define('MUSIC_163_MV_DETAIL_API_URL', 'http://music.163.com/api/mv/detail');
define('MUSIC_163_SONG_LYRIC_API_URL', 'http://music.163.com/api/song/lyric');

/*百度美女*/
define('BAIDU_GIRLS_API_URL', 'http://apis.baidu.com/txapi/mvtp/meinv?num=8');

/*百度新闻*/
define('BAIDU_NEWS_API_URL', 'http://apis.baidu.com/txapi/weixin/wxhot');

/*百度社会新闻*/
define('BAIDU_SOCIALS_API_URL', 'http://apis.baidu.com/txapi/social/social');

/*百度彩票开奖*/
define('BAIDU_LOTTERY_API_URL', 'http://apis.baidu.com/apistore/lottery/lotteryquery');

/*百度笑话*/
define('BAIDU_JOKE_API_URL', 'http://apis.baidu.com/hihelpsme/chinajoke/getjokelist');