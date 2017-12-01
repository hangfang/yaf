<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @todo 微信用户标签
 */
class Manage_TagController extends BasicController {
    public function init(){
        parent::init();
        if(!Wechat_MsgModel::initDomain()){
            lExit(502, '当前企业未接入微信公众号');
        }
    }
    
    /**
     * @todo 查询用户标签列表
     */
    public function getTagsAction(){
        lExit(Wechat_ApiModel::getTags());
    }
    
    /**
     * @todo 创建用户标签
     * @param string name 标签名字
     */
    public function createTagAction(){
        $name = trim(BaseModel::getPost('name'));
        if(empty($name)){
            lExit(502, '标签名字不能为空');
        }
        
        if(strlen($name)>30){
            lExit(502, '标签名字不能大于30字符');
        }
        lExit(Wechat_ApiModel::createTag($name));
    }
    
    /**
     * @todo 修改用户标签
     * @param string name 标签名字
     */
    public function updateTagAction(){
        $id = trim(BaseModel::getPost('id'));
        if(empty($id)){
            lExit(502, '标签id不能为空');
        }
        
        $name = trim(BaseModel::getPost('name'));
        if(empty($name)){
            lExit(502, '标签名字不能为空');
        }
        
        if(strlen($name)>30){
            lExit(502, '标签名字不能大于30字符');
        }
        lExit(Wechat_ApiModel::updateTag($id, $name));
    }
    
    /**
     * @todo 删除用户标签
     * @param string name 标签名字
     */
    public function deleteTagAction(){
        $id = trim(BaseModel::getPost('id'));
        if(empty($id)){
            lExit(502, '标签id不能为空');
        }
        lExit(Wechat_ApiModel::deleteTag($id));
    }
    
    /**
     * @todo 查询标签下的用户
     * @param string id 标签id
     * @param string next_openid 第一个拉取的OPENID，不填默认从头开始拉取
     */
    public function getTagUsersAction(){
        $tagId = trim(BaseModel::getPost('id'));
        if(empty($tagId)){
            lExit(502, '标签id不能为空');
        }
        $nextOpenId = trim(BaseModel::getPost('next_openid'));
        
        lExit(Wechat_ApiModel::getTagUsers($tagId, $nextOpenId));
    }
    
    /**
     * @todo 批量为用户打标签
     * @param string id 标签id
     * @param string next_openid 第一个拉取的OPENID，不填默认从头开始拉取
     */
    public function batchTaggingAction(){
        $tagId = trim(BaseModel::getPost('id'));
        if(empty($tagId)){
            lExit(502, '标签id不能为空');
        }
        
        $openidList = BaseModel::getPost('openid');
        if(empty($openidList)){
            lExit(502, '粉丝openid不能为空');
        }
        !is_array($openidList) && $openidList = explode(',', $openidList);
        
        lExit(Wechat_ApiModel::batchTagging($tagId, $openidList));
    }
    
    /**
     * @todo 批量为用户打标签
     * @param string id 标签id
     * @param string next_openid 第一个拉取的OPENID，不填默认从头开始拉取
     */
    public function batchUntaggingAction(){
        $params = [];
        $tmp = trim(BaseModel::getPost('id'));
        if(empty($tmp)){
            lExit(502, '标签id不能为空');
        }
        $params['tagid'] = $tmp;
        
        $tmp = BaseModel::getPost('openid');
        if(empty($tmp)){
            lExit(502, '粉丝openid不能为空');
        }
        !is_array($tmp) && $tmp = explode(',', $tmp);
        $params['openid_list'] = $tmp;
        
        lExit(Wechat_ApiModel::batchUntagging($params));
    }
    
    /**
     * @todo 查询用户的标签列表
     * @param string openid 粉丝的openid
     */
    public function getUserTagsAction(){
        $openId = BaseModel::getPost('openid');
        if(empty($openId)){
            lExit(502, '粉丝openid不能为空');
        }
        
        lExit(Wechat_ApiModel::getUserTags($openId));
    }
}