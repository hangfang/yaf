<?php 
defined('BASE_PATH') OR exit('No direct script access allowed');
include BASE_PATH.'/application/views/common/weui/header.php';
?>
<link rel='stylesheet' href='/static/public/css/map/txmap.css?v=20160426'/>
</div>
<div id="top-panel">
    <div class="weui_cells">
        <div class="weui_cell_bd weui_cell_primary">
            <input class="weui_input" type="text" placeholder="搜地点、查公交、找线路" maxlength="256" id="keyword">
            <input type="button" value="搜索" class="weui_btn weui_btn_mini weui_btn_primary" id="search">
            <input type="hidden" value="<?php echo $data['clientIP'];?>" id="client_ip">
            <input type="hidden" value="深圳市" id="region">
            <input type="hidden" value="1" id="pageIndex">
            <input type="hidden" value="5" id="pageCapacity">
        </div>
    </div>
    <div class="weui_panel weui_panel_access" style="display:none;">
        <div class="weui_panel_hd">文字组合列表</div>
        <div class="weui_panel_bd">
            <div class="weui_media_box weui_media_text">
                <p class="weui_media_desc">由各种物质组成的巨型球状天体，叫做星球。星球有一定的形状，有自己的运行轨道。</p>
            </div>
        </div>
        <a href="javascript:void(0);" class="weui_panel_ft">查看更多</a>
    </div>
</div>
<script src="http://map.qq.com/api/js?v=2.exp&key=J7CBZ-YV43X-PVS4E-ZGYVP-KF2T3-A3BQZ&libraries=place,drawing"></script>
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
<script src="/static/public/js/map/txmap.js?v=2016-04-07"></script>
<?php include BASE_PATH.'/application/views/common/weui/footer.php';?>