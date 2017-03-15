<?php 
defined('BASE_PATH') OR exit('No direct script access allowed');
include BASE_PATH.'/application/views/common/weui/header.php';
?>
<link rel="stylesheet" href="/static/public/css/news/common.css?v=20160426"/>
<div class="navbar">
    <div class="bd" style="height: 100%;">
        <div class="weui_tab">
            <div class="weui_navbar">
                <a class="weui_navbar_item weui_bar_item_on" href="/weapp/news/wxhot">
                    微信
                </a>
                <a class="weui_navbar_item" href="/weapp/news/social">
                    社会
                </a>
                <a class="weui_navbar_item" href="/weapp/news/girl">
                    美图
                </a>
                <a class="weui_navbar_item" href="/weapp/news/hot">
                    热搜
                </a>
            </div>
            <div class="weui_tab_bd">

            </div>
        </div>
    </div>
</div>
<?php echo $data['msg'];?>
<script src="/static/public/js/news/news.js?d=20160110"></script>
<?php include BASE_PATH.'/application/views/common/weui/footer.php';?>