var news = {};
$(function(){
    $('#container').on('touchstart', '.container-fluid', function(e){
        event.stopPropagation();
        news.touchstart = event.changedTouches[0].clientY;
    }).on('touchend', '.container-fluid:last', function(e){
        event.stopPropagation();
        news.touchend = event.changedTouches[0].clientY;
        if(news.touchstart-news.touchend > 100){
            
            var queryString = location.search;
            if(queryString){
                var match = queryString.match(/page=(\d*)/);
                if(match){
                    page = Math.max(match[1]-0, 1)+1;
                    location.href = location.pathname+queryString.replace(/page=\d*/, 'page='+page);
                }else{
                    location.href = location.pathname+queryString+'&page=2';
                }
                
                return true;
            }
            
            location.href = location.pathname+'?page=2';
            return true;
        }
    }).on('touchend', '.container-fluid:first', function(e){
        event.stopPropagation();
        news.touchend = event.changedTouches[0].clientY;
        if(news.touchstart-news.touchend < -100){
            var queryString = location.search;
            var match = queryString.match(/page=(\d*)/);
            if(queryString && match){
                page = match ? match[1]-1 : 1;
                if(page>0){
                    location.href = location.pathname+queryString.replace(/page=\d*/, 'page='+page);
                    return true;
                }
            }
            
            $('#dialog2').find('.weui_dialog_bd').html('已经是第一页！').end().show();
            $('#dialog2').one('click', '.weui_btn_dialog', function(e){
                $('#dialog2').hide();
            });
            return true;
        }
    });
    
    $('#switch').on('click', function(e){
        if($('#search input').val().length>0){
            location.href = location.pathname+'?keyword='+encodeURIComponent($('#search input').val());
            return true;
        }
    });
    
    $(window).on('scroll', function(e){
        var switcher = $('#switch');
        if($(document).scrollTop() >= $(document).height() - $(window).height()){
            switcher.prop('checked', true);
        }else{
            switcher.prop('checked', false);
        }
       
       if(switcher.prop('checked')){
            $('#search').show().animate({left: 5}, 0).find('input').focus();
        }else{
            $('#search').hide().animate({left: -300}, 0);
        }
    });
});