<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @todo 客户中心
 */
class MemberController extends WechatController {
    public function init(){
        parent::init();
        if(empty($_SESSION['user_type']) || $_SESSION['user_type']!='vip' || empty($_SESSION['member'])){
            lExit(502, '账号角色非法');
        }
    }
    
    /**
     * @todo 客户中心首页
     */
    public function indexAction(){
        if(count($_SESSION['member']['list'])===1){
            $_SESSION['member'] = array_merge($_SESSION['member'], $_SESSION['member']['list'][0]);
        }
        
        lExit($_SESSION);
        return false;
    }
    
    /**
     * @todo 从绑定的会员列表里，选择一个默认账号
     * @param string vip_id 会员id
     * @author fanghang@fujiacaifu.com
     */
    public function selectAction(){
        $vipId = BaseModel::getPost('vip_id');
        if(empty($vipId)){
            lExit('wechat.vipIdEmpty', '会员id不能为空');
        }
        
        if(empty($_SESSION['member']['list'])){
            lExit('wechat.memberListEmpty', '请先绑定会员账号');
        }
        
        if(count($_SESSION['member']['list'])===1){
            $_SESSION['member'] = array_merge($_SESSION['member'], $_SESSION['member']['list'][0]);
            lExit();
        }

        foreach($_SESSION['member']['list'] as $_member){
            if($_member['vip_id']==$vipId){
                $_SESSION['member'] = array_merge($_SESSION['member'], $_member);
            }
        }
        
        lExit(isset($_SESSION['member']['vip_id']) ? [] : ['wechat.selectMemberFailed', '选择账号失败']);
    }
    
    /**
     * @todo 查询我消费的订单列表
     * @param string client_id 企业id
     * @param string shop_id 店铺id
     * @param string start_date 开始日期
     * @param string end_date 戒指日期
     * @param string page 分页.当前页码
     * @param string length 分页.每页记录数
     * @author fanghang@fujiacaifu.com
     */
    public function getBillListAction(){
        //从g3.shop_retail查询
        $where = ['openid'=>$_SESSION['wechat']['openid']];
        $clientId = BaseModel::getPost('client_id');
        if($clientId){
            !in_array($clientId, array_column($_SESSION['member']['list'], 'client_id')) && lExit(502, '企业ID非法');
            $where['client_id'] = $clientId;
        }
        
        $shopId = BaseModel::getPost('shop_id');
        if($shopId && !in_array($shopId, array_column($_SESSION['member']['list'], 'shop_id'))){
            lExit(502, '门店ID非法');
        }
        
        $clientsVipsRef = Operation_ClientsVipsRefModel::getList($where, 'client_id');
        $billList = [];
        foreach($clientsVipsRef as $_clients){
            BaseModel::setDomain(BaseModel::id2Domain($_clients['client_id']));
            $where = ['bill_card'=>array_column($_SESSION['member']['list'], 'memberId')];
            if($shopId){
                $where['shop_id'] = $shopId;
            }
            
            $startDate = BaseModel::getPost('start_date');
            if($startTime){
                $where['bill_time1>='] = strtotime($startDate);
            }
            
            $endDate = BaseModel::getPost('end_date');
            if($endDate){
                $where['bill_time1<='] = strtotime($endDate.' 23:59:59');
            }
            
            $list = G3_ShopRetailModel::getList($where, 'bill_id,bill_time1,bill_money,bill_shop,bill_type');
            if($list){
                $billIds = array_column($list, 'bill_id');
                $sellList = G3_ShopRetailChildSellModel::getList(['bill_id'=>$billIds], 'bill_id,goods_id,goods_money_c');
                $id2goods = G3_BaseGoodsModel::getIndexedList(['goods_id'=>array_column($sellList, 'goods_id')], 'goods_id', 'goods_id,goods_name');
                foreach($sellList as &$_sell){

                    $_list = array_merge($_sell, $id2goods[$_sell['goods_id']][0]);
                    if(!isset($id2List[$_list['bill_id']])){
                        $id2List[$_list['bill_id']] = [];
                    }
                    
                    $_billId = $_list['bill_id'];
                    unset($_list['bill_id']);
                    $id2List[$_billId][] = $_list;
                }
                
                foreach($list as &$_list){
                    $_list['client_id'] = $_clients['client_id'];
                    $_list['sell'] = isset($id2List[$_list['bill_id']]) ? $id2List[$_list['bill_id']] : [];
                }
                //$list['old'] = G3_ShopRetailChildOldModel::getList(['bill_id'=>$billIds], 'bill_id');
                $billList = array_merge($billList, $list);
            }
        }
        
        array_multisort(array_column($billList, 'bill_time1'), SORT_DESC, $billList);
        
        $page = intval(BaseModel::getPost('page'));
        $page = $page<=1 ? 1 : $page;
        
        $length = intval(BaseModel::getPost('length'));
        $length = $length<=0 ? 10 : $length;
        $offset = ($page-1)*$length;

        $result = ['total'=>count($billList), 'list'=>array_slice($billList, $offset, $length)];
        lExit($result);
    }
    
    /**
     * @todo 查询订单详情
     * @param string bill_id 单据id
     * @param string client_id 企业id
     */
    public function getBillDetailAction(){
        //从g3.shop_retail_child_sell查询
        $clientId = BaseModel::getPost('client_id');
        if(!in_array($clientId, array_column($_SESSION['member']['list'], 'client_id'))){
            lExit(502, '企业ID非法');
        }
        
        $billId = BaseModel::getPost('bill_id');
        if(!$billId){
            lExit(502, '单据ID非法');
        }
        
        BaseModel::setDomain(BaseModel::id2Domain($clientId));
        $shopRetail = G3_ShopRetailModel::getRow(['bill_id'=>$billId, 'bill_shop'=>array_column($_SESSION['member']['list'], 'shop_id'), 'bill_card'=>array_column($_SESSION['member']['list'], 'memberId')], 'bill_id,bill_time1,bill_money,bill_shop,bill_type');
        if(!$shopRetail){
            lExit(504, '单据不存在');
        }
        
        $sellList = G3_ShopRetailChildSellModel::getList(['bill_id'=>$billId], 'bill_id,goods_id,goods_money_c');
        if(!$sellList){
            lExit($shopRetail);
        }
        $goodsIds = array_column($sellList, 'goods_id');
        
        $id2goods = G3_BaseGoodsModel::getIndexedList(['goods_id'=>$goodsIds], 'goods_id', 'goods_id,goods_name,goods_stone_weight,goods_stone_weight_unit,goods_gold_weight,goods_gold_weight_unit,goods_certificate');
        $id2photo = G3_BaseGoodsPhotoModel::getIndexedList(['goods_id'=>$goodsIds], 'goods_id', 'goods_id,file_id', '0,1', 'is_cover desc, photo_time desc');
        $id2physical = G3_BaseGoodsPhysicalModel::getIndexedList(['goods_id'=>$goodsIds], 'goods_id', 'goods_id,physical_shape,physical_color,physical_clarity,physical_cut,physical_fluorescenceDegree,physical_polish,physical_symmetry');
        
        foreach($sellList as &$_sell){
            $_sell = array_merge($_sell, $id2goods[$_sell['goods_id']][0]);
            
            if(!empty($id2physical[$_sell['goods_id']][0])){
                $_physical = $id2physical[$_sell['goods_id']][0];
            }else{
                $_physical = ['physical_shape'=>'','physical_color'=>'','physical_clarity'=>'','physical_cut'=>'','physical_fluorescenceDegree'=>'','physical_polish'=>'','physical_symmetry'=>''];
            }
            $_sell = array_merge($_sell, $_physical);
            $_sell['goods_photo'] = empty($id2photo[$_sell['goods_id']][0]['file_id']) ? '' : OSS_URL.$id2photo[$_sell['goods_id']][0]['file_id'];
        }
        
        $shopRetail['sell'] = $sellList;
        lExit($shopRetail);
    }
    
    /**
     * @todo 查询我的会员卡列表
     */
    public function getVipListAction(){
        //从operation.clients_vips_ref查询
        $vipList = [];
        foreach($_SESSION['member']['list'] as $_vip){
            $tmp = [];
            $tmp['shop_name'] = $_vip['shop']['shop_name'];
            $tmp['company_name'] = $_vip['client']['name'];
            $tmp['memberType'] = $_vip['memberType'];
            $tmp['memberMobile'] = $_vip['memberMobile'];

            BaseModel::setDomain(BaseModel::id2Domain($_vip['client_id']));
            $tmp = array_merge($tmp, $this->getBrandInfo($_vip['client_id']));
            
            $vipList[] = $tmp;
        }
        
        lExit($vipList);
    }
    
    /**
     * @todo 查询会员卡详情
     * @param string client_id 企业id
     * @param string member_mobile 会员手机号
     */
    public function getVipDetailAction(){
        //从g3.mms_member查询
        $clientId = BaseModel::getPost('client_id');
        if(!in_array($clientId, array_column($_SESSION['member']['list'], 'client_id'))){
            lExit(502, '企业ID非法');
        }
        
        $memberMobile = BaseModel::getPost('member_mobile');
        if(!in_array($memberMobile, array_column($_SESSION['member']['list'], 'memberMobile'))){
            lExit(502, '会员卡不存在');
        }
        
        $mms = [];
        BaseModel::setDomain(BaseModel::id2Domain($clientId));
        foreach($_SESSION['member']['list'] as $_vip){
            if($clientId== $_vip['client_id'] && $memberMobile==$_vip['memberMobile']){
                $mms = $_vip;
                $mms['company_name'] = $_vip['client']['name'];
                $mms['shop_name'] = $_vip['shop']['shop_name'];
                $mms = array_merge($mms, $this->getBrandInfo($mms['memberShop']));
                break;
            }
        }
        
        lExit($mms);
    }
    
    /**
     * @todo 查询质保单
     * @param string client_id 企业id
     * @param string bill_id 单据id
     */
    public function getWarrantySheetAction(){
        $clientId = BaseModel::getPost('client_id');
        if(!in_array($clientId, array_column($_SESSION['member']['list'], 'client_id'))){
            lExit(502, '企业ID非法');
        }
        
        $billId = BaseModel::getPost('bill_id');
        if(!$billId){
            lExit(502, '单据id非法');
        }
        
        BaseModel::setDomain(BaseModel::id2Domain($clientId));
        $retail = G3_ShopRetailModel::getRow(['bill_id' => $billId, 'bill_type' => 'S', 'bill_card'=>array_column($_SESSION['member']['list'], 'memberId')], 'bill_id,bill_shop,bill_date,seller_main,bill_smoney,bill_money');
        if (empty($retail)) {
            lExit('', '您输入的[' . $billId . ']未能查询到销售记录!');
        }
        
        $retailChild = array_column(G3_ShopRetailChildSellModel::getList(['bill_id' => $billId]), null, 'goods_id');
        if (empty($retailChild)) {
            lExit('', '您输入的[' . $billId . ']未能查询到销售记录!');
        }
        
        $memberInfo = G3_MmsMemberModel::getRow(['memberId' => array_column($retailChild, 'memberId')], 'memberId,memberName');
        $goodsInfo = G3_BaseGoodsModel::getList(['goods_id' => array_column($retailChild, 'goods_id')], 'goods_id,goods_name,goods_code,goods_weight,goods_certificate,goods_sale,goods_brand,goods_bar');
        $userInfo = G3_BaseUserModel::getRow(['user_id' => $retail['seller_main']], 'user_id,user_name');
        $shopInfo = G3_BaseShopModel::getRow(['shop_id' => $retail['bill_shop']], 'shop_id,shop_name,shop_phone,shop_address');
        $retailGoods = [];
        $brandList = array_column(G3_BaseBrandModel::getList(['brand_id' => array_column($goodsInfo, 'goods_brand')]), null, 'brand_id');
        foreach ($goodsInfo as $good) {
            $retailGoods[] = array(
                'goods_certificate' => $good['goods_certificate'],//证书号
                'goods_bar' => $good['goods_bar'],//条码
                'goods_name' => $good['goods_name'],//产品名字
                'goods_weight' => $good['goods_weight'],//产品重量
                'goods_brand' => $brandList[$good['goods_brand']]['brand_name'],//品牌
                'goods_money_o' => $retailChild[$good['goods_id']]['goods_money_o'],//原价
                'goods_money_c' => $retailChild[$good['goods_id']]['goods_money_c'],//实售价
            );
        }
        $result = array(
            'bill_date' => $retail['bill_date'],//日期
            'memberName' => $memberInfo['memberName'],//会员名字
            'memberMobile' => $memberInfo['memberMobile'],//手机号码
            'seller_main' => $userInfo['user_name'],//销售员
            'bill_money_o' => array_sum(array_column($retailChild, 'goods_money_o')),//原价
            'bill_smoney' => $retail['bill_smoney'],//实际售价
            'bill_money' => $retail['bill_money'],//应收金额
            'bill_money_word' => ToolsModel::num2rmb($retail['bill_money']),//应收金额
            'shop_name' => $shopInfo['shop_name'],//店名
            'shop_phone' => $shopInfo['shop_phone'],//门店电话
            'shop_address' => $shopInfo['shop_address'],//门店地址
            'goods_info' => $retailGoods
        );
        lExit($result);
    }
    
    /**
     * 查询会员卡的所属门店的经营品牌信息
     * @param string $shopId 门店id
     * @return array
     * @author fanghang@fujiacaifu.com
     */
    private function getBrandInfo($shopId){
        $result = ['brand_code'=>'', 'brand_name'=>'', 'brand_adress'=>''];
        if($shopId==G3_BaseShopModel::BASE_SHOP_ID){//如果是总店，则取总店的经营品牌
            $brandIds = explode(',', $_shop['manageBrands']);
        }else{
            $_shop = G3_BaseShopModel::getRow(['shop_id'=>G3_BaseShopModel::BASE_SHOP_ID], 'manageBrands');
            if(!empty($_shop['manageBrands'])){//总店，优先取总店的经营品牌
                $brandIds = explode(',', $_shop['manageBrands']);
            }else{//如果总店未设置经营品牌，则取分店的经营品牌
                $_shop = G3_BaseShopModel::getRow(['shop_id'=>$shopId], 'manageBrands');
                !empty($_shop['manageBrands']) && $brandIds = explode(',', $_shop['manageBrands']);
            }
        }

        $where = [];
        !empty($brandIds) && $where['brand_id'] = $brandIds;
        $brand = G3_BaseBrandModel::getRow($where, 'brand_code,brand_name', 'brand_id asc');
        if(!$brand){
            return $result;
        }
        $result = $brand;
        
        $brandDic = Dictionary_BrandsModel::getRow(['id'=>$brand['brand_code']], 'address');
        $brandDic && $result['brand_adress'] = BASE_URL.'/logo/'.$brandDic['address'];
        return $result;
    }
}