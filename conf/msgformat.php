<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

$msgformat = array();

$msgformat['msg_kfsession_create_fail'] = <<<EOF
很抱歉，客服正忙
    推荐方案：
    1、稍后再次尝试接入
    2、<a href='tel:{SERVICE_TEL}'>拨打客服电话</a>
EOF;

$msgformat['msg_kfsession_create_succ'] = <<<EOF
正在接入客服，请稍后...
EOF;

$msgformat['msg_invite'] = <<<EOF
欢迎关注诸葛到店,黄金珠宝店的好军师\n
        为黄金珠宝品牌商和门店研发的高效、低成本SaaS解决方案！\n
        <a href="http://api.zhugedaodian.com/app/download">【点击下载】</a>最新版的诸葛到店app，让“诸葛到店”带你轻松运营门店！
------------------------\n
想了解更多信息，请<a href="https://www.zhugedaodian.com">点击这里</a>\n
EOF;

$msgformat['msg_gold'] = <<<EOF
欢迎关注诸葛到店,黄金珠宝店的好军师\n
        为黄金珠宝品牌商和门店研发的高效、低成本SaaS解决方案！\n
        <a href="http://api.zhugedaodian.com/app/download">【点击下载】</a>最新版的诸葛到店app，让“诸葛到店”带你轻松运营门店！
------------------------\n
想了解更多信息，请<a href="https://www.zhugedaodian.com">点击这里</a>\n
EOF;

$msgformat['msg_unrecognized'] = <<<EOF
欢迎关注诸葛到店,黄金珠宝店的好军师\n
        为黄金珠宝品牌商和门店研发的高效、低成本SaaS解决方案！\n
        <a href="http://api.zhugedaodian.com/app/download">【点击下载】</a>最新版的诸葛到店app，让“诸葛到店”带你轻松运营门店！
------------------------\n
想了解更多信息，请<a href="https://www.zhugedaodian.com">点击这里</a>\n
EOF;
        
$msgformat['msg_to_large'] = <<<EOF
额，信息量太大
请说重点(*≧▽≦*)

...
        
感谢关注
EOF;

$msgformat['msg_position'] = <<<EOF
请注意保护好个人隐私！

...
        
感谢关注
EOF;

$msgformat['msg_welcome_back'] = <<<EOF
欢迎关注诸葛到店,黄金珠宝店的好军师\n
        为黄金珠宝品牌商和门店研发的高效、低成本SaaS解决方案！\n
        <a href="http://api.zhugedaodian.com/app/download">【点击下载】</a>最新版的诸葛到店app，让“诸葛到店”带你轻松运营门店！
------------------------\n
想了解更多信息，请<a href="https://www.zhugedaodian.com">点击这里</a>\n
EOF;

$msgformat['msg_welcome_newbeing'] = <<<EOF
欢迎关注诸葛到店,黄金珠宝店的好军师\n
        为黄金珠宝品牌商和门店研发的高效、低成本SaaS解决方案！\n
        <a href="http://api.zhugedaodian.com/app/download">【点击下载】</a>最新版的诸葛到店app，让“诸葛到店”带你轻松运营门店！
------------------------\n
想了解更多信息，请<a href="https://www.zhugedaodian.com">点击这里</a>\n
EOF;

$msgformat['transfer_customer_service_format'] = <<<EOF
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
<TransInfo>
<KfAccount><![CDATA[%s]]></KfAccount>
 </TransInfo>
</xml>
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
<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
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
<Articles>
%s
</Articles>
</xml> 
EOF;
    
$msgformat['send_format'] = array(
    'transfer_customer_service' => array('touser'=>'', 'msgtype'=>'transfer_customer_service', 'transinfo'=>array('kfaccount'=>'')),
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
    'event' => array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Event', 'EventKey', 'SendLocationInfo', 'Latitude', 'Longitude', 'Precision', 'Ticket', 'MsgId', 'SendPicsInfo', 'ScanCodeInfo'),
);