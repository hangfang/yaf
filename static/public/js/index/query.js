/*---start-快递查询-start---*/
$(function(){
    $('#dialog2 .weui_btn_dialog').click(function(e){
        $('#dialog2').hide();
    })
    
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
            url : '/weapp/app/express',
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
            url : '/weapp/app/weather',
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
            url : '/weapp/app/stock',
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
});