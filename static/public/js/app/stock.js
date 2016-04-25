$(function(){
    $('#stockid').blur(function(e){
        $('#submit_stock').click();
    });
    
    $('#form_stock').submit(function(e){

        if($('#stockid').val().length === 0){
            $('#stockid').parents('.form-group').removeClass('has-success').addClass('has-error').end().next('span').removeClass('glyphicon-ok').addClass('glyphicon-remove').show();
            return false;
        }else{
            $('#stockid').parents('.form-group').removeClass('has-error').addClass('has-success').end().next('span').removeClass('glyphicon-remove').addClass('glyphicon-ok').show();
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
                
                $('#stock_result').find('.weui_media_text').html(data.msg).end().show();
                return true;
            }
        });
        
        return false;
    });
});