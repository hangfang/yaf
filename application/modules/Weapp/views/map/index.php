<?php 
defined('BASE_PATH') OR exit('No direct script access allowed');
include BASE_PATH.'/application/views/common/weui/header.php';
?>
</div>
<link rel='stylesheet' href='/static/public/css/map/index.css?v=20160426'/>
<div id="top-panel">
    <div class="weui_cells">
        <div class="weui_cell_bd weui_cell_primary">
            <input class="weui_input" type="text" placeholder="搜地点、查公交、找线路" maxlength="256" id="keyword">
            <input type="button" value="搜索" class="weui_btn weui_btn_mini weui_btn_primary" id="search" style="display:none;">
            <input type="hidden" value="<?php echo $data['clientIP'];?>" id="client_ip">
        </div>
    </div>
    <div class="weui_panel weui_panel_access" id="search-panel">
    </div>
    <div class="weui_panel weui_panel_access" id="info-panel">
    </div>
    <div class="weui_panel weui_panel_access" id="busline-panel">
    </div>
</div>
<script src="http://api.map.baidu.com/api?v=2.0&ak=26587903c740a20817cd121881dc5dcc"></script>
<!--加载鼠标绘制工具-->
<script type="text/javascript" src="http://api.map.baidu.com/library/DrawingManager/1.4/src/DrawingManager_min.js"></script>
<link rel="stylesheet" href="http://api.map.baidu.com/library/DrawingManager/1.4/src/DrawingManager_min.css" />
<!--加载检索信息窗口-->
<script type="text/javascript" src="http://api.map.baidu.com/library/SearchInfoWindow/1.4/src/SearchInfoWindow_min.js"></script>
<link rel="stylesheet" href="http://api.map.baidu.com/library/SearchInfoWindow/1.4/src/SearchInfoWindow_min.css" />
<script src="/static/weui/js/jweixin-1.1.0.js?v=2016-04-07"></script>
<script>
    var openInWechat = navigator.userAgent.toLowerCase().match(/MicroMessenger/i)=="micromessenger" ? true : false;
    if(openInWechat){
        wx.config({
            debug: <?php echo $data['debug']; ?>, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: '<?php echo $data['appid']; ?>', // 必填，公众号的唯一标识
            timestamp: <?php echo $data['timestamp']; ?>, // 必填，生成签名的时间戳
            nonceStr: '<?php echo $data['nonceStr']; ?>', // 必填，生成签名的随机串
            signature: '<?php echo $data['signature']; ?>', // 必填，签名，见附录1
            jsApiList: ['chooseImage','startRecord','stopRecord','playVoice','pauseVoice','stopVoice','hideMenuItems','showAllNonBaseMenuItem','hideAllNonBaseMenuItem','showOptionMenu','hideOptionMenu','scanQRCode','closeWindow', 'getNetworkType', 'openLocation', 'getLocation','translateVoice'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
    }
</script>
<script src="/static/public/js/map/index.js?v=2016-04-07"></script>
<?php include BASE_PATH.'/application/views/common/weui/footer.php';?>
