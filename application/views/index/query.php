<?php 
defined('BASE_PATH') OR exit('No direct script access allowed');
include dirname(dirname(__FILE__)).'/common/weui/header.php';
?>
<div class="query">
    <div class="hd">
        <h1 class="page_title">便利</h1>
    </div>
    <div class="bd">
        <form class="form-horizontal" action="/weapp/app/express" method="post" target="_self" id="form_express">
            <div class="weui_cells">
                <div class="weui_cell weui_cell_select weui_select_before">
                    <div class="weui_cell_hd">
                        <select name="com" id="com" class="weui_select" data-live-search="true" autocomplete="on">
                        <?php
                            foreach($data['expressList'] as $_k=>$_v){
                                echo <<<EOF
<option value="{$_v}">{$_k}</optoin>
EOF;
                            }
                        ?>
                        </select>
                    </div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" name="nu" id="nu" placeholder="快递单号"/>
                    </div>
                </div>
            </div>
            <button class="weui_btn weui_btn_primary" type="submit" id="submit_express">查询快递</button>
        </form>
        <form class="form-horizontal" action="/weapp/app/weather" method="post" target="_self" id="form_weather">
            <div class="weui_cell">
                <!--<div class="weui_cell_hd">
                    <label class="weui_label">城市</label>
                </div>-->
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" id="cityid" name="cityid" placeholder="请输入城市">
                </div>
            </div>
            <button class="weui_btn weui_btn_primary" type="submit" id="submit_weather">查询天气</button>
        </form>
        
        <form class="form-horizontal" action="/weapp/app/stock" method="post" target="_self" id="form_stock">
            <div class="weui_cell">
                <!--<div class="weui_cell_hd">
                    <label class="weui_label">股票代码</label>
                </div>-->
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="number" id="stockid" name="stockid" placeholder="请输入股票代码">
                </div>
            </div>
            <button class="weui_btn weui_btn_primary" type="submit" id="submit_stock">查询股票</button>
        </form>
    </div>
</div>

<div class="weui_panel" id="result" style="display: none;">
    <div class="weui_panel_hd">查询结果</div>
    <div class="weui_panel_bd">
        <div class="weui_media_box weui_media_text">
            <p class="weui_media_desc"></p>
        </div>
    </div>
</div>
<script src='/static/public/js/index/query.js?v=20160426'></script>
<?php include dirname(dirname(__FILE__)).'/common/weui/footer.php';?>