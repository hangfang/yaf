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
                    $('#img-responsive img').attr('src', localIds);
                }
            });
        });
    }else{
    }
    
    $('#dialog2').on('click', '.weui_btn_dialog', function(e){
        $('#dialog2').hide();
    });
    
    $('#dialog1').on('click', '.weui_btn_dialog', function(e){
        $('#dialog1').hide();
    });
    
    $('#container').on('click', '#locate', function(e){
        if($('#img-responsive img').attr('src').indexOf('rbmax')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
        $('#loadingToast').find('.weui_toast_content').html('处理中').end().show();
        
        var data = {url: $('#img-responsive img').attr('src')};
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
               
               $('#img-responsive img').attr('src', data.img);
               //$('#upload').before($('#img-responsive-template').html().replace('{imgsrc}', data.img));
           }
           
        });
        
    });
    
    $('#container').on('click', '#analyse', function(e){
        if($('#img-responsive img').attr('src').indexOf('rbmax')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
        
        $('#loadingToast').find('.weui_toast_content').html('处理中').end().show();
        
        var data = {url: $('#img-responsive img').attr('src')};
        $.ajax({
           url: '/weapp/image/analyse',
           data: data,
           type: 'post',
           dataType: 'json',
           success: function(data, textStatus, xhr){
               $('#loadingToast').hide();
               if(data.rtn != 0){
                   $('#dialog2').find('.weui_dialog_bd').html(data.msg).end().show();
                   return false;
               }
               
               $('#img-responsive img').attr('src', data.img);
               //$('#upload').before($('#img-responsive-template').html().replace('{imgsrc}', data.img));
           }
           
        });
    });
    
    $('#container').on('click', '#compare', function(e){
        if($('#img-responsive img').attr('src').indexOf('rbmax')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
    });
    
    $('#container').on('click', '#verify', function(e){
        if($('#img-responsive img').attr('src').indexOf('rbmax')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
    });
    
    $('#container').on('click', '#identify', function(e){
        if($('#img-responsive img').attr('src').indexOf('rbmax')===-1){
            $('#dialog2').find('.weui_dialog_bd').html('请添加图片').end().show();
            return false;
        }
    });
    
    $('#container').on('click', '.reset', function(e){
        $('#img-responsive-container').find('img').attr('src', $(this).prev('a').find('i').attr('src').replace(/\?.*/ig, '')+'?rd='+new Date().getTime());
    });
    
  
    (function(){
        var policyText = {
        "expiration": "2020-01-01T12:00:00.000Z", //设置该Policy的失效时间，超过这个失效时间之后，就没有办法通过这个policy上传文件了
        "conditions": [
            ["content-length-range", 0, 1048576000] // 设置上传文件的大小限制
            ]
        };

        var accessid= '3IYCIDFZcGbGpnUX';
        var accesskey= 'AjWQTwY1fq6puNswqPoB0BnXVg1TZI';
        var host = 'http://oss.rbmax.com';


        var policyBase64 = Base64.encode(JSON.stringify(policyText));
        var bytes = Crypto.HMAC(Crypto.SHA1, policyBase64, accesskey, { asBytes: true }) ;
        var signature = Crypto.util.bytesToBase64(bytes);
        var uploader = new plupload.Uploader({
                runtimes : 'html5,flash,silverlight,html4',
                browse_button : 'img-responsive', 
            //runtimes : 'flash',
                container: document.getElementById('img-responsive-container'),
                flash_swf_url : '/static/public/js/upload/lib/plupload-2.1.2/js/Moxie.swf',
                silverlight_xap_url : '/static/public/js/upload/lib/plupload-2.1.2/js/Moxie.xap',
                url : host,
                unique_names : true,
                //multi_selection: false,
                multiple_queues: true,
                max_file_size: '3mb',
                filters : [{title : "图片", extensions : "jpeg,png,bmp,jpg"}],

                multipart_params: {
                    //'Filename': '${filename}', 
                    'key' : 'image/${filename}',
                    'policy': policyBase64,
                    'OSSAccessKeyId': accessid, 
                    'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
                    'signature': signature,
                },

                init: {
                        PostInit: function() {
                            $('#upload').before($('#progress_template').html());
                        },

                        FilesAdded: function(up, files) {
                            plupload.each(files, function(file) {

                                var html = '<li class="list-group-item" id="'+ file.id +'"><div class="weui_progress"><div class="weui_progress_bar"><div class="weui_progress_inner_bar js_progress" style="width: 0%;"></div></div><a href="javascript:;" class="weui_progress_opr"><i class="weui_icon_waiting"></i></a><a href="javascript:;" class="reset weui_progress_opr weui_btn weui_btn_plain_default">复位</a></div></li>';
                                $('#progress ul').append(html);

                            });
                            uploader.start();
                        },

                        UploadProgress: function(up, file) {
                                $('#'+file.id).find('.js_progress').css('width', file.percent+'%');
                        },

                        FileUploaded: function(up, file, info) {
                            if (info.status >= 200 || info.status < 200)
                            {
                                var imgSrc = 'http://oss.rbmax.com/image/'+file.name;
                                $('#'+file.id).find('i').attr('class', 'weui_icon_success').attr('src', imgSrc);
                                $('#img-responsive img').attr('src', imgSrc);
                            }
                            else
                            {
                                $('#'+file.id).find('i').attr('class', 'weui_icon_warning');
                                $('#'+file.id).find('.js_progress').html(info.response);
                            }
                            //uploader.refresh();
                        },

                        Error: function(up, err) {
                            $('#'+file.id).find('.js_progress').html(err.response);
                            //uploader.refresh();
                        }
                }
        });

        uploader.init();
    })($);
});