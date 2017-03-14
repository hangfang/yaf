<?php

//后端接口
$api = array(
    'modify_mobile'             =>  '/account/modify_mobile',//修改预留手机号码
    'query_user_information'    =>  '/account/query_user_information',//查询用户信息
    'personal_unbind_bankcard'  =>  '/account/personal_unbind_bankcard',//解绑银行卡
    'personal_bind_bankcard'    =>  '/account/personal_bind_bankcard',//绑定银行卡
    'reset_password'            =>  '/account/reset_password',//查询用户信息
    'recharge'                  =>  '/account/recharge',//充值
    'withdraw'                  =>  '/account/withdraw',//提现
    'personal_register'         =>  '/account/personal_register',//存管通开户
    'image_upload'              =>  '/image/upload',//上传图片
    'check_login'               =>  '/user/check_login',//检测登录态
    'activate'                  => '/account/user_activate',//用户激活
    
    'query_order_list'          =>  '/account/query_order_list',// 查询交易列表
    'query_order'               =>  '/account/query_order',// 查询交易详情
    'query_change_list'         =>  '/account/query_change_list',// 查询资金变动列表
    'query_change'              =>  '/account/query_change',// 查询资金变动详情
    'query_invest_asset'        =>  '/account/query_invest_asset',// 查询持仓列表
    'query_invest_asset_detail' =>  '/account/query_invest_asset_detail',// 查询持仓交易记录
    
    'query_project_information' =>  '/account/query_project_information',//查询投标信息
    'invest'                    =>  '/account/invest',//投标
);