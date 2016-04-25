var txmap = {};
txmap.latLng = new qq.maps.LatLng('22.5428234337', '114.0595370000');
txmap.autocomplete = new qq.maps.place.Autocomplete(document.getElementById('keyword'), {location: $('#region').val()});

txmap.map = new qq.maps.Map(document.getElementById("container"), {
        // 地图的中心地理坐标。
        center: txmap.latLng,
        zoom: 15
    });
txmap.drawingManager = new qq.maps.drawing.DrawingManager({
        drawingMode: qq.maps.drawing.OverlayType.MARKER,
        drawingControl: true,
        drawingControlOptions: {
            position: qq.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
                qq.maps.drawing.OverlayType.MARKER,
                qq.maps.drawing.OverlayType.CIRCLE,
                qq.maps.drawing.OverlayType.POLYGON,
                qq.maps.drawing.OverlayType.POLYLINE,
                qq.maps.drawing.OverlayType.RECTANGLE
            ]
        },
        circleOptions: {
            fillColor: new qq.maps.Color(255, 208, 70, 0.3),
            strokeColor: new qq.maps.Color(88, 88, 88, 1),
            strokeWeight: 3,
            clickable: false
        }
    });
txmap.drawingManager.setMap(txmap.map);

txmap.markers = [];

txmap.setMarker = function(marker, text){
    //设置Marker的可见性，为true时可见,false时不可见，默认属性为true
    marker.setVisible(true);
    //设置Marker的动画属性为从落下
    marker.setAnimation(qq.maps.MarkerAnimation.DOWN);
    //设置Marker是否可以被拖拽，为true时可拖拽，false时不可拖拽，默认属性为false
    marker.setDraggable(false);

    //设置标注的名称，当鼠标划过Marker时显示
    marker.setTitle(text);

    //添加信息窗口
    var info = new qq.maps.InfoWindow({
        map: txmap.map
    });
    //标记Marker点击事件
    qq.maps.event.addListener(marker, 'click', function() {
        info.open();
        info.setContent(text);
        info.setPosition(marker.getPosition());
    });
    //设置Marker停止拖动事件
    qq.maps.event.addListener(marker, 'dragend', function() {
        info.open();
        info.setContent(text);
        //getPosition()  返回Marker的位置
        info.setPosition(marker.getPosition());
    });
    
    txmap.markers.push(marker);
};

txmap.clearMarkers = function() {
    var marker;
    while (marker = txmap.markers.pop()) {
        marker.setMap(null);
    }
};
    
//设置搜索的范围和关键字等属性
txmap.searchKeyword = function(){
    var keyword = $("#keyword").val();
    var region = $("#region").val();
    var pageIndex = parseInt($("#pageIndex").val());
    var pageCapacity = parseInt($("#pageCapacity").val());

    this.clearMarkers();

    //根据输入的城市设置搜索范围
    this.searchService.setLocation(region);
    //设置搜索页码
    this.searchService.setPageIndex(pageIndex);
    //设置每页的结果数
    this.searchService.setPageCapacity(pageCapacity);
    //根据输入的关键字在搜索范围内检索
    this.searchService.search(keyword);
    //根据输入的关键字在圆形范围内检索
    //var region = new qq.maps.LatLng(39.916527,116.397128);
    //searchService.searchNearBy(keyword, region , 2000);
    //根据输入的关键字在矩形范围内检索
    //region = new qq.maps.LatLngBounds(new qq.maps.LatLng(39.936273,116.440043),new qq.maps.LatLng(39.896775,116.354212));
    //searchService.searchInBounds(keyword, region);

};

txmap.searchService = new qq.maps.SearchService({
    map: txmap.map,
    //检索成功的回调函数
    complete: function(results) {
        //设置回调函数参数
        var pois = results.detail.pois;
        var infoWin = new qq.maps.InfoWindow({
            map: txmap.map
        });
        var latlngBounds = new qq.maps.LatLngBounds();

        for (var i = 0, l = pois.length; i < l; i++) {
            var poi = pois[i];
            //扩展边界范围，用来包含搜索到的Poi点
            latlngBounds.extend(poi.latLng);

            (function(n) {
                var marker = new qq.maps.Marker({
                    map: txmap.map
                });
                marker.setPosition(pois[n].latLng);

                marker.setTitle(i + 1);
                txmap.markers.push(marker);


                qq.maps.event.addListener(marker, 'click', function() {
                    infoWin.open();
                    infoWin.setContent('<div style="width:280px;height:100px;">' + 'POI的ID为：' +
                        pois[n].id + '，POI的名称为：' + pois[n].name + '，POI的地址为：' + pois[n].address + '，POI的类型为：' + pois[n].type + '</div>');
                    infoWin.setPosition(pois[n].latLng);
                });
            })(i);
        }
        //调整地图视野
        txmap.map.fitBounds(latlngBounds);
    },
    //若服务请求失败，则运行以下函数
    error: function() {
        alert("出错了。");
    }
});
    
txmap.init = function() {
    qq.maps.event.addListener(this.map, 'click', function(e){
        
    });
    
    qq.maps.event.addListener(this.map, 'mousemove', function(e){
        //console.log(e);
    });

    qq.maps.event.addListener(this.map, 'dblclick', function(e){
        $('.weui_actionsheet_cell').data(e);
        if(txmap.map.getZoom()===18){
            var mask = $('#mask');
            var weuiActionsheet = $('#weui_actionsheet');
            weuiActionsheet.addClass('weui_actionsheet_toggle');
            mask.show().addClass('weui_fade_toggle').one('click', function () {
                hideActionSheet(weuiActionsheet, mask);
            });
            $('#actionsheet_cancel').one('click', function () {
                hideActionSheet(weuiActionsheet, mask);
            });
            weuiActionsheet.unbind('transitionend').unbind('webkitTransitionEnd');

            function hideActionSheet(weuiActionsheet, mask) {
                weuiActionsheet.removeClass('weui_actionsheet_toggle');
                mask.removeClass('weui_fade_toggle');
                weuiActionsheet.on('transitionend', function () {
                    mask.hide();
                }).on('webkitTransitionEnd', function () {
                    mask.hide();
                })
            } 
        }
    });

    qq.maps.event.addListener(this.map, 'rightclick', function(e){
        $('.weui_actionsheet_cell').data(e);

        var mask = $('#mask');
        var weuiActionsheet = $('#weui_actionsheet');
        weuiActionsheet.addClass('weui_actionsheet_toggle');
        mask.show().addClass('weui_fade_toggle').one('click', function () {
            hideActionSheet(weuiActionsheet, mask);
        });
        $('#actionsheet_cancel').one('click', function () {
            hideActionSheet(weuiActionsheet, mask);
        });
        weuiActionsheet.unbind('transitionend').unbind('webkitTransitionEnd');

        function hideActionSheet(weuiActionsheet, mask) {
            weuiActionsheet.removeClass('weui_actionsheet_toggle');
            mask.removeClass('weui_fade_toggle');
            weuiActionsheet.on('transitionend', function () {
                mask.hide();
            }).on('webkitTransitionEnd', function () {
                mask.hide();
            })
        }
    });

    //根据指定的范围调整地图视野。
    //map.fitBounds(latlngBounds);
    qq.maps.event.addListener(this.map, 'bounds_changed', function () {
        //console.log("地图的可视范围为：" + map.getBounds());
    });


    qq.maps.event.addListener(this.map, 'center_changed', function () {
        //console.log("地图中心为：" + map.getCenter());
    });


    //当地图缩放级别更改时会触发此事件。
    qq.maps.event.addListener(this.map, 'zoom_changed', function () {
        //console.log("地图缩放级别为：" + map.getZoom());
        if(txmap.map.getZoom()===18){
            var mask = $('#mask');
            var weuiActionsheet = $('#weui_actionsheet');
            weuiActionsheet.addClass('weui_actionsheet_toggle');
            mask.show().addClass('weui_fade_toggle').one('click', function () {
                hideActionSheet(weuiActionsheet, mask);
            });
            $('#actionsheet_cancel').one('click', function () {
                hideActionSheet(weuiActionsheet, mask);
            });
            weuiActionsheet.unbind('transitionend').unbind('webkitTransitionEnd');

            function hideActionSheet(weuiActionsheet, mask) {
                weuiActionsheet.removeClass('weui_actionsheet_toggle');
                mask.removeClass('weui_fade_toggle');
                weuiActionsheet.on('transitionend', function () {
                    mask.hide();
                }).on('webkitTransitionEnd', function () {
                    mask.hide();
                })
            }
        }
    });

    qq.maps.event.addListener(this.map, 'maptypeid_changed', function () {
        //console.log("地图类型ID为：" + map.getMapTypeId());
    });
    
    qq.maps.event.addListener(this.autocomplete, "confirm", function(res){console.log(res);
        txmap.searchService.search(res.value);
    });
    
    $('body').on('click', '.weui_actionsheet_cell:eq(0)', function(e){
        /*--start---创建街景--start---*/
        var latLng = $(this).data().latLng;
        pano_service = new qq.maps.PanoramaService();
        var point = {lat: latLng.lat, lng: latLng.lng};
        var radius;
        pano_service.getPano(point, radius, function (result){
            pano = new qq.maps.Panorama(document.getElementById('container'), {
                //pano: '10011501120802180635300',    //场景ID
                pov:{   //视角
                        heading:1,  //偏航角
                        pitch:0     //俯仰角
                    },
                zoom:1      //缩放
            })
            
            pano.setPano(result.svid);
        });

        $('#mask').click();
        /*---end----创建街景---end----*/
    });

    $('body').on('click', '.weui_actionsheet_cell:eq(1)', function(e){
        $('#loadingToast').find('.weui_toast_content').html('敬请期待').end().show();
        setTimeout(function(){
            $('#loadingToast').hide();
        }, 1000);
    });

    $('body').on('click', '.weui_actionsheet_cell:eq(2)', function(e){
        $('#loadingToast').find('.weui_toast_content').html('敬请期待').end().show();
        setTimeout(function(){
            $('#loadingToast').hide();
        }, 1000);
    });

    $('body').on('click', '.weui_actionsheet_cell:eq(3)', function(e){
        $('#loadingToast').find('.weui_toast_content').html('敬请期待').end().show();
        setTimeout(function(){
            $('#loadingToast').hide();
        }, 1000);
    });
    
    $('body').on('change', '#keyword', function(e){
        var marker = null;
        while(marker = txmap.markers.pop()){
            marker.setMap(null);
        }
    });
//        var times = 0;
//        var oInterval = setInterval(function () {
//
//            //panBy()将地图中心移动一段指定的距离（以像素为单位）。
//            map.panBy(-100, 100);
//
//            //zoomBy()将地图缩放到指定的缩放比例（每次所增加的数值）。
//            map.zoomBy(5);
//            times++;
//            if (times >= 1) {
//                clearInterval(oInterval)
//            }
//        }, 3 * 1000);
//
//
//        setTimeout(function () {
//
//            //panTo()将地图中心移动到指定的经纬度坐标。
//            map.panTo(new qq.maps.LatLng(39.9, 116.4));
//
//            //zoomTo()将地图缩放到指定的级别。
//            map.zoomTo(15);
//
//        }, 8 * 1000);
//
//
//        setTimeout(function () {
//            //setCenter()设置地图中心点坐标。
//            map.setCenter(new qq.maps.LatLng(30, 117));
//
//            //setZoom()设置地图缩放级别。
//            map.setZoom(6);
//
//            //setMapTypeId()设置地图类型。
//            map.setMapTypeId(qq.maps.MapTypeId.HYBRID);
//
//        }, 15 * 1000);
//
//
//        setTimeout(function () {
//
//            //设置地图参数。
//            map.setOptions({
//                keyboardShortcuts: false,
//                scrollwheel: false
//            });
//
//        }, 30 * 1000);
};

$(function(){
    txmap.init();

    if(openInWechat){
        wx.ready(function(res){
            wx.getLocation({
                type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                    txmap.latLng = new qq.maps.LatLng(res.latitude, res.longitude);
                    txmap.speed = res.speed; // 速度，以米/每秒计
                    txmap.accuracy = res.accuracy; // 位置精度

                    txmap.map.setCenter(txmap.latLng);
                    var marker = new qq.maps.Marker({//设置marker标记
                        map: txmap.map,
                        position: txmap.latLng
                    });
                    
                    txmap.setMarker(marker, '当前位置');
                    
                    citylocation = new qq.maps.CityService({
                        //设置地图
                        map : txmap.map,

                        complete : function(results){
                            $('#region').val(results.detail.name);
                        }
                    });
                    citylocation.searchCityByLatLng(txmap.latLng);
                }
            });
        });
    }else{
        if($('#client_ip').val().match(/^127\./) || $('#client_ip').val().match(/^10\./)){
            $('#loadingToast').find('.weui_toast_content').html('获取位置失败').end().show();
            setTimeout(function(){
                $('#loadingToast').hide();
            }, 1500);
            
        }else{
            //获取  城市位置信息查询 接口  
            citylocation = new qq.maps.CityService({
                //设置地图
                map : txmap.map,

                complete : function(results){
                    txmap.latLng = results.detail.latLng;
                    txmap.map.setCenter(txmap.latLng);
                    var marker = new qq.maps.Marker({//设置marker标记
                        map: txmap.map,
                        position: txmap.latLng
                    });

                    txmap.setMarker(marker, '当前位置');
                    $('#region').val(results.detail.name);
                }
            });
            citylocation.searchLocalCity();
        }
    }
});