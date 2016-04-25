$(function(){
    $('#lottery_type').on('change', function(e){
        var _this = this;
        $('#loadingToast').show();
        $.ajax({
            url : '/lottery/index',
            data : {lottery_code: $(_this).val()},
            type : 'get',
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
                
                $('.weui_panel_hd').find('.text-danger').text('old').removeClass('text-danger');
                var html = $('<div class="weui_panel" id="lottery_result" style=""><div class="weui_panel_hd">查询结果(<span class="text-danger">new</span>)</div><div class="weui_panel_bd"><div class="weui_media_box weui_media_text">'+data.msg+'</div></div></div>');
                $('form:last').after(html);
                
                return true;
            }
        });
        
        $('#form_check .weui_cell_primary').hide();
        $('#'+$(this).val()+' input').val('');
        $('#'+$(this).val()).show().find('input:first').focus();
    });
    
    $('#form_check').on('input', '#ssq .red', function(e){
        var val = $(this).val();
        var rule = /[^\d]+/ig;
        if(val-0>33 || rule.test(val)){
            $(this).val('');
            return false;
        }
        
        if(val.length===2){
            if($(this).next('.red').length===0){
                $(this).next('.blue').focus();
                return true;
            }
            $(this).next('.red').focus();
        }
        
    }).on('blur', '#ssq .red', function(e){
        var val = $(this).val();
        var _this = this;
        var dumplicate = false;
        $(this).siblings('.red').each(function(index, el){
            if($(el).val().length>0 && $(el).val()-0===val-0){
                dumplicate = true;
                return false;
            }
        });

        if(dumplicate){
            $(this).val('')
            $(this).focus();
            return false;
        }
        
    });
    
    $('#form_check').on('input', '#ssq .blue', function(e){
        var val = $(this).val();
        var rule = /[^\d]+/ig;
        if(val-0>16 || rule.test(val)){
            $(this).val('');
            return false;
        }
        
        if(val.length===2){
            $('#form_check').submit();
            return true;
        }
    }).on('blur', '#ssq .blue', function(e){
        var val = $(this).val();
        var _this = this;
        var dumplicate = false;
        $(this).siblings().each(function(index, el){
            if($(_this).attr('name')!=='g' && $(el).val().length>0 && $(el).val()===val){
                dumplicate = true;
                return false;
            }
        });

        if(dumplicate){
            $(this).val('').focus();
            return false;
        }
        
    });
    
    $('#form_check').on('input', '#dlt .red', function(e){
        var val = $(this).val();
        var rule = /[^\d]+/ig;

        if(val-0>35 || rule.test(val)){
            $(this).val('');
            return false;
        }
        
        if(val.length===2){
            if($(this).next('.red').length===0){
                $(this).next('.blue').focus();
                return true;
            }
            $(this).next('.red').focus();
        }
        
    }).on('blur', '#dlt .red', function(e){
        var val = $(this).val();
        var dumplicate = false;
        $(this).siblings('.red').each(function(index, el){
            if($(el).val().length>0 && $(el).val()-0===val-0){
                dumplicate = true;
                return false;
            }
        });

        if(dumplicate){
            $(this).val('').focus();
            return false;
        }
        
    });
    
    $('#form_check').on('input', '#dlt .blue', function(e){
        var val = $(this).val();
        var rule = /[^\d]+/ig;

        if(val-0>12 || rule.test(val)){
            $(this).val('');
            return false;
        }
        
        if(val.length===2){
            if($(this).next('.blue').length===0){
                $('#form_check').submit();
                return true;
            }
            $(this).next('.blue').focus();
        }
        
    }).on('blur', '#dlt .blue', function(e){
        var val = $(this).val();
        var dumplicate = false;
        $(this).siblings('.blue').each(function(index, el){
            if($(el).val().length>0 && $(el).val()-0===val-0){
                dumplicate = true;
                return false;
            }
        });

        if(dumplicate){
            $(this).val('').focus();
            return false;
        }
        
    });
    
    $('#form_check').on('input', '#pl3 input, #pl5 input, #qxc input, #fc3d input', function(e){
        var val = $(this).val();
        var rule = /[^\d]+/ig;
        var isLast = $(this).next().length==0;

        if(val-0>9 || rule.test(val)){
            $(this).val('');
            return false;
        }
        
        if(val.length===1){
            if(isLast){
                $('#form_check').submit();
                return true;
            }
            $(this).next().focus();
        }
        
    });
    
    $('#form_check').on('submit', function(e){
        var lottery_type = $('#lottery_type').val();
        var data = {lottery_type: lottery_type};
        var isCompleted = true;
        $('#'+lottery_type).find('input[type=tel]').each(function(index, el){
            if($(el).val().length==0){
                $(el).focus();
                isCompleted = false;
                return false;
            }
            data[$(el).attr('name')] = $(el).val();
        });
        
        if(isCompleted===false){
            return false;
        }

        $('#loadingToast').show();
        $.ajax({
            url : '/lottery/checkLottery',
            data : data,
            type : 'get',
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
                
                $('.weui_panel_hd').find('.text-danger').text('old').removeClass('text-danger');
                var html = $('<div class="weui_panel" style=""><div class="weui_panel_hd">查询结果(<span class="text-danger">new</span>)</div><div class="weui_panel_bd"><div class="weui_media_box weui_media_text">'+data.msg+'</div></div></div>');
                $('form:last').after(html);
                return true;
            }
        });
        
        return false;
    });
    
    $('#lottery_type').trigger('change');
});