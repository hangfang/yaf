var policyText = {
    "expiration": "2020-01-01T12:00:00.000Z", //设置该Policy的失效时间，超过这个失效时间之后，就没有办法通过这个policy上传文件了
    "conditions": [
    ["content-length-range", 0, 1048576000] // 设置上传文件的大小限制
    ]
};

accessid= '3IYCIDFZcGbGpnUX';
accesskey= 'AjWQTwY1fq6puNswqPoB0BnXVg1TZI';
host = 'http://oss.rbmax.com';


var policyBase64 = Base64.encode(JSON.stringify(policyText))
message = policyBase64
var bytes = Crypto.HMAC(Crypto.SHA1, message, accesskey, { asBytes: true }) ;
var signature = Crypto.util.bytesToBase64(bytes);
var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'img-responsive', 
    //runtimes : 'flash',
	container: document.getElementById('img-responsive-container'),
	flash_swf_url : '/static/public/js/upload/lib/plupload-2.1.2/js/Moxie.swf',
	silverlight_xap_url : '/static/public/js/upload/lib/plupload-2.1.2/js/Moxie.xap',

        url : host,

	multipart_params: {
            'Filename': '${filename}', 
            'key' : '${filename}',
            'policy': policyBase64,
            'OSSAccessKeyId': accessid, 
            'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
            'signature': signature,
	},

	init: {
		PostInit: function() {
                    $('#upload').before($('#progress_template').html());
                    setTimeout(function(){
                        uploader.start();
                    }, 2000);
		},

		FilesAdded: function(up, files) {
                    plupload.each(files, function(file) {
                        
                        var html = '<li class="list-group-item" id="'+ file.id +'"><div class="weui_progress"><div class="weui_progress_bar"><div class="weui_progress_inner_bar js_progress" style="width: 0%;"></div></div><a href="javascript:;" class="weui_progress_opr"><i class="weui_icon_waiting"></i></a></div></li>';
                        $('#progress ul').append(html);
                        return false;
                    });
		},

		UploadProgress: function(up, file) {console.log(file);
			$('#'+file.id).find('.js_progress').css('width', file.percent+'%');
		},

		FileUploaded: function(up, file, info) {
                    if (info.status >= 200 || info.status < 200)
                    {
                        $('#'+file.id).find('i').attr('class', 'weui_icon_success');
                        $('#img-responsive img').attr('src', 'http://oss.rbmax.com/'+file.name);
                    }
                    else
                    {
                        $('#'+file.id).find('i').attr('class', 'weui_icon_warning');
                        $('#'+file.id).find('.js_progress').html(info.response);
                    } 
		},

		Error: function(up, err) {
                    $('#'+file.id).find('.js_progress').html(err.response);
		}
	}
});

uploader.init();
