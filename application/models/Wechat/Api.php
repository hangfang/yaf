<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

/**
 * 微信公众号的API可以放到这里
 */
class Wechat_ApiModel extends BaseModel{
    public static $_error = [
        '-1'=>'系统繁忙，此时请开发者稍候再试',
        0=>'请求成功',
        40001=>'获取access_token时AppSecret错误，或者access_token无效。请开发者认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口',
        40002=>'不合法的凭证类型',
        40003=>'不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID',
        40004=>'不合法的媒体文件类型',
        40005=>'不合法的文件类型',
        40006=>'不合法的文件大小',
        40007=>'不合法的媒体文件id',
        40008=>'不合法的消息类型',
        40009=>'不合法的图片文件大小',
        40010=>'不合法的语音文件大小',
        40011=>'不合法的视频文件大小',
        40012=>'不合法的缩略图文件大小',
        40013=>'不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写',
        40014=>'不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
        40015=>'不合法的菜单类型',
        40016=>'不合法的按钮个数',
        40017=>'不合法的按钮个数',
        40018=>'不合法的按钮名字长度',
        40019=>'不合法的按钮KEY长度',
        40020=>'不合法的按钮URL长度',
        40021=>'不合法的菜单版本号',
        40022=>'不合法的子菜单级数',
        40023=>'不合法的子菜单按钮个数',
        40024=>'不合法的子菜单按钮类型',
        40025=>'不合法的子菜单按钮名字长度',
        40026=>'不合法的子菜单按钮KEY长度',
        40027=>'不合法的子菜单按钮URL长度',
        40028=>'不合法的自定义菜单使用用户',
        40029=>'不合法的oauth_code',
        40030=>'不合法的refresh_token',
        40031=>'不合法的openid列表',
        40032=>'不合法的openid列表长度',
        40033=>'不合法的请求字符，不能包含\uxxxx格式的字符',
        40035=>'不合法的参数',
        40038=>'不合法的请求格式',
        40039=>'不合法的URL长度',
        40050=>'不合法的分组id',
        40051=>'分组名字不合法',
        40060=>'删除单篇图文时，指定的 article_idx 不合法',
        40117=>'分组名字不合法',
        40118=>'media_id大小不合法',
        40119=>'button类型错误',
        40120=>'button类型错误',
        40121=>'不合法的media_id类型',
        40132=>'微信号不合法',
        40137=>'不支持的图片格式',
        40155=>'请勿添加其他公众号的主页链接',
        41001=>'缺少access_token参数',
        41002=>'缺少appid参数',
        41003=>'缺少refresh_token参数',
        41004=>'缺少secret参数',
        41005=>'缺少多媒体文件数据',
        41006=>'缺少media_id参数',
        41007=>'缺少子菜单数据',
        41008=>'缺少oauth code',
        41009=>'缺少openid',
        42001=>'access_token超时，请检查access_token的有效期，请参考基础支持-获取access_token中，对access_token的详细机制说明',
        42002=>'refresh_token超时',
        42003=>'oauth_code超时',
        42007=>'用户修改微信密码，accesstoken和refreshtoken失效，需要重新授权',
        43001=>'需要GET请求',
        43002=>'需要POST请求',
        43003=>'需要HTTPS请求',
        43004=>'需要接收者关注',
        43005=>'需要好友关系',
        43019=>'需要将接收者从黑名单中移除',
        44001=>'多媒体文件为空',
        44002=>'POST的数据包为空',
        44003=>'图文消息内容为空',
        44004=>'文本消息内容为空',
        45001=>'多媒体文件大小超过限制',
        45002=>'消息内容超过限制',
        45003=>'标题字段超过限制',
        45004=>'描述字段超过限制',
        45005=>'链接字段超过限制',
        45006=>'图片链接字段超过限制',
        45007=>'语音播放时间超过限制',
        45008=>'图文消息超过限制',
        45009=>'接口调用超过限制',
        45010=>'创建菜单个数超过限制',
        45011=>'API调用太频繁，请稍候再试',
        45015=>'回复时间超过限制',
        45016=>'系统分组，不允许修改',
        45017=>'分组名字过长',
        45018=>'分组数量超过上限',
        45047=>'客服接口下行条数超过上限',
        46001=>'不存在媒体数据',
        46002=>'不存在的菜单版本',
        46003=>'不存在的菜单数据',
        46004=>'不存在的用户',
        47001=>'解析JSON/XML内容错误',
        48001=>'api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限',
        48002=>'粉丝拒收消息（粉丝在公众号选项中，关闭了“接收消息”）',
        48004=>'api接口被封禁，请登录mp.weixin.qq.com查看详情',
        48005=>'api禁止删除被自动回复和自定义菜单引用的素材',
        48006=>'api禁止清零调用次数，因为清零次数达到上限',
        50001=>'用户未授权该api',
        50002=>'用户受限，可能是违规后接口被封禁',
        61451=>'参数错误(invalid parameter)',
        61452=>'无效客服账号(invalid kf_account)',
        61453=>'客服帐号已存在(kf_account exsited)',
        61454=>'客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)(invalid   kf_acount length)',
        61455=>'客服帐号名包含非法字符(仅允许英文+数字)(illegal character in     kf_account)',
        61456=>'客服帐号个数超过限制(10个客服账号)(kf_account count exceeded)',
        61457=>'无效头像文件类型(invalid   file type)',
        61450=>'系统错误(system error)',
        61500=>'日期格式错误',
        65301=>'不存在此menuid对应的个性化菜单',
        65302=>'没有相应的用户',
        65303=>'没有默认菜单，不能创建个性化菜单',
        65304=>'MatchRule信息为空',
        65305=>'个性化菜单数量受限',
        65306=>'不支持个性化菜单的帐号',
        65307=>'个性化菜单信息为空',
        65308=>'包含没有响应类型的button',
        65309=>'个性化菜单开关处于关闭状态',
        65310=>'填写了省份或城市信息，国家信息不能为空',
        65311=>'填写了城市信息，省份信息不能为空',
        65312=>'不合法的国家信息',
        65313=>'不合法的省份信息',
        65314=>'不合法的城市信息',
        65316=>'该公众号的菜单设置了过多的域名外跳（最多跳转到3个域名的链接）',
        65317=>'不合法的URL',
        9001001=>'POST数据参数不合法',
        9001002=>'远端服务不可用',
        9001003=>'Ticket不合法',
        9001004=>'获取摇周边用户信息失败',
        9001005=>'获取商户信息失败',
        9001006=>'获取OpenID失败',
        9001007=>'上传文件缺失',
        9001008=>'上传素材的文件类型不合法',
        9001009=>'上传素材的文件尺寸不合法',
        9001010=>'上传失败',
        9001020=>'帐号不合法',
        9001021=>'已有设备激活率低于50%，不能新增设备',
        9001022=>'设备申请数不合法，必须为大于0的数字',
        9001023=>'已存在审核中的设备ID申请',
        9001024=>'一次查询设备ID数量不能超过50',
        9001025=>'设备ID不合法',
        9001026=>'页面ID不合法',
        9001027=>'页面参数不合法',
        9001028=>'一次删除页面ID数量不能超过10',
        9001029=>'页面已应用在设备中，请先解除应用关系再删除',
        9001030=>'一次查询页面ID数量不能超过50',
        9001031=>'时间区间不合法',
        9001032=>'保存设备与页面的绑定关系参数错误',
        9001033=>'门店ID不合法',
        9001034=>'设备备注信息过长',
        9001035=>'设备申请参数不合法',
        9001036=>'查询起始值begin不合法'
    ];
    
    /**
     * 请求微信api
     * @param string $uri 接口uri
     * @param array $params 请求参数
     * @return boolean or array
     */
    private static function request($uri, $params, $method='POST', $get=[]){
        $access_token = Wechat_MsgModel::getAccessToken();
        if(!$access_token){
            return false;
        }
        
        $args = ['method'=>$method, 'url'=>sprintf(Yaf_Registry::get('WECHAT_API_HOST').'/cgi-bin/%s?access_token=%s', $uri, $access_token['access_token'])];
        if(!empty($get)){
            foreach($get as $_k=>$_v){
                $args['url'] .= '&'.$_k.'='.$_v;
            }
        }
        if(!empty($params)){
            $args['data'] = $params;
            if($args['method']==='POST'){
                $args['data'] = json_encode($params, JSON_UNESCAPED_UNICODE);
            }else if($args['method']==='UPLOAD'){
                $args['method']='POST';
                $args['url'] = preg_replace('/^https:/', 'http:', $args['url']);
                //$args['header'] = ['multipart/form-data'];
            }
        }
        
        $rt = http($args);
        
        if(isset($rt['errcode']) && $rt['errcode']>0){
            $rt['errmsg'] = isset(self::$_error[$rt['errcode']]) ? self::$_error[$rt['errcode']] : $rt['errmsg'];
            log_message('error', __FUNCTION__.' failed, msg: '. print_r($rt, true));
        }
        
        return $rt;
    }
    
    /**
     * 查询用户微信信息
     * @return array
     */
    public static function getSnsUserInfo(){
        $args = ['url'=>sprintf(Yaf_Registry::get('WECHAT_API_HOST').'/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN', $_SESSION['wechat']['access_token'], $_SESSION['wechat']['openid'])];
        return http($args);
    }
    
    /**
     * 推送模版消息,从job/sync/templatemsgqueue调用
     * @param string templateId 模版id
     * @param array params 模版数据
     * @return boolean
     */
    public static function pushTemplateMsg($params){
        if(empty($params['client_id'])){
            log_message('error', __FUNCTION__.', 企业id不能为空, $params: '. print_r($params, true));
            return false;
        }
        
        if(empty($params['template_id'])){
            log_message('error', __FUNCTION__.', 模版id不能为空, $params: '. print_r($params, true));
            return false;
        }
        
        if(empty($params['touser'])){
            log_message('error', __FUNCTION__.', 接收人openid不能为空, $params: '. print_r($params, true));
            return false;
        }
        
        if(empty($params['data'])){
            log_message('error', __FUNCTION__.', 消息数据不能为空, $params: '. print_r($params, true));
            return false;
        }
        
        $insert = [
            'domain'        => BaseModel::getDomain(),
            'template_id'   =>  $params['template_id'],
            'touser'        =>  $params['touser'],
            'url'           =>  isset($params['url']) ? $params['url'] : '',
            'data'          =>  json_encode($params['data']),
            'msg_type'      =>  isset($params['msg_type']) ? $params['msg_type'] : '',
            'status'        =>  'INIT',
            'create_time'   =>  time(),
            'ts'            =>  date('Y-m-d H:i:s')
        ];
        $msgId = Operation_WechatTemplateMessageModel::insert($insert);
        if(!$msgId){
            log_message('error', __FUNCTION__.', 保存模版消息失败, $insert: '. print_r($insert, true));
            return false;
        }
        
        $data = array('template_id'=>$params['template_id'], 'url'=>$params['url'], 'topcolor'=>$params['topcolor'], 'touser'=>$params['touser'], 'data'=>array());
        
        foreach($params['data'] as $_variable=>&$_data){
            if(!is_array($_data) && !is_object($_data)){
                $_data = ['value'=>$_data, 'color'=>'#000000'];
                continue;
            }
            
            if(!isset($_data['value'])){
                log_message('error', __FUNCTION__.', value not found error, $param: '. print_r($params, true));
                return false;
            }
            
            if(!isset($_data['color'])){
                $_data['color'] = '#000000';
            }
        }
        $data['data'] = $params['data'];
        
        $access_token = Wechat_MsgModel::getAccessToken();
        if(!$access_token){
            return false;
        }
        
        $args = [];
        $args['data'] = $data;
        $args['url'] = sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s', $access_token['access_token']);
        $args['method'] = 'put';
        $rt = http($args);

        if(isset($rt['errcode']) && $rt['errcode']>0){
            $rt['errmsg'] = isset(self::$_error[$rt['errcode']]) ? self::$_error[$rt['errcode']] : $rt['errmsg'];
            if(!Operation_WechatTemplateMessageModel::update(['status'=>'FAILED', 'return'=>json_encode($rt)], ['id'=>$msgId])){
                log_message('error', __FUNCTION__.', 更新模版消息状态, INIT=>FAILED,失败, $msgId: '. print_r($msgId, true));
            }
            log_message('error', __FUNCTION__.', push template msg failed, msg: '. print_r($rt, true));
            return false;
        }
        
        if(!Operation_WechatTemplateMessageModel::update(['status'=>'SUCC', 'return'=>json_encode($rt)], ['id'=>$msgId])){
            log_message('error', __FUNCTION__.', 更新模版消息状态, INIT=>SUCC,失败, $msgId: '. print_r($msgId, true));
        }
        log_message('info', __FUNCTION__.', push template msg succ, templateId: '.$templateId.', params: '. json_encode($params, JSON_UNESCAPED_UNICODE));
        return $rt;
    }
    
    /**
     * 获取自定义菜单列表
     * @return array or boolean
     */
    public static function getMenuConf(){
        return self::request('get_current_selfmenu_info');
    }
    
    /**
     * 获取自定义菜单列表
     * @return array or boolean
     */
    public static function getMenuList(){
        return self::request('menu/get');
    }
    
    /**
     * 创建自定义菜单
     * @param array $menu 菜单数据
     * @return array or boolean
     */
    public static function updateMenu($menu){
        return self::request('menu/create', $menu);
    }
    
    /**
     * 删除自定义菜单
     * @return array or boolean
     */
    public static function deleteMenu(){
        return self::request('menu/delete');
    }
    
    /**
     * 创建个性化菜单
     * @param array $menu 菜单数据
     * @return array or boolean
     */
    public static function updateConditional($menu){
        return self::request('menu/addconditional', $menu);
    }
    
    /**
     * 删除个性化菜单
     * @param string $menuId 需要删除的菜单id
     * @return array or boolean
     */
    public static function deleteConditional($menuId){
        return self::request('menu/delconditional', ['menuid'=>$menuId]);
    }
    
    /**
     * 测试个性化菜单
     * @param string $userId 需要匹配的用户openid
     * @return array or boolean
     */
    public static function tryConditional($userId){
        return self::request('menu/trymatch', ['user_id'=>$userId]);
    }
    
    /**
     * 查询标签列表
     * @return array or boolean
     */
    public static function getTags(){
        return self::request('tags/get');
    }
    
    /**
     * 创建标签
     * @param string $name 标签名字
     * @return array or boolean
     */
    public static function createTag($name){
        return self::request('tags/create', ['tag'=>['name'=>$name]]);
    }
    
    /**
     * 更新标签
     * @param string $tagId 标签id
     * @param string $name 标签名字
     * @return array or boolean
     */
    public static function updateTag($tagId, $name){
        return self::request('tags/update', ['tag'=>['name'=>$name, 'id'=>$tagId]]);
    }
    
    /**
     * 删除标签
     * @param string $tagId 标签id
     * @return array or boolean
     */
    public static function deleteTag($tagId){
        return self::request('tags/delete', ['tag'=>['id'=>$tagId]]);
    }
    
    /**
     * 查询标签下的用户
     * @param string $tagId 标签id
     * @param array $nextOpenId 下一个粉丝openid
     * @return array or boolean
     */
    public static function getTagUsers($tagId, $nextOpenId=null){
        $params = ['tagid'=>$tagId];
        !is_null($nextOpenId) && $params['next_openid'] = $nextOpenId;
        return self::request('usr/tag/get', $params);
    }
    
    /**
     * 批量为用户打标签
     * @param string $tagId 标签id
     * @param array $openidList 粉丝openid列表
     * @return array or boolean
     */
    public static function batchTagging($tagId, $openidList){
        return self::request('tags/members/batchtagging', ['tagid'=>$tagId, 'openid_list'=>$openidList]);
    }
    
    /**
     * 批量为用户取消标签
     * @param string $tagId 标签id
     * @param array $openidList 粉丝openid列表
     * @return array or boolean
     */
    public static function batchUntagging($tagId, $openidList){
        return self::request('tags/members/batchuntagging', ['tagid'=>$tagId, 'openid_list'=>$openidList]);
    }
    
    /**
     * 查询用户的标签
     * @param string $openId 粉丝的openid
     * @return array or boolean
     */
    public static function getUserTags($openId){
        return self::request('tags/getidlist', ['openid'=>$openId]);
    }
    
    /**
     * 设置用户的备注名
     * @param string $openId 粉丝的openid
     * @param string $remark 粉丝的备注名
     * @return array or boolean
     */
    public static function updateRemark($openId, $remark){
        return self::request('/user/info/update_remark', ['openid'=>$openId, 'remark'=>$remark]);
    }
    
    /**
     * 获取用户基本信息（包括UnionID机制）
     * @param string $openId 粉丝的openid
     * @param string $lang 返回语言
     * @return array or boolean
     */
    public static function getUserInfo($openId, $lang='zh_CN'){
        $access_token = Wechat_MsgModel::getAccessToken();
        if(!$access_token){
            return false;
        }
        
        $args = ['url'=>sprintf(Yaf_Registry::get('WECHAT_API_HOST').'/cgi-bin/user/info?access_token=%s&openid=%s&lang=%s', $access_token['access_token'], $openId, $lang)];
        $rt = http($args);
        
        if(isset($rt['errcode']) && $rt['errcode']>0){
            $rt['errmsg'] = isset(self::$_error[$rt['errcode']]) ? self::$_error[$rt['errcode']] : $rt['errmsg'];
            log_message('error', __FUNCTION__.', get data from wechat failed, msg: '. print_r($rt, true));
            return false;
        }
        
        return $rt;
    }
    
    /**
     * 批量获取用户基本信息（包括UnionID机制）
     * @param string $userList 粉丝列表
     * @return array or boolean
     */
    public static function batchGetUserInfo($userList){
        return self::request('user/info/batchget', ['user_list'=>$userList]);
    }
    
    /**
     * 获取用户列表
     * @param string $nextOpenId 下一个粉丝的openid
     * @return array or boolean
     */
    public static function getUsers($nextOpenId){
        return self::request('user/get', $nextOpenId ? ['next_openid'=>$nextOpenId] : []);
    }
    
    /**
     * 获取黑名单列表
     * @param string $beginOpenid 下一个粉丝的openid
     * @return array or boolean
     */
    public static function getBlackList($beginOpenid){
        return self::request('tags/members/getblacklist', $beginOpenid ? ['begin_openid'=>$beginOpenid] : []);
    }
    
    /**
     * 批量拉黑用户
     * @param string $openIdList 粉丝的openid列表
     * @return array or boolean
     */
    public static function batchBlackList($openIdList){
        return self::request('tags/members/batchblacklist', ['openid_list'=>$openIdList]);
    }
    
    /**
     * 批量取消拉黑用户
     * @param string $openIdList 粉丝的openid列表
     * @return array or boolean
     */
    public static function batchUnblackList($openIdList){
        return self::request('tags/members/batchunblacklist', ['openid_list'=>$openIdList]);
    }
    
    /**
     * 获取二维码
     * @param array $params 获取二维码的数据
     * @return array or boolean
     */
    public static function getQrcode($params){
        return self::request('qrcode/create', $params);
    }
    
    /**
     * 长连接转短连接
     * @param array $url 原始链接
     * @return array or boolean
     */
    public static function long2Short($url){
        return self::request('shorturl', ['action'=>'long2short', 'long_url'=>$url]);
    }
    
    /**
     * 获取永久素材的列表。
     * @param string $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int offset 分页.偏移量
     * @param int length 分页.每页记录数
     * @return array or boolean
     */
    public static function batchGetMaterial($type, $offset, $length){
        return self::request('material/batchget_material', ['type'=>$type, 'offset'=>$offset, 'count'=>$length]);
    }
    
    /**
     * 获取永久素材的总数。
     * @return array or boolean
     */
    public static function getMaterialCount(){
        return self::request('material/get_materialcount', [], 'GET');
    }
    
    /**
     * 获取永久素材。
     * @param string $mediaId 素材id
     * @return array or boolean
     */
    public static function getMaterial($mediaId){
        return self::request('material/get_material', ['media_id'=>$mediaId]);
    }
    
    /**
     * 获取临时素材。
     * @param string $mediaId 素材id
     * @return array or boolean
     */
    public static function getMedia($mediaId){
        return self::request('media/get', ['media_id'=>$mediaId], 'GET');
    }
    
    /**
     * 添加临时素材。
     * @param string $type 媒体文件类型，分别有图片image、语音voice、视频video和缩略图thumb
     * @param string $media 上传的文件
     * @return array or boolean
     */
    public static function uploadMedia($type, $media){
        return self::request('media/upload', $media, 'UPLOAD', ['type'=>$type]);
    }
    
    /**
     * 添加永久图文素材。
     * @param array $params 素材数据
     * @return array or boolean
     */
    public static function addNews($params){
        return self::request('material/add_news', ['articles'=>$params]);
    }
    
    /**
     * 上传图文消息内的图片获取URL。
     * @param string $file 上传的文件
     * @return array or boolean
     */
    public static function uploadImg($file){
        return self::request('media/uploadimg', ['media'=>$file], 'UPLOAD');
    }
    
    /**
     * 上传其他素材。
     * @param string $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param string $file 上传的文件
     * @return array or boolean
     */
    public static function uploadOther($type, $file){
        return self::request('material/add_material', ['type'=>$type, 'media'=>$file], 'UPLOAD');
    }
    
    /**
     * 上传视频素材。
     * @param string $title 视频素材的标题
     * @param string $introduction 视频素材的描述
     * @param string $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param string $file 上传的文件
     * @return array or boolean
     */
    public static function uploadVideo($title, $introduction, $type, $file){
        return self::request('material/add_material', ['title'=>$title, 'introduction'=>$introduction, 'type'=>$type, 'media'=>$file], 'UPLOAD');
    }
    
    /**
     * 删除永久素材。
     * @param string $mediaId 素材id
     * @return array or boolean
     */
    public static function delMaterial($mediaId){
        return self::request('material/del_material', ['media_id'=>$mediaId]);
    }
    
    /**
     * 修改永久图文素材。
     * @param array $params 素材数据
     * @return array or boolean
     */
    public static function updateNews($params){
        return self::request('material/update_news', $params);
    }
}