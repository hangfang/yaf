<?php
defined('BASE_PATH') OR exit('No direct script access allowed');
include BASE_PATH . '/application/views/common/weui/header.php';
?>

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

<script type="text/javascript" src="/static/public/js/office/index.js?d=20160110"></script>
<?php include BASE_PATH . '/application/views/common/weui/footer.php'; ?>
