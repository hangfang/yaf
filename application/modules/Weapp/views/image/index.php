<?php
defined('BASE_PATH') OR exit('No direct script access allowed');
include BASE_PATH . '/application/views/common/weui/header.php';
?>
<link rel="stylesheet" href="/static/public/css/image/common.css?v=20160426"/>
<div class="navbar">
    <div class="bd" style="height: 100%;">
        <div class="weui_tab">
            <div class="weui_navbar">
                <a class="weui_navbar_item weui_bar_item_on" href="/weapp/image/index">
                    人脸识别
                </a>
                <a class="weui_navbar_item" href="/weapp/image/photo">
                    图像识别
                </a>
                <a class="weui_navbar_item" href="javascript:void(0)">
                    continuing
                </a>
                <a class="weui_navbar_item" href="javascript:void(0)">
                    continuing
                </a>
            </div>
            <div class="weui_tab_bd">

            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="bd">
        <ul class="list-group">
            <li class="list-group-item"  id="img-responsive-container">
                <a class="bg-wrapper" href="javascript:void(0)"  id="img-responsive">
                    <img src="http://placeholdit.imgix.net/~text?txtsize=16&txt=+%E7%82%B9%E5%87%BB%E6%B7%BB%E5%8A%A0%E5%9B%BE%E7%89%87&w=403&h=268&txttrack=1" class="carousel-inner img-responsive">
                </a>
            </li>
            <li class="list-group-item">
                <div class="row" >
                    <div class="col-xs-9 no-new-line">
                        <div class="weui_cell image_url">
                            <div class="weui_cell_bd weui_cell_primary">
                                <input class="weui_input input_image_url" type="text" placeholder="请输入图像url">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3"><a href="javascript:;" class="weui_btn weui_btn_plain_primary">按钮</a></div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row" >
                    <div class="col-xs-3"><a href="javascript:;" class="weui_btn weui_btn_plain_primary" id="locate">五官定位</a></div>
                    <div class="col-xs-3"><a href="javascript:;" class="weui_btn weui_btn_plain_primary" id="analyse">表情分析</a></div>
                    <div class="col-xs-3"><a href="javascript:;" class="weui_btn weui_btn_plain_primary" id="compare">人脸对比</a></div>
                    <div class="col-xs-3"><a href="javascript:;" class="weui_btn weui_btn_plain_primary" id="verify">人脸验证</a></div>
                    <div class="col-xs-3"><a href="javascript:;" class="weui_btn weui_btn_plain_primary" id="identify">人脸识别</a></div>
                </div>
            </li>
        </ul>
    </div>
</div>

<form  enctype="multipart/form-data" method="post" action="/weapp/image/upload" style="display:none;" id="upload" target="iframe">
<input type="file" name="image" id="file" />
<div id="imgDiv"></div>
</form>
<iframe style="display:none;" name="iframe"></iframe>
<script type="text/html" id="img-responsive-template">
    <div class="container-fluid">
        <div class="bd">
            <ul class="list-group">
                <li class="list-group-item">
                    <a class="bg-wrapper" href="javascript:void(0)">
                        <img src="{imgsrc}" class="carousel-inner img-responsive">
                    </a>
                </li>
            </ul>
        </div>
    </div>
</script>
<script type="text/html" id="progress_template">
    <div class="container-fluid" id="progress">
        <div class="bd">
            <ul class="list-group">
            </ul>
        </div>
    </div>
</script>

<script type="text/javascript" src="/static/public/js/upload/lib/crypto1/crypto/crypto.js"></script>
<script type="text/javascript" src="/static/public/js/upload/lib/crypto1/hmac/hmac.js"></script>
<script type="text/javascript" src="/static/public/js/upload/lib/crypto1/sha1/sha1.js"></script>
<script type="text/javascript" src="/static/public/js/upload/lib/base64.js"></script>
<script type="text/javascript" src="/static/public/js/upload/lib/plupload-2.1.2/js/plupload.full.min.js"></script>

<script src="/static/weui/js/jweixin-1.1.0.js?v=2016-04-07"></script>
<script>
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?php echo $data['appId'];?>', // 必填，公众号的唯一标识
        timestamp: '<?php echo $data['timestamp'];?>', // 必填，生成签名的时间戳
        nonceStr: '<?php echo $data['nonceStr'];?>', // 必填，生成签名的随机串
        signature: '<?php echo $data['signature'];?>',// 必填，签名，见附录1
        jsApiList: ['chooseImage','startRecord','stopRecord','playVoice','pauseVoice','stopVoice','hideMenuItems','showAllNonBaseMenuItem','hideAllNonBaseMenuItem','showOptionMenu','hideOptionMenu','scanQRCode','closeWindow', 'getNetworkType', 'openLocation', 'getLocation','translateVoice'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });    
</script>
<script src="/static/public/js/image/index.js?d=20160110"></script>
<?php include BASE_PATH . '/application/views/common/weui/footer.php'; ?>