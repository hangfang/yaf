<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 13:01:48
         compiled from "C:\xampp\htdocs\yaf\application\views\index\index.php" */ ?>
<?php /*%%SmartyHeaderCode:32227571e11d1602098-03338278%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1743db90d4dac1f7641be4347ec8b178f7726504' => 
    array (
      0 => 'C:\\xampp\\htdocs\\yaf\\application\\views\\index\\index.php',
      1 => 1461589306,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '32227571e11d1602098-03338278',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571e11d1605f17_64619245',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571e11d1605f17_64619245')) {function content_571e11d1605f17_64619245($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("../common/weui/header.php", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>
 <style>
    body { color: #666; font-family: sans-serif; line-height: 1.4; }
    h1 { color: #444; font-size: 1.2em; padding: 14px 2px 12px; margin: 0px; }
    h1 em { font-style: normal; color: #999; }
    a { color: #888; text-decoration: none; }
    #wrapper { width: 400px; margin: 40px auto; }

    #play_list { padding: 0px; margin: 0px; list-style: decimal-leading-zero inside; color: #ccc; border-top: 1px solid #ccc; font-size: 0.9em; }
    #play_list li { position: relative; margin: 0px; padding: 9px 2px 10px; border-bottom: 1px solid #ccc; cursor: pointer; }
    #play_list li a { display: block; text-indent: .3ex; padding: 0px 0px 0px 20px; }
    #play_list li.playing { color: #aaa; text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.3); }
    #play_list li.playing a { color: #000; }
    #play_list li.playing:before{ content: '♬'; width: 14px; height: 14px; padding: 3px; line-height: 14px; margin: 0px; position: absolute; top: 9px; color: #000; font-size: 13px; text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.2); }
    #search_show .add { background-color: blanchedalmond;text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.2); }

    #shortcuts { position: fixed; bottom: 0px; width: 100%; color: #666; font-size: 0.9em; margin: 60px 0px 0px; padding: 20px 20px 15px; background: #f3f3f3; background: rgba(240, 240, 240, 0.7); }
    #shortcuts div { width: 460px; margin: 0px auto; }
    #shortcuts h1 { margin: 0px 0px 6px; }
    #shortcuts p { margin: 0px 0px 18px; }
    #shortcuts em { font-style: normal; background: #d3d3d3; padding: 3px 9px; position: relative; left: -3px;
      -webkit-border-radius: 4px; -moz-border-radius: 4px; -o-border-radius: 4px; border-radius: 4px;
      -webkit-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); -moz-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); -o-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); }

    @media screen and (max-device-width: 480px) {
      #wrapper { position: relative; left: -3%; }
      #shortcuts { display: none; }
    }
    
    .audiojs {margin-bottom:15px; width:100% !important;}
    .audiojs .play-pause {padding: 4px 0px;}
    
    .weui_cell_primary p {height: 20px; overflow: hidden;}
    
    .weui_toast_content {overflow : hidden; text-overflow: ellipsis; display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;}
</style>
<script type="text/html" id="tpl_home">
    <div class="hd">
        <h1 class="page_title">WeApp</h1>
        <p class="page_desc">为微信Web服务量身定制</p>
    </div>
    <div class="bd">
        <div class="weui_grids">
            <a href="#/query" class="weui_grid">
                <div class="weui_grid_icon">
                    <i class="icon icon_actionSheet"></i>
                </div>
                <p class="weui_grid_label">
                    生活查询
                </p>
            </a>
            <a href="/lottery/index" class="weui_grid">
                <div class="weui_grid_icon">
                    <i class="icon icon_toast"></i>
                </div>
                <p class="weui_grid_label">
                    彩票查询
                </p>
            </a>
           
            <a href="/app/news" class="weui_grid">
                <div class="weui_grid_icon">
                    <i class="icon icon_progress"></i>
                </div>
                <p class="weui_grid_label">
                    新闻资讯
                </p>
            </a>
            <a href="/map/index" class="weui_grid">
                <div class="weui_grid_icon">
                    <i class="icon icon_button"></i>
                </div>
                <p class="weui_grid_label">
                    地图服务
                </p>
            </a>
        </div>
    </div>
</script>
<script type="text/html" id="tpl_query">
    <div class="hd">
        <h1 class="page_title">生活查询</h1>
    </div>
    <div class="bd">
        <form class="form-horizontal" action="/app/express" method="post" target="_self" id="form_express">
            <div class="weui_cells">
                <div class="weui_cell weui_cell_select weui_select_before">
                    <div class="weui_cell_hd">
                        <select name="com" id="com" class="weui_select" data-live-search="true" autocomplete="on">
                        <<?php ?>?php
                            foreach($expressList as $_k=>$_v){
                                echo <<<EOF
<option value="{$_v}">{$_k}</optoin>
EOF;
                            }
                        ?<?php ?>>
                        </select>
                    </div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" name="nu" id="nu" placeholder="快递单号"/>
                    </div>
                </div>
            </div>
            <button class="weui_btn weui_btn_primary" type="submit" id="submit_express">查询快递</button>
        </form>
        <form class="form-horizontal" action="/app/weather" method="post" target="_self" id="form_weather">
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
        
        <form class="form-horizontal" action="/app/stock" method="post" target="_self" id="form_stock">
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
</script>

<div class="weui_panel" id="result" style="display: none;">
    <div class="weui_panel_hd">查询结果</div>
    <div class="weui_panel_bd">
        <div class="weui_media_box weui_media_text">
            <p class="weui_media_desc"></p>
        </div>
    </div>
</div>
<script src="/static/weui/js/zepto.min.js?d=20160110"></script>
<script src="/static/weui/js/router.min.js?d=20160110"></script>
<script src="/static/public/js/app/index.js?d=20160110"></script>
<?php echo $_smarty_tpl->getSubTemplate ("../common/weui/footer.php", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }} ?>