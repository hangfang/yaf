<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

$_msg_weather = <<<EOF
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
    
$_msg_stock = <<<EOF
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
    
$_msg_lottery = <<<EOF
彩种：%s
期号：%s
时间：%s
号码：%s
EOF;
    
$_msg_joke = <<<EOF
标题：%s
            
%s
EOF;

$_msg_unrecognized = <<<EOF
咦，您是说“%s”吗？
可小i尚小，未能处理ㄒoㄒ

1、发送如“北京”<a href="%s/#/query">查询</a>天气
2、发送如“申通，xx”<a href="%s/#/query">查询</a>物流
3、发送如“600000”<a href="%s/#/query">查询</a>股票数据
4、发送如“美容”等，搜索周边
5、最新推出<a href="%s/map/index">地图服务</a>
6、更多隐藏功能由您发掘…
        
感谢关注
EOF;
        
$_msg_to_large = <<<EOF
额，信息量太大
请说重点(*≧▽≦*)

1、发送如“北京”<a href="%s/#/query">查询</a>天气
2、发送如“申通，xx”<a href="%s/#/query">查询</a>物流
3、发送如“600000”<a href="%s/#/query">查询</a>股票数据
4、发送如“美容”等，搜索周边
5、最新推出<a href="%s/map/index">地图服务</a>
6、更多隐藏功能由您发掘…
        
感谢关注
EOF;

$_msg_welcome_back = <<<EOF
热烈欢迎老伙伴回归！

1、发送如“北京”<a href="%s/#/query">查询</a>天气
2、发送如“申通，xx”<a href="%s/#/query">查询</a>物流
3、发送如“600000”<a href="%s/#/query">查询</a>股票数据
4、发送如“美容”等，搜索周边
5、最新推出<a href="%s/map/index">地图服务</a>
6、更多隐藏功能由您发掘…
        
感谢关注
EOF;

$_msg_welcome_newbeing = <<<EOF
撒花欢迎新朋友到来！

1、发送如“北京”<a href="%s/#/query">查询</a>天气
2、发送如“申通，xx”<a href="%s/#/query">查询</a>物流
3、发送如“600000”<a href="%s/#/query">查询</a>股票数据
4、发送如“美容”等，搜索周边
5、最新推出<a href="%s/map/index">地图服务</a>
6、更多隐藏功能由您发掘…
        
感谢关注
EOF;
       
$_msg_position = <<<EOF
OK，我记住了
您在%s！
试试搜索周边？如酒店、美食...
EOF;
    
$_msg_position_expired = <<<EOF
您的位置信息已很久远
于[%s]定位
为精确搜索周边，请重新发送位置
EOF;

$_msg_kuaidi = <<<EOF
公司名称：%s
快递单号：%s
物流信息：%s
EOF;

$_text_format = <<<EOF
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>
EOF;
    
$_image_format = <<<EOF
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
    
$_voice_format = <<<EOF
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

$_video_format = <<<EOF
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

$_music_format = <<<EOF
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
    
$_news_format = <<<EOF
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>%s</Articles>
</xml> 
EOF;
    
$_send_format = array(
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
$_receive_format = array(
    'text' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Content', 'MsgId'),
    'image' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'PicUrl', 'MediaId', 'MsgId'),
    'voice' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'MediaId', 'Format', 'MsgId', 'Recognition'),
    'video' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'MediaId', 'ThumbMediaId', 'MsgId'),
    'shortvideo' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'MediaId', 'ThumbMediaId', 'MsgId'),
    'location' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Location_X', 'Location_Y', 'Scale', 'Label', 'MsgId'),
    'link' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Title', 'Description', 'Url', 'MsgId'),
    'event' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Event', 'EventKey', 'Latitude', 'Longitude', 'Precision', 'Ticket', 'MsgId'),
);

$_msg_lottery_web = <<<EOF
<p class="weui_media_desc">彩种：%s</p>
<p class="weui_media_desc">期号：%s</p>
<p class="weui_media_desc">时间：%s</p>
<p class="weui_media_desc">号码：%s</p>
%s
EOF;
    
$_msg_lottery_extra = <<<EOF
<p class="weui_media_desc">销量：%s</p>
<p class="weui_media_desc">奖池：%s</p>
%s
EOF;
    
$_ssq_pride = <<<EOF
<p class="weui_media_desc">一等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">二等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">三等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">四等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">五等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">六等奖：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;
    
$_dlt_pride = <<<EOF
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
    
$_fc3d_pride = <<<EOF
<p class="weui_media_desc">直选：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">%s：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;
        
$_pl3_pride = <<<EOF
<p class="weui_media_desc">直选：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">%s：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;
    
$_pl5_pride = <<<EOF
<p class="weui_media_desc">直选：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;

$_qxc_pride = <<<EOF
<p class="weui_media_desc">一等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">二等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">三等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">四等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">五等奖：奖金%s&nbsp;&nbsp;共%s注</p>
<p class="weui_media_desc">六等奖：奖金%s&nbsp;&nbsp;共%s注</p>
EOF;

