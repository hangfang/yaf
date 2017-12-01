<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @todo 销售中心
 */
class UserController extends WechatController {
    public function init(){
        parent::init();
        if(empty($_SESSION['user_type']) || $_SESSION['user_type']!='seller' || empty($_SESSION['user'])){
            lExit(502, '账号角色非法');
        }
    }
    
    /**
     * @todo 销售中心首页
     */
    public function indexAction(){
        if(count($_SESSION['user']['list'])===1){
            $_SESSION['user'] = array_merge($_SESSION['user'], $_SESSION['user']['list'][0]);
        }
        
        $_SESSION['user_type'] = 'seller';
        
        lExit($_SESSION);
        return false;
    }
    
    /**
     * @todo 总绑定的员工列表里，选择一个默认账号
     * @param string user_id 员工id
     * @author fanghang@fujiacaifu.com
     */
    public function selectAction(){
        $userId = BaseModel::getPost('user_id');
        if(empty($userId)){
            lExit('wechat.userIdEmpty', '员工id不能为空');
        }
        
        if(empty($_SESSION['user']['list'])){
            lExit('wechat.userListEmpty', '请先绑定员工账号');
        }
        
        if(count($_SESSION['user']['list'])===1){
            $_SESSION['user'] = array_merge($_SESSION['user'], $_SESSION['user']['list'][0]);
            lExit();
        }
        
        foreach($_SESSION['user']['list'] as $_user){
            if($_user['user_id']==$userId){
                $_SESSION['user'] = array_merge($_SESSION['user'], $_user);
            }
        }
        
        lExit(isset($_SESSION['user']['user_id']) ? [] : ['wechat.selectUserFailed', '选择账号失败']);
    }
}