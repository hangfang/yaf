    </div>
    <!-- start-已完成-start -->
    <div id="toast" style="display: none;">
        <div class="weui_mask_transparent"></div>
        <div class="weui_toast">
            <i class="weui_icon_toast"></i>
            <p class="weui_toast_content">已完成</p>
        </div>
    </div>
    <!-- end--已完成--end -->
    <!-- start-加载中-start -->
    <div id="loadingToast" class="weui_loading_toast" style="display: none;">
        <div class="weui_mask_transparent"></div>
        <div class="weui_toast">
            <div class="weui_loading">
                <div class="weui_loading_leaf weui_loading_leaf_0"></div>
                <div class="weui_loading_leaf weui_loading_leaf_1"></div>
                <div class="weui_loading_leaf weui_loading_leaf_2"></div>
                <div class="weui_loading_leaf weui_loading_leaf_3"></div>
                <div class="weui_loading_leaf weui_loading_leaf_4"></div>
                <div class="weui_loading_leaf weui_loading_leaf_5"></div>
                <div class="weui_loading_leaf weui_loading_leaf_6"></div>
                <div class="weui_loading_leaf weui_loading_leaf_7"></div>
                <div class="weui_loading_leaf weui_loading_leaf_8"></div>
                <div class="weui_loading_leaf weui_loading_leaf_9"></div>
                <div class="weui_loading_leaf weui_loading_leaf_10"></div>
                <div class="weui_loading_leaf weui_loading_leaf_11"></div>
            </div>
            <p class="weui_toast_content">数据加载中</p>
        </div>
    </div>
    <!-- end--加载中--end -->
    <!-- start-确认弹框-start -->
    <div class="weui_dialog_confirm" id="dialog1" style="display:none;">
        <div class="weui_mask"></div>
        <div class="weui_dialog">
            <div class="weui_dialog_hd"><strong class="weui_dialog_title">请确认</strong></div>
            <div class="weui_dialog_bd">自定义弹窗内容，居左对齐显示，告知需要确认的信息等</div>
            <div class="weui_dialog_ft">
                <a href="javascript:;" class="weui_btn_dialog default">取消</a>
                <a href="javascript:;" class="weui_btn_dialog primary">确定</a>
            </div>
        </div>
    </div>
    <!-- end--确认弹框--end -->
    <!-- start-提示弹框-start -->
    <div class="weui_dialog_alert" id="dialog2" style="display: none;">
        <div class="weui_mask"></div>
        <div class="weui_dialog">
            <div class="weui_dialog_hd"><strong class="weui_dialog_title">警告</strong></div>
            <div class="weui_dialog_bd">弹窗内容，告知当前页面信息等</div>
            <div class="weui_dialog_ft">
                <a href="javascript:;" class="weui_btn_dialog primary">确定</a>
            </div>
        </div>
    </div>
    <!-- end--提示弹框--end -->
    <!-- start-操作成功-start -->
    <div class="msg" id="msg" style="display:none;">
        <div class="weui_msg">
            <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
            <div class="weui_text_area">
                <h2 class="weui_msg_title">操作成功</h2>
                <p class="weui_msg_desc">内容详情，可根据实际需要安排</p>
            </div>
            <div class="weui_opr_area">
                <p class="weui_btn_area">
                    <a href="javascript:;" class="weui_btn weui_btn_primary">确定</a>
                    <a href="javascript:;" class="weui_btn weui_btn_default">取消</a>
                </p>
            </div>
            <div class="weui_extra_area">
                <a href="">查看详情</a>
            </div>
        </div>
    </div>
    <!-- end--操作成功--end -->
    <div id="tabbar" class="tabbar">
        <div class="weui_tab">
            <div class="weui_tab_bd">

            </div>
            <div class="weui_tabbar">
                <a href="/index/index" class="weui_tabbar_item <?php if($data['class']==='app'){echo 'weui_bar_item_on';}?>">
                    <div class="weui_tabbar_icon">
                        <img src="/static/weui/images/icon_nav_button.png" alt="">
                    </div>
                    <p class="weui_tabbar_label">微信</p>
                </a>
                <a href="/weapp/contact/index" id="contact" class="weui_tabbar_item <?php if($data['class']==='contact'){echo 'weui_bar_item_on';}?>">
                    <div class="weui_tabbar_icon">
                        <img src="/static/weui/images/icon_nav_msg.png" alt="">
                    </div>
                    <p class="weui_tabbar_label">通讯录</p>
                </a>
                <a href="/weapp/map/index" class="weui_tabbar_item <?php if($data['class']==='map'){echo 'weui_bar_item_on';}?>">
                    <div class="weui_tabbar_icon">
                        <img src="/static/weui/images/icon_nav_article.png" alt="">
                    </div>
                    <p class="weui_tabbar_label">发现</p>
                </a>
                <a href="/weapp/user/index" class="weui_tabbar_item <?php if($data['class']==='user'){echo 'weui_bar_item_on';}?>">
                    <div class="weui_tabbar_icon">
                        <img src="/static/weui/images/icon_nav_cell.png" alt="">
                    </div>
                    <p class="weui_tabbar_label">我</p>
                </a>
            </div>
        </div>
    </div>
    <!-- start-ActionSheet-start -->
    <div id="actionSheet_wrap">
        <div class="weui_mask_transition" id="mask" style="display: none;"></div>
        <div class="weui_actionsheet" id="weui_actionsheet">
            <div class="weui_actionsheet_menu">
                <div class="weui_actionsheet_cell">谢谢使用WeApp！</div>
            </div>
            <div class="weui_actionsheet_action">
                <div class="weui_actionsheet_cell" id="actionsheet_cancel">取消</div>
            </div>
        </div>
    </div>
    <!-- start-ActionSheet-start -->
</body>
</html>
<script type="text/javascript" src="http://tajs.qq.com/stats?sId=55696994" charset="UTF-8"></script>