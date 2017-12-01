<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @todo 微信素材管理
 */
class Manage_MaterialController extends BasicController {
    public function init(){
        parent::init();
        if(!Wechat_MsgModel::initDomain()){
            lExit(502, '当前企业未接入微信公众号');
        }
    }
    
    /**
     * @todo 获取永久素材的列表。
     * @param string type 素材的类型,图片image、视频video、语音voice、图文news
     * @param int offset 分页.偏移量
     * @param int length 分页.每页记录数
     * @return 永久图文消息素材列表的响应如下：
     *   {
     *     "total_count": TOTAL_COUNT,
     *     "item_count": ITEM_COUNT,
     *     "item": [{
     *         "media_id": MEDIA_ID,
     *         "content": {
     *             "news_item": [{
     *                 "title": TITLE,
     *                 "thumb_media_id": THUMB_MEDIA_ID,
     *                 "show_cover_pic": SHOW_COVER_PIC(0 / 1),
     *                 "author": AUTHOR,
     *                 "digest": DIGEST,
     *                 "content": CONTENT,
     *                 "url": URL,
     *                 "content_source_url": CONTETN_SOURCE_URL
     *             },
     *             //多图文消息会在此处有多篇文章
     *             ]
     *          },
     *          "update_time": UPDATE_TIME
     *      },
     *      //可能有多个图文消息item结构
     *    ]
     *   }
     *    其他类型（图片、语音、视频）的返回如下：
     *   {
     *     "total_count": TOTAL_COUNT,
     *     "item_count": ITEM_COUNT,
     *     "item": [{
     *         "media_id": MEDIA_ID,
     *         "name": NAME,
     *         "update_time": UPDATE_TIME,
     *         "url":URL
     *     },
     *     //可能会有多个素材
     *     ]
     *   }
     *   返回参数说明
     *   参数	描述
     *   total_count	该类型的素材的总数
     *   item_count	本次调用获取的素材的数量
     *   title	图文消息的标题
     *   thumb_media_id	图文消息的封面图片素材id（必须是永久mediaID）
     *   show_cover_pic	是否显示封面，0为false，即不显示，1为true，即显示
     *   author	作者
     *   digest	图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
     *   content	图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
     *   url	图文页的URL，或者，当获取的列表是图片素材列表时，该字段是图片的URL
     *   content_source_url	图文消息的原文地址，即点击“阅读原文”后的URL
     *   update_time		这篇图文消息素材的最后更新时间
     *   name	文件名称
     *   错误情况下的返回JSON数据包示例如下（示例为无效媒体类型错误）：
     *   {"errcode":40007,"errmsg":"invalid media_id"}
     */
    public function batchGetAction(){
        $type = BaseModel::getPost('type');
        if(!in_array($type, ['image', 'video', 'voice', 'news'])){
            lExit(502, '素材的类型错误,必须为图片（image）、视频（video）、语音 （voice）、图文（news）');
        }
        
        $offset = intval(BaseModel::getPost('offset', 0));
        $offset = $offset<0 ? 0 : $offset;
        
        $length = intval(BaseModel::getPost('length'));
        $length = $length<=0 ? 10 : $offset;
        if($length<1 || $length>20){
            lExit(502, '素材数量取值在1到20之间');
        }
        
        lExit(Wechat_ApiModel::batchGetMaterial($type, $offset, $length));
    }
    
    /**
     * @todo 获取永久素材总数
     * @return 
     *   返回参数说明
     *   参数	描述
     *   voice_count 语音总数量
     *   video_count 视频总数量
     *   image_count 图片总数量
     *   news_count	图文总数量
     *   错误情况下的返回JSON数据包示例如下（示例为无效媒体类型错误）：
     *   {"errcode":-1,"errmsg":"system error"}
     */
    public function getCountAction(){
        lExit(Wechat_ApiModel::getMaterialCount());
    }
    
    /**
     * @todo 获取永久素材
     * @param string media_id 素材id
     * @return
     *  图文素材: {
     *       "news_item":[
     *           {
     *               "title":TITLE,//标题
     *               "thumb_media_id"::THUMB_MEDIA_ID,//封面
     *               "show_cover_pic":SHOW_COVER_PIC(0/1),//是否显示封面
     *               "author":AUTHOR,//作者
     *               "digest":DIGEST,//摘要
     *               "content":CONTENT,//正文
     *               "url":URL,//图文页的url
     *               "content_source_url":CONTENT_SOURCE_URL//阅读原文的url
     *           },
     *           //多图文消息有多篇文章
     *       ],...
     *   }
     *   视频消息素材： {
     *    "title":TITLE,//标题
     *    "description":DESCRIPTION,//描述
     *    "down_url":DOWN_URL,//下载地址
     *   }
     */
    public function getDetailAction(){
        $mediaId = BaseModel::getPost('media_id');
        if(empty($mediaId)){
            lExit(502, '素材id非法');
        }
        lExit(Wechat_ApiModel::getMaterial($mediaId));
    }
    
    /**
     * @todo 获取临时素材
     * @param string media_id 素材id
     * @reutrn 返回说明
        正确情况下的返回HTTP头如下：
        HTTP/1.1 200 OK
        Connection: close
        Content-Type: image/jpeg 
        Content-disposition: attachment; filename="MEDIA_ID.jpg"
        Date: Sun, 06 Jan 2013 10:20:18 GMT
        Cache-Control: no-cache, must-revalidate
        Content-Length: 339721
        curl -G "https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID"

        如果返回的是视频消息素材，则内容如下：
        {
         "video_url":DOWN_URL
        }

        错误情况下的返回JSON数据包示例如下（示例为无效媒体ID错误）：
        {"errcode":40007,"errmsg":"invalid media_id"}
     */
    public function getMediaAction(){
        $mediaId = BaseModel::getPost('media_id');
        if(empty($mediaId)){
            lExit(502, '素材id非法');
        }
        exit(Wechat_ApiModel::getMedia($mediaId));
    }
    
    /**
     * @todo 添加临时素材
     * @param string $type 媒体文件类型，分别有图片image、语音voice、视频video和缩略图thumb
     * @param file $media 上传的文件
     * @return
     *   type	媒体文件类型
     *   media_id	媒体文件上传后，获取标识
     *   created_at	媒体文件上传时间戳
     *   错误情况下的返回JSON数据包示例如下（示例为无效媒体类型错误）：
     *   {"errcode":40004,"errmsg":"invalid media type"}
     */
    public function uploadMediaAction(){
        $type = BaseModel::getPost('type');
        if(!in_array($type, ['image', 'video', 'voice', 'thumb'])){
            lExit(502, '素材的类型错误,必须为图片(image)、视频(video)、视频(video)、缩略图(thumb)');
        }
        
        if($_FILES['media']['error']){
            lExit(502, ['', '文件大小超过限制', '文件大小超过HTML表单中隐藏域MAX_FILE_SIZE选项指定的值', '文件只有部分被上传', '没有找到上传的文件', '临时文件夹丢失', '找不到临时文件夹', '文件写入失败'][$_FILES['media']['error']]);
        }
        
        if(!isset($_FILES['media']['tmp_name'])){
            lExit(212);
        }
        
        if(!isset($_FILES['media']['size']) || $_FILES['media']['size'] > 20480000){
            lExit(213, '文件尺寸不能超过20M');
        }

        $filePath = '/upload/material/'. $type .'/';
        if(!file_exists(APPLICATION_PATH . $filePath)){
            $rt = mkdir(APPLICATION_PATH . $filePath, 0777, true);
            if(!$rt){
                lExit(214, '创建文件保存目录失败');
            }
        }

        $filePath = APPLICATION_PATH . $filePath . $_FILES['media']['name'];
        if(!move_uploaded_file($_FILES['media']['tmp_name'], $filePath)){
            lExit(215, '移动文件至保存目录失败');
        }

        lExit(Wechat_ApiModel::uploadMedia($type, ['media'=>new CURLFile($filePath)]));
    }
    
    /**
     * @todo 新增永久图文素材
     * @param string articles[0]['title'] 标题
     * @param string articles[0]['thumb_media_id'] 图文消息的封面图片素材id（必须是永久mediaID）
     * @param string articles[0]['author'] 作者
     * @param string articles[0]['digest'] 图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空。如果本字段为没有填写，则默认抓取正文前64个字。
     * @param string articles[0]['show_cover_pic'] 是否显示封面，0为false，即不显示，1为true，即显示
     * @param string articles[0]['content'] 图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS,涉及图片url必须来源"上传图文消息内的图片获取URL"接口获取。外部图片url将被过滤。
     * @param string articles[0]['content_source_url'] 图文消息的原文地址，即点击“阅读原文”后的URL
     * @param string articles[0]['need_open_comment'] 是否打开评论，0不打开，1打开
     * @param string articles[0]['only_fans_can_comment'] 是否粉丝才可评论，0所有人可评论，1粉丝才可评论
     * @return {
     *   "media_id":MEDIA_ID
     * }
     */
    public function addNewsAction(){
        $articles = BaseModel::getPost('articles');
        if(empty($articles)){
            $articles = [];
            $request = BaseModel::getPost();
            foreach($request as $_k=>$_v){
                eval('$'.$_k.'=\''.addslashes($_v).'\';');
            }

            if(empty($articles)){
                lExit(502, '素材信息不能为空');
            }
        }
        
        foreach($articles as &$_article){
            if(empty($_article['title'])){
                lExit(502, '标题不能为空');
            }
            
            if(empty($_article['thumb_media_id'])){
                lExit(502, '封面素材id不能为空');
            }
            
            if(empty($_article['content'])){
                lExit(502, '图文内容不能为空');
            }
            
            if(empty($_article['content_source_url'])){
                lExit(502, '"阅读原文"的url不能为空');
            }
            
            $_article['show_cover_pic'] = !!$_article['show_cover_pic'];
            $_article['need_open_comment'] = !!$_article['need_open_comment'];
            $_article['only_fans_can_comment'] = !!$_article['only_fans_can_comment'];
        }
        
        lExit(Wechat_ApiModel::addNews($articles));
    }
    
    /**
     * @todo 上传图文消息内的图片获取URL
     * @param file media 上传图片素材
     * @return {
     *      "url":  "http://mmbiz.qpic.cn/mmbiz/gLO17UPS6FS2xsypf378iaNhWacZ1G1UplZYWEYfwvuU6Ont96b1roYs CNFwaRrSaKTPCUdBK9DgEHicsKwWCBRQ/0"
     *   }
     */
    public function uploadImgAction(){
        if(!isset($_FILES['media']['tmp_name'])){
            log_message('error','图片不存在');
            lExit(212);
        }
        
        if($_FILES['media']['error']){
            lExit(502, ['', '文件大小超过限制', '文件大小超过HTML表单中隐藏域MAX_FILE_SIZE选项指定的值', '文件只有部分被上传', '没有找到上传的文件', '临时文件夹丢失', '找不到临时文件夹', '文件写入失败'][$_FILES['media']['error']]);
        }
        
        if(!isset($_FILES['media']['size']) || $_FILES['media']['size'] > 10240000){
            lExit(213, '文件尺寸不能超过10M');
        }

        $filePath = '/upload/material/image/';
        if(!file_exists(APPLICATION_PATH . $filePath)){
            $rt = mkdir(APPLICATION_PATH . $filePath, 0777, true);
            if(!$rt){
                lExit(214, '创建文件保存目录失败');
            }
        }

        $filePath = APPLICATION_PATH . $filePath . $_FILES['media']['name'];
        if(!move_uploaded_file($_FILES['media']['tmp_name'], $filePath)){
            lExit(215, '移动文件至保存目录失败');
        }

        lExit(Wechat_ApiModel::uploadImg(new CURLFile($filePath)));
    }
    
    /**
     * @todo 新增其他类型永久素材
     * @param string title 视频素材的标题
     * @param string introduction 视频素材的描述
     * @param string type 媒体文件类型，分别有图片image、语音voice、视频video和缩略图thumb
     * @param file media 上传图片素材
     * @return {
     *   "media_id":MEDIA_ID,
     *   "url":URL
     *  }
     */
    public function uploadOtherAction(){
        $type = BaseModel::getPost('type');
        if(!in_array($type, ['image', 'video', 'voice', 'thumb'])){
            lExit(502, '素材的类型错误,必须为图片(image)、视频(video)、视频(video)、缩略图(thumb)');
        }
        
        if($_FILES['media']['error']){
            lExit(502, ['', '文件大小超过限制', '文件大小超过HTML表单中隐藏域MAX_FILE_SIZE选项指定的值', '文件只有部分被上传', '没有找到上传的文件', '临时文件夹丢失', '找不到临时文件夹', '文件写入失败'][$_FILES['media']['error']]);
        }
        
        if(!isset($_FILES['media']['tmp_name'])){
            lExit(212);
        }

        if(!isset($_FILES['media']['size']) || $_FILES['media']['size'] > 20480000){
            lExit(213, '文件尺寸不能超过20M');
        }

        $filePath = '/upload/material/'. $type .'/';
        if(!file_exists(APPLICATION_PATH . $filePath)){
            $rt = mkdir(APPLICATION_PATH . $filePath, 0777, true);
            if(!$rt){
                lExit(214, '创建文件保存目录失败');
            }
        }

        $filePath = APPLICATION_PATH . $filePath . $_FILES['media']['name'];
        if(!move_uploaded_file($_FILES['media']['tmp_name'], $filePath)){
            lExit(215, '移动文件至保存目录失败');
        }
        
        if($type==='video'){
            $title = BaseModel::getPost('title');
            if(empty($title)){
                lExit(502, '视频标题不能为空');
            }
            
            $introduction = BaseModel::getPost('introduction');
            if(empty($introduction)){
                lExit(502, '视频描述不能为空');
            }
            lExit(Wechat_ApiModel::uploadVideo($title, $introduction, $type, new CURLFile($filePath)));
        }

        lExit(Wechat_ApiModel::uploadOther($type, new CURLFile($filePath)));
    }
    
    /**
     * @todo 删除永久素材
     * @param string media_id 素材id
     * @return {
     *   "errcode":ERRCODE,
     *   "errmsg":ERRMSG
     *  }
     */
    public function delAction(){
        $mediaId = BaseModel::getPost('media_id');
        if(empty($mediaId)){
            lExit(502, '素材id不能为空');
        }
        
        lExit(Wechat_ApiModel::delMaterial($mediaId));
    }
    
    /**
     * @todo 修改永久图文素材
     * @param string media_id 要修改的图文消息的id
     * @param int index 要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0
     * @param string articles['title'] 标题
     * @param string articles['thumb_media_id'] 图文消息的封面图片素材id（必须是永久mediaID）
     * @param string articles['author'] 作者
     * @param string articles['digest'] 图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空。如果本字段为没有填写，则默认抓取正文前64个字。
     * @param string articles['show_cover_pic'] 是否显示封面，0为false，即不显示，1为true，即显示
     * @param string articles['content'] 图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS,涉及图片url必须来源"上传图文消息内的图片获取URL"接口获取。外部图片url将被过滤。
     * @param string articles['content_source_url'] 图文消息的原文地址，即点击“阅读原文”后的URL
     * @param string articles['need_open_comment'] 是否打开评论，0不打开，1打开
     * @param string articles['only_fans_can_comment'] 是否粉丝才可评论，0所有人可评论，1粉丝才可评论
     * @return {
     *   "media_id" : MEDIA_ID
     *   }
     */
    public function updateNewsAction(){
        $params = [];
        $tmp = BaseModel::getPost('media_id');
        if(empty($tmp)){
            lExit(502, '素材id不能为空');
        }
        $params['media_id'] = $tmp;
        
        $tmp = intval(BaseModel::getPost('index'));
        $params['media_id'] = $tmp;
        
        $articles = BaseModel::getPost('articles');
        if(empty($articles)){
            $articles = [];
            $request = BaseModel::getPost();
            foreach($request as $_k=>$_v){
                if(in_array($_k, ['media_id', 'index'])){
                    continue;
                }
                eval('$'.$_k.'=\''.addslashes($_v).'\';');
            }

            if(empty($articles)){
                lExit(502, '素材信息不能为空');
            }
        }
        
        foreach($articles as $_k=>&$_v){
            if(in_array($_v, ['show_cover_pic', 'need_open_comment', 'only_fans_can_comment'])){
                $_v = !!$_v;
                continue;
            }
            
            if(!strlen($_v)){
                lExit(502, $_k.'不能为空');    
            }
        }
        $params['articles'] = $articles;
        lExit(Wechat_ApiModel::updateNews($params));
    }
}