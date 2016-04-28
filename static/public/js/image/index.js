var image = {};

$(function(){   
    if(navigator.userAgent.toLowerCase().match(/MicroMessenger/i) === 'micromessenger' ){
        $('#container').on('click', '#img-responsive', function(e){
            wx.chooseImage({
                count: 1, // 默认9
                sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    $('#img-responsive').attr('src', localIds);
                    $('#file').val(localIds);
                    $('#upload').submit();
                }
            });
        });
    }else{
        $('#container').on('change', '#file', function(e){
            var val = $('#file').val();
            if(!val){
                return false;
            }
            
            var valArr = val.split('.');
            var ext = valArr.pop();
            var imgExt = 'jpeg,png,bmp';
            if(imgExt.indexOf(ext.toLowerCase())===-1){
                $('#dialog2').find('.weui_dialog_bd').html('仅支持jpeg、png、bmp格式').end().show();
                return false;
            }
            
            $('#loadingToast').find('.weui_toast_content').html('图片上传中').end().show();
            $('#upload').submit();
        });

        $('#container').on('click', '#img-responsive', function(e){
            $('#file').click();
        });
    }
    
    $('#dialog2').on('click', '.weui_btn_dialog', function(e){
        $('#dialog2').hide();
    });
    
    $('#dialog1').on('click', '.weui_btn_dialog', function(e){
        $('#dialog1').hide();
    });
    
    $('#container').on('click', '#locate', function(e){
        if($('#img-responsive').attr('src').indexOf('upload')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
        $('#loadingToast').find('.weui_toast_content').html('处理中').end().show();
        
        var data = {url: location.protocol +'//'+ location.host + $('#img-responsive').attr('src')};
        $.ajax({
           url: '/weapp/image/shape',
           data: data,
           type: 'post',
           dataType: 'json',
           success: function(data, textStatus, xhr){
               $('#loadingToast').hide();
               if(data.rtn != 0){
                   $('#dialog2').find('.weui_dialog_bd').html(data.msg).end().show();
                   return false;
               }
               
               console.log(data);
           }
           
        });
        
    });
    
    $('#container').on('click', '#alasys', function(e){
        if($('#img-responsive').attr('src').indexOf('upload')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
    });
    
    $('#container').on('click', '#compare', function(e){
        if($('#img-responsive').attr('src').indexOf('upload')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
    });
    
    $('#container').on('click', '#verify', function(e){
        if($('#img-responsive').attr('src').indexOf('upload')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
    });
    
    $('#container').on('click', '#identify', function(e){
        if($('#img-responsive').attr('src').indexOf('upload')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
    });
});