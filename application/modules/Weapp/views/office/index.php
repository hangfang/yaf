<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
include APPLICATION_PATH . '/application/views/common/weui/header.php';
?>

<link type="text/css" rel="stylesheet" href="/static/public/css/imgbox.css?v=20160505"/>
<div class="weui_cells weui_cells_form">
    <div class="weui_cell">
        <div class="weui_cell_bd weui_cell_primary">
            <div class="weui_uploader">
                <div class="weui_uploader_hd weui_cell">
                    <div class="weui_cell_bd weui_cell_primary">文件上传</div>
                    <div class="weui_cell_ft"></div>
                </div>
                <form method="post" action="" id="form" target="_self" enctype="multipart/form-data">
                    <div class="weui_uploader_bd">
                        <div class="weui_uploader_input_wrp">
                            <input class="weui_uploader_input" type="file" name="file" id="file">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<a id="img-preview" title href="http://placeholdit.imgix.net/~text?txtsize=14&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=17&h=17&txttrack=0" >
    <img alt src="http://placeholdit.imgix.net/~text?txtsize=14&txt=%E5%9B%BE%E8%A3%82%E4%BA%86&w=17&h=17&txttrack=0" />
</a>

<div id="imgbox-loading"><div style="opacity: 0.4;"></div></div>
<div id="imgbox-overlay"></div>


<script src="/static/public/js/jquery.imgbox.pack.js?d=20160110"></script>
<script type="text/javascript" src="/static/public/js/office/index.js?d=20160110"></script>
<?php include APPLICATION_PATH . '/application/views/common/weui/footer.php'; ?>
