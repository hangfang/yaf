<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

$msgformat = array();
$msgformat['msg_around'] = <<<EOF
搜索周边：%s
%s

注：以%s的位置计算
EOF;

$msgformat['msg_weather'] = <<<EOF
天气(%s)
    日期：%s
    发布时间：%s
    天气：%s
    当前气温：%s℃
    最高：%s℃
    最低：%s℃
    风向：%s
    风力：%s
    日出时间：%s
    日落时间：%s        
EOF;
    
$msgformat['msg_stock'] = <<<EOF
%s:
    股票代码: %s
    日期: %s
    时间: %s
    开盘价: %s元
    收盘价: %s元
    当前价格: %s元
    最高价: %s元
    最低价: %s元
    买一报价: %s元
    卖一报价: %s元
    成交量: %s万手
    成交额: %s亿
    涨幅: %s
    买一: %s股  %s元
    买二: %s股  %s元
    买三: %s股  %s元
    买四: %s股  %s元
    买五: %s股  %s元
    卖一: %s股  %s元
    卖二: %s股  %s元
    卖三: %s股  %s元
    卖四: %s股  %s元
    卖五: %s股  %s元
    分时图: %s
    日K线: %s
    周K线: %s
    月K线: %s
    
仅供参考，非投资依据。
EOF;
    
$msgformat['msg_lottery'] = <<<EOF
彩种：%s
期号：%s
号码：%s
%s
EOF;
    
$msgformat['msg_joke'] = <<<EOF
标题：%s
            
%s
EOF;

$msgformat['msg_unrecognized'] = <<<EOF
咦，您是说“%s”吗？
可小i尚小，未能处理ㄒoㄒ

1、发送如“北京”<a href="%s">查询</a>天气
2、发送如“申通，xx”<a href="%s">查询</a>物流
3、发送如“600000”<a href="%s">查询</a>股票数据
4、发送如“美容”等，搜索周边
5、最新推出<a href="%s/weapp/map/index">地图服务</a>
6、更多隐藏功能由您发掘…
        
感谢关注
EOF;
        
$msgformat['msg_to_large'] = <<<EOF
额，信息量太大
请说重点(*≧▽≦*)

1、发送如“北京”<a href="%s">查询</a>天气
2、发送如“申通，xx”<a href="%s">查询</a>物流
3、发送如“600000”<a href="%s">查询</a>股票数据
4、发送如“美容”等，搜索周边
5、最新推出<a href="%s/weapp/map/index">地图服务</a>
6、更多隐藏功能由您发掘…
        
感谢关注
EOF;

$msgformat['msg_welcome_back'] = <<<EOF
热烈欢迎老伙伴回归！

1、发送如“北京”<a href="%s">查询</a>天气
2、发送如“申通，xx”<a href="%s">查询</a>物流
3、发送如“600000”<a href="%s">查询</a>股票数据
4、发送如“美容”等，搜索周边
5、最新推出<a href="%s/weapp/map/index">地图服务</a>
6、更多隐藏功能由您发掘…
        
感谢关注
EOF;

$msgformat['msg_welcome_newbeing'] = <<<EOF
撒花欢迎新朋友到来！

1、发送如“北京”<a href="%s">查询</a>天气
2、发送如“申通，xx”<a href="%s">查询</a>物流
3、发送如“600000”<a href="%s">查询</a>股票数据
4、发送如“美容”等，搜索周边
5、最新推出<a href="%s/weapp/map/index">地图服务</a>
6、更多隐藏功能由您发掘…
        
感谢关注
EOF;
       
$msgformat['msg_position'] = <<<EOF
OK，我记住了
您在%s！
试试搜索周边？如酒店、美食...
EOF;
    
$msgformat['msg_position_expired'] = <<<EOF
您的位置信息已很久远
于[%s]定位
为精确搜索周边，请重新发送位置
EOF;

$msgformat['msg_kuaidi'] = <<<EOF
公司名称：%s
快递单号：%s
物流信息：%s
EOF;

$msgformat['text_format'] = <<<EOF
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>
EOF;
    
$msgformat['image_format'] = <<<EOF
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA[%s]]></MediaId>
</Image>
</xml>
EOF;
    
$msgformat['voice_format'] = <<<EOF
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
<Voice>
<MediaId><![CDATA[%s]]></MediaId>
</Voice>
</xml>
EOF;

$msgformat['video_format'] = <<<EOF
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
<Video>
<MediaId><![CDATA[%s]]></MediaId>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
</Video> 
</xml>
EOF;

$msgformat['music_format'] = <<<EOF
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
<Music>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<MusicUrl><![CDATA[%s]]></MusicUrl>
<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
</Music>
</xml>
EOF;
    
$msgformat['news_format'] = <<<EOF
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>%s</Articles>
</xml> 
EOF;
    
$msgformat['send_format'] = array(
    'text' => array('touser'=>'', 'msgtype'=>'text', 'text'=>array('content'=>'')),
    'image' => array('touser'=>'', 'msgtype'=>'image', 'image'=>array('media_id'=>'')),
    'voice' => array('touser'=>'', 'msgtype'=>'voice', 'voice'=>array('media_id'=>'')),
    'video' => array('touser'=>'', 'msgtype'=>'video', 'video'=>array('media_id'=>'', 'thumb_media_id'=>'', 'title'=>'', 'description'=>'')),
    'music' => array('touser'=>'', 'msgtype'=>'music', 'music'=>array('title'=>'', 'description'=>'', 'musicurl'=>'', 'hqmusicurl'=>'', 'thumb_media_id'=>'')),
    'news' => array('touser'=>'', 'msgtype'=>'news', 'articles'=>array(array('title'=>'', 'description'=>'', 'url'=>'', 'picurl'=>''))),
);

/**
*  Content     文本消息内容
*  CreateTime	消息创建时间 （整型）
*  Description 消息描述
*  Format      语音格式，如amr，speex等
*  FromUserName发送方帐号（一个OpenID）
*  Label       地理位置信息
*  Location_X	地理位置维度
*  Location_Y	地理位置经度
*  MediaId     视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
*  MsgId       消息id，64位整型
*  MsgType     消息类型: 文本、图片、语音、视频、小视频、位置、链接 
*  PicUrl      图片链接
*  Recognition 语音识别结果，UTF8编码
*  Scale       地图缩放大小
*  ThumbMediaId视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
*  Title       消息标题
*  ToUserName	开发者微信号
* @var array
*/
$msgformat['receive_format'] = array(
    'text' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Content', 'MsgId'),
    'image' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'PicUrl', 'MediaId', 'MsgId'),
    'voice' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'MediaId', 'Format', 'MsgId', 'Recognition'),
    'video' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'MediaId', 'ThumbMediaId', 'MsgId'),
    'shortvideo' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'MediaId', 'ThumbMediaId', 'MsgId'),
    'location' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Location_X', 'Location_Y', 'Scale', 'Label', 'MsgId'),
    'link' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Title', 'Description', 'Url', 'MsgId'),
    'event' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Event', 'EventKey', 'Latitude', 'Longitude', 'Precision', 'Ticket', 'MsgId'),
);

$msgformat['msg_lottery_web'] = <<<EOF
<p class="weui_media_desc">彩种：%s</p>
<p class="weui_media_desc">期号：%s</p>
<p class="weui_media_desc">时间：%s</p>
<p class="weui_media_desc">号码：%s</p>
%s
EOF;
    
$msgformat['msg_lottery_extra'] = <<<EOF
<p class="weui_media_desc">销量：%s</p>
<p class="weui_media_desc">奖池：%s</p>
%s
EOF;
    
$msgformat['ssq_pride'] = <<<EOF
<p class="weui_media_desc">一等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">二等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">三等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">四等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">五等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">六等奖：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;
    
$msgformat['dlt_pride'] = <<<EOF
<p class="weui_media_desc">一等奖追加：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">一&nbsp;&nbsp;&nbsp;等&nbsp;&nbsp;&nbsp;奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">二等奖追加：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">二&nbsp;&nbsp;&nbsp;等&nbsp;&nbsp;&nbsp;奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">三等奖追加：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">三&nbsp;&nbsp;&nbsp;等&nbsp;&nbsp;&nbsp;奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">四等奖追加：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">四&nbsp;&nbsp;&nbsp;等&nbsp;&nbsp;&nbsp;奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">五等奖追加：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">五&nbsp;&nbsp;&nbsp;等&nbsp;&nbsp;&nbsp;奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">六&nbsp;&nbsp;&nbsp;等&nbsp;&nbsp;&nbsp;奖：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;
    
$msgformat['fc3d_pride'] = <<<EOF
<p class="weui_media_desc">直选：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">%s：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;
        
$msgformat['pl3_pride'] = <<<EOF
<p class="weui_media_desc">直选：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">%s：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;
    
$msgformat['pl5_pride'] = <<<EOF
<p class="weui_media_desc">直选：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;

$msgformat['qxc_pride'] = <<<EOF
<p class="weui_media_desc">一等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">二等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">三等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">四等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">五等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">六等奖：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;

$msgformat['msg_stock_web'] = <<<EOF
<p class="weui_media_desc blue">%s</p>
<p class="weui_media_desc">股票代码: %s</p>
<p class="weui_media_desc">日期: %s</p>
<p class="weui_media_desc">时间: %s</p>
<p class="weui_media_desc">开盘价: %s元</p>
<p class="weui_media_desc">收盘价: %s元</p>
<p class="weui_media_desc">当前价格: %s元</p>
<p class="weui_media_desc">最高价: %s元</p>
<p class="weui_media_desc">最低价: %s元</p>
<p class="weui_media_desc">买一报价: %s元</p>
<p class="weui_media_desc">卖一报价: %s元</p>
<p class="weui_media_desc">成交量: %s万手</p>
<p class="weui_media_desc">成交额: %s亿</p>
<p class="weui_media_desc">涨幅: %s</p>
<p class="weui_media_desc">买一: %s手  %s元</p>
<p class="weui_media_desc">买二: %s手  %s元</p>
<p class="weui_media_desc">买三: %s手  %s元</p>
<p class="weui_media_desc">买四: %s手  %s元</p>
<p class="weui_media_desc">买五: %s手  %s元</p>
<p class="weui_media_desc">卖一: %s手  %s元</p>
<p class="weui_media_desc">卖二: %s手  %s元</p>
<p class="weui_media_desc">卖三: %s手  %s元</p>
<p class="weui_media_desc">卖四: %s手  %s元</p>
<p class="weui_media_desc">卖五: %s手  %s元</p>
<p class="weui_media_desc">分时图: <img src="%s"/></p>
<p class="weui_media_desc">日K线: <img src="%s"/></p>
<p class="weui_media_desc">周K线: <img src="%s"/></p>
<p class="weui_media_desc">月K线: <img src="%s"/></p>

<p class="weui_media_desc blue">仅供参考，非投资依据。</p>
EOF;
    
$msgformat['msg_weather_web'] = <<<EOF
<p class="weui_media_desc">%s天气：</p>
<p class="weui_media_desc">    日期：%s</p>
<p class="weui_media_desc">    发布时间：%s</p>
<p class="weui_media_desc">    天气：%s</p>
<p class="weui_media_desc">    当前气温：%s℃</p>
<p class="weui_media_desc">    最高：%s℃</p>
<p class="weui_media_desc">    最低：%s℃</p>
<p class="weui_media_desc">    风向：%s</p>
<p class="weui_media_desc">    风力：%s</p>
<p class="weui_media_desc">    日出时间：%s</p>
<p class="weui_media_desc">    日落时间：%s</p>      
EOF;
    
$msgformat['msg_news_web'] = <<<EOF
<div class="container-fluid">
<!--<div class="hd">
<h1 class="page_title">资讯</h1>
</div>-->
<div class="bd">
<ul class="list-group">%s%s</ul>
</div>
</div>
EOF;

$msgformat['msg_news_banner'] = <<<EOF
<li class="list-group-item">
<a class="bg-wrapper" href="%s">
<img src="%s" class="carousel-inner img-responsive" onerror="this.src='%s'"/>
<div class="banner">
<h5 class="font16">%s</h5>
</div>
</a>
</li>    
EOF;
    
$msgformat['msg_news_list'] = <<<EOF
<li class="list-group-item">
<a class="row" href="%s">
<div class="col-xs-9 no-new-line">
<div class="txt"><span>%s</span></div>
</div>
<div class="col-xs-3"><img src="%s" class="pull-right img" onerror="this.src='%s'"/></div>
</a>
</li>    
EOF;
