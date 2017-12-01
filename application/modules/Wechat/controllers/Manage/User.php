<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @todo 微信用户管理
 */
class Manage_UserController extends BasicController {
    public function init(){
        parent::init();
        if(!Wechat_MsgModel::initDomain()){
            lExit(502, '当前企业未接入微信公众号');
        }
    }
    
    /**
     * @todo 更新用户备注名
     * @param string openid 粉丝的openid
     * @param string remark 粉丝的备注名
     */
    public function updateRemarkAction(){
        $openId = BaseModel::getPost('openid');
        if(empty($openId)){
            lExit(502, '粉丝的openid不能为空');
        }
        
        $remark = BaseModel::getPost('remark');
        if(strlen($remark)>30){
            lExit(502, '新备注名不能大于30字符');
        }
        
        lExit(Wechat_ApiModel::updateRemark($openId, $remark));
    }
    
    /**
     * @todo 查询用户信息
     * @param string openid 粉丝的openid
     * @param string lang 返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
     * @return {
     *      "subscribe": 1, 
     *      "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M", 
     *      "nickname": "Band", 
     *      "sex": 1, 
     *      "language": "zh_CN", 
     *      "city": "广州", 
     *      "province": "广东", 
     *      "country": "中国", 
     *      "headimgurl":  "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
     *      "subscribe_time": 1382694957,
     *      "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
     *      "remark": "",
     *      "groupid": 0,
     *      "tagid_list":[128,2]
     *    }
     */
    public function getUserInfoAction(){
        $openId = BaseModel::getPost('openid');
        if(empty($openId)){
            lExit(502, '粉丝的openid不能为空');
        }
        
        $lang = BaseModel::getPost('lang');//返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
        
        lExit(Wechat_ApiModel::getUserInfo($openId, $lang));
    }
    
    /**
     * @todo 批量查询用户信息
     * @param string user_list[0]['openid'] 粉丝的openid
     * @param string user_list[0]['lang'] 返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
     * @return {"user_info_list":[{
     *      "subscribe": 1, 
     *      "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M", 
     *      "nickname": "Band", 
     *      "sex": 1, 
     *      "language": "zh_CN", 
     *      "city": "广州", 
     *      "province": "广东", 
     *      "country": "中国", 
     *      "headimgurl":  "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
     *      "subscribe_time": 1382694957,
     *      "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
     *      "remark": "",
     *      "groupid": 0,
     *      "tagid_list":[128,2]
     *    },...]
     * }
     */
    public function batchGetUserInfoAction(){
        $userList = BaseModel::getPost('user_list');
        if(empty($userList)){
            lExit(502, '粉丝的列表不能为空');
        }
        
        lExit(Wechat_ApiModel::batchGetUserInfo($userList));
    }
    
    /**
     * @todo 查询用户列表
     * @param string next_openid 第一个拉取的OPENID，不填默认从头开始拉取
     */
    public function getUsersAction(){
        $nextOpenid = BaseModel::getPost('next_openid');
        lExit(Wechat_ApiModel::getUsers($nextOpenid));
    }
    
    /**
     * @todo 获取黑名单列表
     * @param string begin_openid 第一个拉取的OPENID，不填默认从头开始拉取
     */
    public function getBlackListAction(){
        $beginOpenid = BaseModel::getPost('begin_openid');
        lExit(Wechat_ApiModel::getBlackList($beginOpenid));
    }
    
    /**
     * @todo 批量拉黑用户
     * @param string openid_list 粉丝的openid列表
     */
    public function batchBlackListAction(){
        $openIdList = BaseModel::getPost('openid_list');
        if(empty($openIdList)){
            lExit(502, '粉丝的列表不能为空');
        }
        !is_array($openIdList) && $openIdList = explode(',', $openIdList);
        lExit(Wechat_ApiModel::batchBlackList($openIdList));
    }
    
    /**
     * @todo 批量取消拉黑用户
     * @param string openid_list 粉丝的openid列表
     */
    public function batchUnblackListAction(){
        $openIdList = BaseModel::getPost('openid_list');
        if(empty($openIdList)){
            lExit(502, '粉丝的列表不能为空');
        }
        !is_array($openIdList) && $openIdList = explode(',', $openIdList);
        lExit(Wechat_ApiModel::batchUnblackList($openIdList));
    }
}