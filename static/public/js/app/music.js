$(function(){
    $('#container').on('focus', '#search_input', function (e) {
        $('#search_show').show();
        var $weuiSearchBar = $('#search_bar');
        $weuiSearchBar.addClass('weui_search_focusing');
    }).on('blur', '#search_input', function (e) {
        var $weuiSearchBar = $('#search_bar');
        $weuiSearchBar.removeClass('weui_search_focusing');
        if ($(this).val()) {
            $('#search_text').hide();
        } else {
            $('#search_text').show();
        }
    }).on('input', '#search_input', function (e) {

        var name = $('#search_input').val();
        var $searchShow = $("#search_show");
        if (name.length>0) {
            var data = {name: name, page: music.page, size: music.size};
            music.getMusic(data);
        } else {
            $searchShow.hide();
        }
    }).on('click touchend', '#search_cancel', function (e) {
        $("#search_show").hide();
        $('#search_input').val('');
    }).on('click touchend', '#search_clear', function (e) {
        $("#search_show").hide();
        $('#search_input').val('');
    }).on('click touched', '.weui_cell', function(e){
        var _this = this;
        $(this).addClass('add').siblings().removeClass('add');
        var filename = $(this).attr('filename');
        var hash = $(this).attr('hash');
        
        $('#loadingToast').show();
        var data = {hash: hash};
        $.ajax({
            url: '/app/getMusicPlayInfo',
            data: data,
            dataType: 'json',
            type: 'get',
            success: function(data, textStatus, xhr){
                $('#loadingToast').hide();
                if(data.rtn > 0){
                    $('#toast').find('.weui_toast_content').html(data.errmsg).end().show();
                    setTimeout(function(){;
                        $('#toast').hide();
                    }, 2000);
                    return false;
                }

                var li = $('<li><a href="#" data-src="'+data.msg.url+'">'+filename+'</a></li>');
                li.appendTo($('#play_list'));
                
                $('#toast').find('.weui_toast_content').html(filename).end().show();
                setTimeout(function(){;
                    $('#toast').hide();
                }, 2000);
                
                if($('#play_list li').length === 1){
                    li.click();
                }
            }
        });
    }).on('touchstart', '#search_show', function(e){
        music.touchstart = event.changedTouches[0].clientY;
    }).on('touchend', '#search_show', function(e){
        music.touchend = event.changedTouches[0].clientY;
        if(music.total_page < music.page){
            $('#toast').find('.weui_toast_content').html('已加载完毕...').end().show();
            setTimeout(function(){;
                $('#toast').hide();
            }, 2000);
            return false;
        }
        
        if(music.touchstart-music.touchend > 200){
            var data = {name: $('#search_input').val(), page: music.page, size: music.size};
            music.getMusic(data);
        }
    }).on('click', function(e){
        if($(e.target).parents('#search_show').length == 0 && $(e.target).parents('#search_bar').length ===0){
            $('#search_show').hide();
        }
    });
    
    music.audio = audiojs.createAll({
        trackEnded: function() {
            if($('#play_list li').length===0){
                music.audio.stop();
            }
            var next = $('ol li.playing').next();
            if (!next.length){
                next = $('ol li:first-child');
            }
            next.addClass('playing').siblings().removeClass('playing');
            music.audio.load($('a', next).attr('data-src'));
            music.audio.play();               
        }
    }).pop();

    // Load in the first track
    if($('#play_list li').length > 0){
        music.audio.load($('#play_list li:first-child').addClass('playing').find('a').attr('data-src'));
        music.audio.play();
    }

    // Load in a track on click
    $('#play_list').on('touchend click', function(e) {
        if($('#play_list li').length === 0){
            return false;
        }
        if(music.delay > 0){
            $('#toast').find('.weui_toast_content').html('心好累，请勿操作太快。').end().show();
            setTimeout(function(){;
                $('#toast').hide();
            }, 2000);
            return false;
        }
        
        music.delay = 5;
        music.intervalId = setInterval(function(){
            music.delay--;
            if(music.delay===0){
                clearInterval(music.intervalId);
            }
        });
        
        e.preventDefault();
        if(e.target.nodeName.toLowerCase()==='li'){
            $(e.target).addClass('playing').siblings().removeClass('playing');
            music.audio.load($(e.target).find('a').attr('data-src'));
            music.audio.play();
            return true;
        }
        $(e.target).parents('li').addClass('playing').siblings().removeClass('playing');
        music.audio.load($(e.target).attr('data-src'));
        music.audio.play();
    });
    // Keyboard shortcuts
    $(document).keydown(function(e) {
        var unicode = e.charCode ? e.charCode : e.keyCode;
        // right arrow
        if (unicode == 39) {
            var next = $('#play_list li.playing').next();
            if (!next.length) next = $('#play_list li:first-child');
            next.click();
            // back arrow
        } else if (unicode == 37) {
            var prev = $('#play_list li.playing').prev();
            if (!prev.length) prev = $('#play_list li:last-child');
            prev.click();
            // spacebar
        } else if (unicode == 32) {
            music.audio.playPause();
        }
    })
});

music.getMusic = function(data){
    if(music.xhr){
        music.xhr.abort();
        $('#loadingToast').hide();
    }
    $('#loadingToast').show();
    music.xhr = $.ajax({
        url: '/app/music',
        data: data,
        dataType: 'json',
        type: 'get',
        success : function(data, textStatus, xhr){
            $('#loadingToast').hide();
            if(data.rtn > 0){
                $('#toast').find('.weui_toast_content').html(data.errmsg).end().show();
                setTimeout(function(){;
                    $('#toast').hide();
                }, 2000);
                return false;
            }

            var tmp = data.msg;
            music.page = tmp.current_page-0+1;
            music.total_rows = tmp.total_rows;
            music.total_page = tmp.total_page;

            var html = '';
            tmp = data.msg.data;
            for(var i in tmp){
                html += '<div class="weui_cell" hash="'+tmp[i]['hash']+'" filename="'+tmp[i]['filename']+'"><div class="weui_cell_bd weui_cell_primary"><p>'+tmp[i]['filename']+'</p><p>时长:'+tmp[i]['duration']+'秒\t格式:'+tmp[i]['extname']+'\t码率:'+tmp[i]['bitrate']+'\t尺寸:'+new Number(tmp[i]['filesize']/1024/1024).toFixed(0)+'M</p></div></div>';
            }

            $('#search_show').html(html).show();
            return true;
        }
    });
}