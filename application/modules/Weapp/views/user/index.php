<?php 
defined('BASE_PATH') OR exit('No direct script access allowed');
include BASE_PATH.'/application/views/common/weui/header.php';
?>
<link rel="stylesheet" href="/static/public/css/user/index.css?v=20160426"/>
<div class="weui_panel panel1" style="margin-top: 0;">
<!--    <div class="weui_panel_hd">个人中心</div>-->
    <div class="weui_panel_bd">
        <div class="weui_media_box weui_media_text">
            <div class="head-img">
                <img src="/static/public/img/user/head-img.png" width="75" height="75">
            </div>
            <div class="head-dsb">
                <p class="dsb-name">--凌乱</p>
                <p class="dsb-id">ID  1271543621</p>
            </div>
        </div>
    </div>
</div>
<div class="weui_panel panel2">
    <div class="weui_panel_bd">
        <div class="weui_media_box weui_media_text">
            <ul>
                <li>
                    <i class="idt"></i>
                    <p>签到</p>
                </li>
                <li class="pt-line">
                    <i class="clt"></i>
                    <p>关心</p>
                </li>
                <li>
                    <i class="rcm"></i>
                    <p>推荐</p>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="weui_cell panel3">
    <div class="bd">
        <div class="weui_cells weui_cells_access">
            <a class="weui_cell" href="javascript:;">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>修改个人资料</p>
                </div>
                <div class="weui_cell_ft">
                </div>
            </a>
            <a class="weui_cell" href="javascript:;">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>修改密码</p>
                </div>
                <div class="weui_cell_ft">
                </div>
            </a>
        </div>
    </div>
</div>

<div class="weui_cell panel4">
    <div class="bd">
        <div class="weui_cells weui_cells_access">
            <a class="weui_cell" href="javascript:;">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>推送通知</p>
                </div>
                <div class="weui_cell_ft">
                    <input class="weui_switch" type="checkbox" id="switch" checked="">
                </div>
            </a>
        </div>
    </div>
</div>

<div class="weui_cell panel5">
    <div class="bd">
        <div class="weui_cells weui_cells_access">
            <a class="weui_cell" href="javascript:;">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>猜你喜欢</p>
                </div>
                <div class="weui_cell_ft">
                </div>
            </a>
            <a class="weui_cell" href="javascript:;">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>附近热门</p>
                </div>
                <div class="weui_cell_ft">
                </div>
            </a>
            <a class="weui_cell" href="javascript:;">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>周边推荐</p>
                </div>
                <div class="weui_cell_ft">
                </div>
            </a>
            <a class="weui_cell" href="javascript:;">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>设置</p>
                </div>
                <div class="weui_cell_ft">
                </div>
            </a>
        </div>
    </div>
</div>
<script src="/static/public/js/user/index.js?v=20160420"></script>
<?php include BASE_PATH.'/application/views/common/weui/footer.php';?>