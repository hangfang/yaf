$(function(){
    $('#cityid').selectpicker({
        'mobile': true
    });

    $('#form_weather').submit(function(e){

        if($('#cityid').val().length === 0){
            $('#cityid').parents('.form-group').removeClass('has-success').addClass('has-error').end().next('span').removeClass('glyphicon-ok').addClass('glyphicon-remove').show();
            return false;
        }else{
            $('#cityid').parents('.form-group').removeClass('has-error').addClass('has-success').end().next('span').removeClass('glyphicon-remove').addClass('glyphicon-ok').show();
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
                
                $('#weather_result').find('.weui_media_text').html(data.msg).end().show();
                return true;
            }
        });
        
        return false;
    });
});