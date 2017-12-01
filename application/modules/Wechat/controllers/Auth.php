<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @todo 微信公众号授权登录
 */
class AuthController extends WechatController {
    /**
     * @todo 获得微信openid后，来此识别身份
     * @author fanghang@fujiacaifu.com
     */
    public function indexAction(){
        $url = BaseModel::getQuery('redirect_uri');

        if(isset($_SESSION['user']) && $_SESSION['user_type']=='seller'){
            $url = $url ? $url : '/wechat/user/index';
            header('location: '.$url);exit;
        }else if(isset($_SESSION['member']) && $_SESSION['user_type']=='vip'){
            $url = $url ? $url : '/wechat/member/index';
            header('location: /wechat/member/index');exit;
        }
        
        /*---start---查询会员---start---*/
        $members = Operation_ClientsVipsRefModel::getList(['openid'=>$_SESSION['wechat']['openid']], 'shop_id,client_id,vips_id,vip_id,vip_type');
        if($members){
            $id2vip = Operation_ClientsVipsModel::getIndexedList(['id'=>array_column($members, 'vips_id')], 'id', 'id,vip_mobile');
            $clientIds = array_unique(array_column($members, 'client_id'));
            $shopIds = array_unique(array_column($members, 'shop_id'));
            $companys = Operation_ClientsCompanyModel::getIndexedList(['client_id'=>$clientIds], 'client_id');
            $shops = Operation_ClientsShopsModel::getList(['client_id'=>$clientIds, 'shop_id'=>$shopIds], 'client_id,shop_id,shop_name,shop_phone,shop_phone400,shop_address');
            
            $memberList = [];echo 111;
            foreach($members as $_member){
                BaseModel::setDomain(BaseModel::id2Domain($_member['client_id']));
                $tmp = G3_MmsMemberModel::getRow(['memberMobile'=>$id2vip[$_member['vips_id']][0]['vip_mobile']], 'memberId,memberCode,memberName,memberMobile,memberShop,memberCreate,memberType,memberIntegral,memberItgRetail');
                if(!$tmp){
                    continue;
                }
                
                $tmp['client_id'] = $_member['client_id'];
                $tmp['shop_id'] = $_member['shop_id'];
                $tmp['client'] = $companys[$_member['client_id']][0];
                foreach($shops as $_shop){
                    if($_shop['shop_id']==$_member['shop_id'] && $_shop['client_id']==$_member['client_id']){
                        $tmp['shop'] = $_shop;
                        break;
                    }
                }
                $memberList[] = $tmp;
            }
            $_SESSION['member']['list'] = $memberList;
        }
        /*---end---查询会员---end---*/
        
        /*---start---查询员工---start---*/
        $users = Operation_ClientsUsersModel::getList(['openid'=>$_SESSION['wechat']['openid']]);//上线前，clients_users须添加openid字段
        if($users){
            $clientIds = array_unique(array_column($users, 'client_id'));
            $shopIds = array_unique(array_column($users, 'shop_id'));
            $companys = Operation_ClientsCompanyModel::getIndexedList(['client_id'=>$clientIds], 'client_id');//查询公司信息
            $shops = Operation_ClientsShopsModel::getList(['client_id'=>$clientIds, 'shop_id'=>$shopIds]);
            foreach($users as &$_user){
                $_user['client'] = $companys[$_user['client_id']][0];
                foreach($shops as $_shop){
                    if($_shop['shop_id']==$_user['shop_id'] && $_shop['client_id']==$_user['client_id']){
                        $_user['shop'] = $_shop;
                        break;
                    }
                }
            }
            $_SESSION['user']['list'] = $users;
        }
        /*---end---查询员工---end---*/
        
        if($members && $users){
            $url = $url ? $url : '/wechat/auth/select';
            header('location: '.$url);exit;
        }
        
        if($members){
            $_SESSION['user_type'] = 'vip';
            $url = $url ? $url : '/wechat/member/index';
            header('location: '.$url);exit;
        }
        
        if($users){
            $_SESSION['user_type'] = 'seller';
            $url = $url ? $url : '/wechat/user/index';
            header('location: '.$url);exit;
        }
        
        $url = $url ? $url : '/wechat/auth/bind';
        header('location: '.$url);exit;
    }
    
    /**
     * @todo 微信登录入口
     * @author fanghang@fujiacaifu.com
     */
    public function loginAction(){
        $url = '/wechat/auth/index';
        if($tmp=BaseModel::getQuery('redirect_uri')){
            $url = $tmp;
        }
        
        if(!empty($_SESSION['wechat']['openid'])){//去到用户中心，此时通过openid查询到客户信息
            header('location: '.$url);exit;
        }

        //微信授权登录
        header('location: '.sprintf(Yaf_Registry::get('WECHAT_OPEN_HOST').'/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect', Yaf_Registry::get('WECHAT_APP_ID'), urlencode(BASE_URL.'/wechat/auth/code?redirect_uri='.$url)));exit;
    }
    
    /**
     * @todo 公众号授权页面，回调到此，返回code
     * @author fanghang@fujiacaifu.com
     */
    public function codeAction(){
        $code = BaseModel::getQuery('code');
        $state = BaseModel::getQuery('state');
        
        $url = '/wechat/auth/index';
        $tmp = BaseModel::getQuery('redirect_uri');
        if($tmp && strpos($tmp, $url)===false){
            $url .= '?redirect_uri='.urlencode($tmp);
        }
        
        $result = $this->getAccessToken($code, $state);
        $_SESSION['wechat']['access_token'] = $result['access_token'];
        $_SESSION['wechat']['access_token_time'] = time()-50;//防止刚好7200秒，导致token过期
        $_SESSION['wechat']['refresh_token'] = $result['refresh_token'];
        $_SESSION['wechat']['refresh_token_time'] = time()-50;//30天内有效，用来刷新access_token
        $_SESSION['wechat']['openid'] = $result['openid'];
        $_SESSION['wechat']['unionid'] = $result['unionid'];

        header('location: '.$url);exit;
    }
    
    /**
     * @todo 绑定账号：会员or销售
     * @param string type 绑定类型vip/seller (vip)
     * @param string mobile 手机号码
     * @param string password 密码 (123456)
     * @author fanghang@fujiacaifu.com
     */
    public function bindAction(){
        $type = BaseModel::getPost('type');
        if(!in_array($type, ['vip', 'seller'])){
            lExit('绑定类型错误，必须为会员、店员二选一');
        }
        
        $mobile = BaseModel::getPost('mobile');
        if(!preg_match(PHONE_REG, $mobile)){
            lExit('手机号码错误');
        }
        
        $password = BaseModel::getPost('password');
        if(empty($password)){
            lExit('密码不能为空');
        }
        
        if($type==='vip'){
            $clientsVips = Operation_ClientsVipsModel::getRow(['vip_mobile'=>$mobile]);
            if(!$clientsVips){
                lExit('客户不存在');
            }
            
            $clientsVipsRef = Operation_ClientsVipsRefModel::getList(['vips_id'=>$clientsVips['id'], 'vip_name'=>$password], 'vip_name');
            if(!$clientsVipsRef){
                lExit('客户名称验证失败');
            }
            
            if(Operation_ClientsVipsRefModel::update(['openid'=>$_SESSION['wechat']['openid']], ['vips_id'=>$clientsVips['id']])===false){
                lExit('会员账号绑定失败');
            }
            
            $_SESSION['user_type'] = $type;
            lExit(0, '会员账号绑定成功');
        }
        
        $clientsTmpUser = Operation_ClientsTmpUsersModel::getRow(['mobile'=>$mobile]);
        if(!$clientsTmpUser){
            lExit('员工不存在');
        }

        if($clientsTmpUser['password']!=md5($password)){
            lExit('员工密码验证失败');
        }

        if(Operation_ClientsUsersModel::update(['openid'=>$_SESSION['wechat']['openid']], ['user_tel'=>$mobile])===false){
            lExit('员工账号绑定失败');
        }

        $_SESSION['user_type'] = $type;
        lExit(0, '员工账号绑定成功');
    }
    
    /**
     * @todo 选择账户角色
     * @param string user_type 角色名字 (seller|vip)
     */
    public function selectAction(){
        $userType = BaseModel::getPost('user_type');
        if(!in_array($userType, ['seller', 'vip'])){
            lExit(502, '角色必须为店员或者会员');
        }
        
        $_SESSION['user_type'] = $userType;
        lExit();
    }
    
    /**
     * @todo 一次性订阅消息.暂时未用到
     */
    public function subscribeAuthAction(){
        //需要每家企业在诸葛到店登记模版id
        //微信授权
        header('location: '.sprintf('https://mp.weixin.qq.com/mp/subscribemsg?action=get_confirm&appid=%s&scene=%s&template_id=%s&redirect_url=%s&reserved=test#wechat_redirect', Yaf_Registry::get('WECHAT_APP_ID'), 1000, 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', urlencode(BASE_URL.'/wechat/auth/subscribemsg')));exit;
    }
    
    /**
     * @todo 发送订阅消息
     * @param string openid 接收者openid
     * @param string template_id 消息模板id
     * @param string action 用户点击动作，”confirm”代表用户确认授权，”cancel”代表用户取消授权
     * @param string scene 订阅场景值
     * @param string reserved 请求带入原样返回
     */
    public function subscribeMsgAction(){
        $params = [];
        $tmp = BaseModel::getQuery('openid');
        if(!$tmp){
            lExit(502, '用户openid非法');
        }
        $params['touser'] = $tmp;
        
        $tmp = BaseModel::getQuery('template_id');
        if(!$tmp){
            lExit(502, '消息模版id非法');
        }
        $params['template_id'] = $tmp;
        
        $tmp = BaseModel::getQuery('action');
        if($tmp!=='confirm'){
            lExit(502, '用户拒绝授权');
        }
        
        $tmp = BaseModel::getQuery('scene');
        if(!$tmp){
            lExit(502, '场景值非法');
        }
        $params['scene'] = $tmp;
        
        $tmp = BaseModel::getQuery('reserved');
        
        $params['url'] = 'http://www.zhugedaodian.com';//点击消息时跳转地址
        $params['title'] = '消息标题AAA';//消息标题
        $params['data'] = [
            'username'  =>  ['value'=>'测试用户', 'color'=>'#ff0000'],
            'time'      =>  ['value'=>'2017-11-28 18:43:50', 'color'=>'#00ff00'],
            'shop_name' =>  ['value'=>'富甲集团', 'color'=>'#0000ff'],
            'money'     =>  ['value'=>'99.95', 'color'=>'#f0f000'],
        ];
        
        $cache = Cache::getInstance('wechat:');
        $cache->lpush('template.msg', json_encode($params));
        lExit();
    }
}