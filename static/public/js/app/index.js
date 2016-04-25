$(function () {

    var router = new Router({
        container: '#container',
        enterTimeout: 250,
        leaveTimeout: 250
    });
    
    var home = {
        url: '/',
        className: 'home',
        render: function () {
            return $('#tpl_home').html();
        }
    };
    
    // express
    var query = {
        url: '/query',
        className: 'query',
        render: function () {
            return $('#tpl_query').html();
        },
        bind: function(){
            /*---start-快递查询-start---*/

            $('#container').on('submit', '#form_express', function(e){

                if(!$('#com').val()){
                    $('#dialog2').find('.weui_dialog_hd').html('输入错误').end().find('.weui_dialog_bd').html('请选择快递公司').end().show();
                    return false;
                }

                if(!$('#nu').val() || $('#nu').val().length<6){
                    $('#dialog2').find('.weui_dialog_hd').html('输入错误').end().find('.weui_dialog_bd').html('快递单号不能少于六个字符').end().show();
                    return false;
                }

                var data = $(this).serialize();
                $('#loadingToast').show();
                $.ajax({
                    url : '/app/express',
                    data : data,
                    type : 'post',
                    dataType : 'json',
                    success : function(data, textStatus, xhr){
                        $('#loadingToast').hide();
                        if(data.rtn > 0){
                            $('#toast').find('.weui_toast_content').html(data.errmsg).end().show();
                            setTimeout(function(){;
                                $('#toast').hide();
                            }, 2000);
                            return false;
                        }

                        $('#result').find('.weui_media_text').html(data.msg).end().show();
                        return true;
                    }
                });

                return false;
            });
            /*---end--快递查询--end---*/
            
            /*---start-天气查询-start---*/
            $('#container').on('submit', '#form_weather', function(e){

                if(!$('#cityid').val()){
                    $('#dialog2').find('.weui_dialog_hd').html('输入错误').end().find('.weui_dialog_bd').html('城市不能为空').end().show();
                    return false;
                }

                var data = $(this).serialize();
                $('#loadingToast').show();
                $.ajax({
                    url : '/app/weather',
                    data : data,
                    type : 'post',
                    dataType : 'json',
                    success : function(data, textStatus, xhr){
                        $('#loadingToast').hide();
                        if(data.rtn > 0){
                            $('#toast').find('.weui_toast_content').html(data.errmsg).end().show();
                            setTimeout(function(){;
                                $('#toast').hide();
                            }, 2000);
                            return false;
                        }

                        $('#result').find('.weui_media_text').html(data.msg).end().show();
                        return true;
                    }
                });

                return false;
            });
            /*---end--天气查询--end---*/
            
            /*---start-股票查询-start---*/

            $('#container').on('submit', '#form_stock', function(e){

                if(!$('#stockid').val() || $('#stockid').val().length<6){
                    $('#dialog2').find('.weui_dialog_hd').html('输入错误').end().find('.weui_dialog_bd').html('股票代码必须为六位数，如600001').end().show();
                    return false;
                }

                var data = $(this).serialize();
                $('#loadingToast').show();
                $.ajax({
                    url : '/app/stock',
                    data : data,
                    type : 'post',
                    dataType : 'json',
                    success : function(data, textStatus, xhr){
                        $('#loadingToast').hide();
                        if(data.rtn > 0){
                            $('#toast').find('.weui_toast_content').html(data.errmsg).end().show();
                            setTimeout(function(){;
                                $('#toast').hide();
                            }, 2000);
                            return false;
                        }

                        $('#result').find('.weui_media_text').html(data.msg).end().show();
                        return true;
                    }
                });

                return false;
            });
            /*---end--股票查询--end---*/
        }
    };
    
    router.push(home)
        .push(query)
        .setDefault('/')
        .init();


    // .container 设置了 overflow 属性, 导致 Android 手机下输入框获取焦点时, 输入法挡住输入框的 bug
    // 相关 issue: https://github.com/weui/weui/issues/15
    // 解决方法:
    // 0. .container 去掉 overflow 属性, 但此 demo 下会引发别的问题
    // 1. 参考 http://stackoverflow.com/questions/23757345/android-does-not-correctly-scroll-on-input-focus-if-not-body-element
    //    Android 手机下, input 或 textarea 元素聚焦时, 主动滚一把
    if (/Android/gi.test(navigator.userAgent)) {
        window.addEventListener('resize', function () {
            if (document.activeElement.tagName == 'INPUT' || document.activeElement.tagName == 'TEXTAREA') {
                window.setTimeout(function () {
                    document.activeElement.scrollIntoViewIfNeeded();
                }, 0);
            }
        })
    }
    
    $('#dialog2 .weui_btn_dialog').click(function(e){
        $('#dialog2').hide();
    })
});