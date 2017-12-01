<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @todo 微信菜单管理
 */
class Manage_MenuController extends BasicController {
    public function init(){
        parent::init();
        if(!Wechat_MsgModel::initDomain()){
            lExit(502, '当前企业未接入微信公众号');
        }
    }
    
    /**
     * @todo 获取微信公众号菜单配置
     */
    public function getConfAction(){
        lExit(Wechat_ApiModel::getMenuConf());
    }
    
    /**
     * @todo 获取微信公众号菜单列表
     */
    public function getListAction(){
        lExit(Wechat_ApiModel::getMenuList());
    }
    
    /**
     * @todo 创建菜单
     * @param string menu[0]['name'] 菜单名称
     * @param string menu[0]['type'] 菜单类型
     * @param string menu[0]['key'] 菜单key
     * @param string menu[0]['url'] 菜单跳转链接
     * @param string menu[0]['appid'] 菜单跳转的小程序id
     * @param string menu[0]['pagepath'] 小程序的页面路径
     * @param string menu[0]['media_id'] 调用新增永久素材接口返回的合法media_id
     * @param string menu[0]['sub_button'] 二级菜单,最多5个
     */
    public function updateAction(){
        $default = [
            [
                "name"=>"扫码", 
                "sub_button"=>[
                    [
                        "type"=>"scancode_waitmsg", 
                        "name"=>"扫码带提示", 
                        "key"=>"rselfmenu_0_0",
                    ], 
                    [
                        "type"=>"scancode_push", 
                        "name"=>"扫码推事件", 
                        "key"=>"rselfmenu_0_1",
                    ]
                ]
            ], 
            [
                "name"=>"发图", 
                "sub_button"=>[
                    [
                        "type"=>"pic_sysphoto", 
                        "name"=>"系统拍照发图", 
                        "key"=>"rselfmenu_1_0",
                    ], 
                    [
                        "type"=>"pic_photo_or_album", 
                        "name"=>"拍照或者相册发图", 
                        "key"=>"rselfmenu_1_1",
                    ], 
                    [
                        "type"=>"pic_weixin", 
                        "name"=>"微信相册发图", 
                        "key"=>"rselfmenu_1_2",
                    ]
                ]
            ],
            [
                "name"=>"发图", 
                "sub_button"=>[
                    [
                        "name"=>"发送位置", 
                        "type"=>"location_select", 
                        "key"=>"rselfmenu_2_0"
                    ],
                    [
                        "type"=>"view", 
                        "name"=>"视频", 
                        "url"=>"http://v.qq.com/", 
                        "sub_button"=>[ ]
                    ], 
                    [
                        "type"=>"click", 
                        "name"=>"赞一下我们", 
                        "key"=>"V1001_GOOD", 
                        "sub_button"=>[ ]
                    ],
//                    [
//                       "type"=>"media_id", 
//                       "name"=>"图片", 
//                       "media_id"=>"MEDIA_ID1"
//                    ], 
//                    [
//                       "type"=>"view_limited", 
//                       "name"=>"图文消息", 
//                       "media_id"=>"MEDIA_ID2"
//                    ]
                ]
            ]
        ];
        $menus = BaseModel::getPost('menu', $default);
        if(empty($menus)){
            lExit(502, '菜单数据格式错误');
        }
        
        $menu = ['button'=>[]];
        foreach($menus as $_menu){
            $button = [];
            foreach($_menu as $_k=>$_v){
                if($_k==='sub_button'){
                    $subButton = [];
                    foreach($_v as $_subButton){
                        $tmp = [];
                        foreach($_subButton as $_subk=>$_subv){
                            $tmp[$_subk] = $_subv;
                        }
                        
                        $subButton[] = $tmp;
                    }
                    $button[$_k] = $subButton;
                    continue;
                }
                $button[$_k] = $_v;
            }
            $menu['button'][] = $button;
        }
        lExit(Wechat_ApiModel::updateMenu($menu));
    }
    
    /**
     * @todo 删除微信公众号菜单
     */
    public function deleteAction(){
        lExit(Wechat_ApiModel::deleteMenu());
    }
    
    /**
     * @todo 创建个性化菜单
     * @param string menu[0]['name'] 菜单名称
     * @param string menu[0]['type'] 菜单类型
     * @param string menu[0]['key'] 菜单key
     * @param string menu[0]['url'] 菜单跳转链接
     * @param string menu[0]['appid'] 菜单跳转的小程序id
     * @param string menu[0]['pagepath'] 小程序的页面路径
     * @param string menu[0]['media_id'] 调用新增永久素材接口返回的合法media_id
     * @param string menu[0]['sub_button'] 二级菜单,最多5个
     * @param string rules['tag_id'] 用户标签的id，可通过用户标签管理接口获取
     * @param string rules['sex'] 性别：男（1）女（2），不填则不做匹配
     * @param string rules['country'] 国家 (中国)
     * @param string rules['province'] 省份 (广东)
     * @param string rules['city'] 城市 (深圳)
     * @param string rules['client_platform_type'] 客户端版本，当前只具体到系统型号：IOS(1), Android(2),Others(3)，不填则不做匹配
     * @param string rules['language'] 语言 (zh_CN)
     */
    public function updateConditionalAction(){
        $default = [
            [
                "name"=>"个性化扫码", 
                "sub_button"=>[
                    [
                        "type"=>"scancode_waitmsg", 
                        "name"=>"扫码带提示", 
                        "key"=>"rselfmenu_0_0",
                    ], 
                    [
                        "type"=>"scancode_push", 
                        "name"=>"扫码推事件", 
                        "key"=>"rselfmenu_0_1",
                    ]
                ]
            ], 
            [
                "name"=>"个性化发图", 
                "sub_button"=>[
                    [
                        "type"=>"pic_sysphoto", 
                        "name"=>"系统拍照发图", 
                        "key"=>"rselfmenu_1_0",
                    ], 
                    [
                        "type"=>"pic_photo_or_album", 
                        "name"=>"拍照或者相册发图", 
                        "key"=>"rselfmenu_1_1",
                    ], 
                    [
                        "type"=>"pic_weixin", 
                        "name"=>"微信相册发图", 
                        "key"=>"rselfmenu_1_2",
                    ]
                ]
            ],
            [
                "name"=>"个性化发图", 
                "sub_button"=>[
                    [
                        "name"=>"发送位置", 
                        "type"=>"location_select", 
                        "key"=>"rselfmenu_2_0"
                    ],
                    [
                        "type"=>"view", 
                        "name"=>"视频", 
                        "url"=>"http://v.qq.com/", 
                        "sub_button"=>[ ]
                    ], 
                    [
                        "type"=>"click", 
                        "name"=>"赞一下我们", 
                        "key"=>"V1001_GOOD", 
                        "sub_button"=>[ ]
                    ],
//                    [
//                       "type"=>"media_id", 
//                       "name"=>"图片", 
//                       "media_id"=>"MEDIA_ID1"
//                    ], 
//                    [
//                       "type"=>"view_limited", 
//                       "name"=>"图文消息", 
//                       "media_id"=>"MEDIA_ID2"
//                    ]
                ]
            ]
        ];
        $menus = BaseModel::getPost('menu', $default);
        if(empty($menus)){
            lExit(502, '菜单数据格式错误');
        }
        
        $rules = [
                //"tag_id"=>"2",//用户标签的id，可通过用户标签管理接口获取
                "sex"=>"1",//性别：男（1）女（2），不填则不做匹配
                "country"=>"中国",
                "province"=>"广东",
                "city"=>"深圳",
                "client_platform_type"=>"1",//客户端版本，当前只具体到系统型号：IOS(1), Android(2),Others(3)，不填则不做匹配
                "language"=>"zh_CN"
            ];
        $rules = BaseModel::getPost('rules', $rules);
        if(empty($rules)){
            lExit(502, '个性化规则不能都为空');
        }
        
        if(!empty($rules['city'])){
            if(empty($rules['province']) || empty($rules['country'])){
                lExit(502, '城市不为空，则国家、省份均不能为空');
            }
        }
        
        if(!empty($rules['province']) && empty($rules['country'])){
            lExit(502, '省份不为空，则国家不能为空');
        }
        
        $menu = ['button'=>[], 'matchrule'=>$rules];
        foreach($menus as $_menu){
            $button = [];
            foreach($_menu as $_k=>$_v){
                if($_k==='sub_button'){
                    $subButton = [];
                    foreach($_v as $_subButton){
                        $tmp = [];
                        foreach($_subButton as $_subk=>$_subv){
                            $tmp[$_subk] = $_subv;
                        }
                        
                        $subButton[] = $tmp;
                    }
                    $button[$_k] = $subButton;
                    continue;
                }
                $button[$_k] = $_v;
            }
            $menu['button'][] = $button;
        }
        
        $menuId = Wechat_ApiModel::updateConditional($menu);
        lExit(isset($menuId['errcode']) ? $menuId : ['menuId'=>$menuId]);
    }
    
    /**
     * @todo 删除微信个性化菜单
     * @param string menuid 个性化菜单id
     */
    public function deleteConditionalAction(){
        $menuId = BaseModel::getPost('menuid');
        if(empty($menuId)){
            lExit(502, '菜单id不能为空');
        }
        lExit(Wechat_ApiModel::deleteConditional($menuId));
    }
    
    /**
     * @todo 测试微信个性化菜单
     * @param string user_id 粉丝的openid或者微信号
     */
    public function tryConditionalAction(){
        $userId = BaseModel::getPost('user_id');
        if(empty($userId)){
            lExit(502, '粉丝的openid或者微信号不能为空');
        }
        lExit(Wechat_ApiModel::tryConditional($userId));
    }
}