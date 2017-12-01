<?php require $viewPath.'/common/header.php';?>
<div class="row-fluid">
<div class="row">
    <nav role="navigation" class="navbar navbar-default">
        <div class="navbar-header">
            <button data-target="#example-navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                <span class="sr-only">切换导航</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="#" class="navbar-brand"><?php echo $title;?></a>
        </div>
        <div id="example-navbar-collapse" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                </li>
                <li><span>&nbsp;&nbsp;&nbsp;&nbsp;</span></li>
                <?php 
                    foreach($modules as $name => $m){
                        echo '<li><a href="/index/test/index?module='. $name .'" class="'. ($name == $module ? 'active' : '') .'">'. $m['name'] .'('. $name .')</a></li>';
                    }
                ?>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li id="accountBtn" <?php echo cookie('userid') ? '' : 'style="display:none"';?> class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="loginInfo-account"><?php echo cookie('realname') ? cookie('realname') : 'ACCOUNT';?><span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a id="loginInfo-sid"><?php echo cookie('realname') ? cookie('realname') : 'USERNAME';?></a></li>
                        <li class="divider"></li>
                        <li><a href="/api/auth/logout?jto=<?php echo BASE_URL;?>" id="logoutBtn">退出登录</a></li>
                    </ul>
                </li>
                <li id="" class="dropdown">
                    <a href="https://www.pgyer.com/V2gP" class="dropdown-toggle" data-toggle="dropdown" id="">IOS公测app下载<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><img src="http://saas-static.oss-cn-shenzhen.aliyuncs.com/app/qrcode/ios.png"></img></li>
                        <li class="divider"></li>
                    </ul>
                </li>
                <li id="" class="dropdown">
                    <a href="https://www.pgyer.com/qX5s" class="dropdown-toggle" data-toggle="dropdown" id="">安卓公测app下载<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><img src="http://saas-static.oss-cn-shenzhen.aliyuncs.com/app/qrcode/android.png"></img></li>
                        <li class="divider"></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>

<div class="row">
    <div class="col-md-2">
        <div class="panel-group" id="accordion2">
            <?php 
                foreach($controllers as $cname=>$c){
                
                    $apiPath = explode("/",$cname);
                    $moduleName = isset($apiPath["1"]) ? strtolower($apiPath["1"]) : "";
                    $controllerName = isset($apiPath["3"]) ? strtolower($apiPath["3"]) : "";
            ?>
                <div class="panel panel-info">
                    <div class="panel-heading" data-toggle="collapse" data-parent="#accordion2" href="#collapse<?php echo $moduleName.$controllerName;?>">
                        <a href="#" class="accordion-toggle" style="text-decoration:none;display: block;outline: none" title="<?php echo '【controller】'.$cname;?>"><?php echo $c['todo'].'('.$controllerName.')';?></a>
                    </div>
                    <div id="collapse<?php echo $moduleName.$controllerName;?>" class="list-group panel-collapse collapse<?php echo strpos(strtolower($controller), '/'.$moduleName.'/controllers/'.$controllerName)===false ? '': ' in';?>">
                        <?php
                            foreach($c['actions'] as $aname=>$a){
                                $actionName = strtolower($aname);
                                $apiUri = "/".$moduleName."/".$controllerName."/".$actionName;
                                
                                echo '<a href="/index/test/index?module='. $module .'&controller='. $controllerName .'&action='. $aname .'" title="【controller】'.$cname."\n".'【action】'.$aname .'" id="collapse'.$aname.'" class="list-group-item'.($action==$aname ? ' active': '') .'">'. $a['todo'].$apiUri .'</a>';
                            }
                        ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div id="ouputPannel" class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">接口调试</h3>
            </div>
            <div class="panel-body">
                <?php if(empty($controller) || empty($action)){?>
                    未选择接口或获取参数失败
                <?php }else{ ?>
                    <form id="invokeForm" class="form-horizontal" role="form" method="<?php echo trim(str_replace('<br />','',$method));?>" action="<?php $tmp = explode('/', $controller);echo '/'.strtolower($module).'/'.strtolower($tmp[3]).'/'.strtolower($action);?>">
                        <?php foreach($params as $i=>$p){?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="param-<?php echo $i;?>-<?php echo $p['name'];?>"><?php echo $p['todo'];?></label>
                                <div class="col-sm-9">
                                    <?php if(!empty(cookie('uid')) && $p['name'] == 'uid') {
                                        echo '<input type="text" id="param-'. $i .'-'. $p['name'] .'" class="form-control" name="'. $p['name'] .'" value="'. cookie('uid') .'" placeholder="'. $p['type'].' '.$p['name'] .'">';
                                    } else if(!empty(cookie('sid')) && $p['name'] == 'sid'){
                                        echo '<input type="text" id="param-'. $i .'-'. $p['name'] .'" class="form-control" name="'. $p['name'] .'" value="'. cookie('sid') .'" placeholder="'. $p['type'].' '.$p['name'] .'">';
                                    } else {
                                        if($p['type'] == 'file'){
                                            echo '<input type="file" onclick="" name="'.$p['name'].'" placeholder="" value="">';
                                        }else{
                                            $defaultValue= htmlentities(str_replace(array('(',')'), array('', ''), $p['detail']));
                                            echo '<input type="text" id="param-'. $i .'-'. $p['name'] .'" class="form-control" name="'. $p['name'] .'" value="'. $defaultValue .'" placeholder="'. $p['type'].' '.$p['name'] .'">';
                                        }
                                    }?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-10">
                                <button type="button" class="btn btn-danger" id="invokeBtn">调用接口</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="" id="apiUrl" style="word-break: break-all;" target="_blank"></a>
                            </div>
                        </div>
                    </form>
                    <form style="display:none;" id="hiddenForm" class="form-horizontal" role="form" method="<?php echo trim(str_replace('<br />','',$method));?>" action="<?php $tmp = explode('/', $controller);echo '/'.strtolower($module).'/'.strtolower($tmp[3]).'/'.strtolower($action);?>">
                        <input type="hidden" value="" name="request" id="request"/>
                    </form>
                <?php } ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <ul class="nav nav-pills">
                    <li class="active" ><a href="#result" data-toggle="tab">输出结果</a></li>
                    <li><a href="#thrift" data-toggle="tab">DEBUG输出</a></li>
                    <li style="float:right;">
                        <a id="outputExpand" href="javascript:;" style="color:gray">展开&gt;&gt;</a>
                        <a id="outputCollapse" href="javascript:;" style="color:gray;display: none">&lt;&lt;缩回</a>
                    </li>
                </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="result">未调用或调用失败</div>
                    <div class="tab-pane" id="thrift">未调用或调用失败</div>
                </div>
            </div>
        </div>
    </div>

    <div id="descPannel" class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">接口说明</h3>
            </div>
            <div class="panel-body">
                <p><span style="display: inline-block;padding-right: 5px;font-weight: bold;">调用地址:</span>
                    <?php $tmp = explode('/', $controller);echo '/'.strtolower($module).'/'.strtolower($tmp[3]).'/'.strtolower($action);?></p>
                <p><span style="display: inline-block;padding-right: 5px;font-weight: bold;">HTTP方法:</span><?php echo $method?></p>
                <p><span style="display: inline-block;padding-right: 5px;font-weight: bold;">接口功能:</span><?php echo $todo?></p>
                <p><span style="display: inline-block;padding-right: 5px;font-weight: bold;">功能详述:</span><?php echo str_replace('<br&nbsp;/>','<br />',str_replace(' ','&nbsp;',$function));?></p>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">调用参数说明</h3>
            </div>
            <div class="panel-body">
                <?php if(!empty($params)){?>
                    <?php foreach($params as $i=>$p){?>
                        <?php //var_dump($p);  ;?>
                        <?= $p['name'].' '.$p['default'].' '.$p['todo'].' '.$p['detail'].'<br />' ?>
                    <?php } ?>
                <?php }else{ ?>
                    未选择接口或获取参数失败
                <?php } ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title" style="font-weight: bold;color:red">表结构说明</h3>
            </div>
            <div class="panel-body">
                <?php if(!empty($table)) {
                    $table = preg_replace('/<br\s*\/>/', ' ', $table);
                    $table = preg_replace('/\s+/', ',', $table);
                    $table = preg_replace('/[^a-z0-9_\.,]/', '', $table);
                    $table = explode(',', $table);
                    $prefix = 'http://gitlab.fujia.com/saas/wiki/blob/master/';
                    foreach($table as $_table){
                        if(!strlen(trim($_table))){
                            continue;
                        }
                        $_table = explode('.', $_table);
                        echo '<a href="'.$prefix.$_table[0].'/'.$_table[1].'.md" target="_blank">'. $_table[1] .'</a><br/>';
                    }
                }else{
                    echo '未填写';
                }?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">返回值说明</h3>
            </div>
            <div class="panel-body">
                <?php if(!empty($return)) {
                    echo str_replace('<br&nbsp;/>','<br />',str_replace(' ','&nbsp;',  nl2br($return)));
                }else{
                    echo '未填写';
                }?>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    $(function(){
        var userLoginUrl = '/api/user/in';
        var folder = '<?php echo $staticDir;?>';

        $('#invokeBtn').click(function(e){
            $("#result").html("");
                    
            var formData = $('#invokeForm').serializeArray();
            var excel = $('#invokeForm').find('input[name=excel]');
            if(excel.length){
                $('#hiddenForm').find('input[name=excel]').remove().end().attr('enctype', 'multipart/form-data').append(excel.clone());
            }
            
            var img = $('#invokeForm').find('input[name=img]');
            if(img.length){
                $('#hiddenForm').find('input[name=img]').remove().end().attr('enctype', 'multipart/form-data').append(img.clone());
            }
            
            var media = $('#invokeForm').find('input[name=media]');
            if(media.length){
                $('#hiddenForm').find('input[name=media]').remove().end().attr('enctype', 'multipart/form-data').append(media.clone());
            }
                
            var request = {};
            for(var i=0,len=formData.length; i<len; i++){
                request[formData[i]['name']] = formData[i]['value'];
            }

            $('#request').val(JSON.stringify(request));
            
            var loadIndex = "";
            var options = {
                beforeSubmit: function (paramsObj) {
                    var formActionUrl = $("#invokeForm").attr("action");
                    reStoreInputVal(paramsObj, formActionUrl);
                    loadIndex = layer.load(1);
                },
                success: function (resp) {
                    layer.close(loadIndex);
                    //api调用url链接
                    $QueryUrl = $(this)[0]['url'];
                    $("#apiUrl").attr("href", $QueryUrl);
                    $("#apiUrl").html($QueryUrl);

                    new JsonFormater({
                        dom: '#result',
                        imgCollapsed: folder + "/jsonformater/images/Collapsed.gif",
                        imgExpanded: folder + "/jsonformater/images/Expanded.gif"
                        //isCollapsible: $('#CollapsibleView').prop('checked'),
                        //quoteKeys: $('#QuoteKeys').prop('checked'),
                        //tabSize: $('#TabSize').val()
                    }).doFormat(resp);

                    if (typeof resp !== 'object') {
                        var resp = $.parseJSON(resp);
                    }
                },
                error: function (error) {
                    layer.close(loadIndex);
                    var status = error.status;
                    var statusText = error.statusText;
                    var errorMsg = "ErrorCode:" + status + '，ErrorMsg:' + statusText;
                    layer.msg(errorMsg);
                }
            }
            $('#hiddenForm').ajaxSubmit(options);
        });

        $('#outputExpand').click(function(){
            $('#ouputPannel').addClass('col-md-10').removeClass('col-md-5');
            $('#descPannel').hide();
            $(this).hide();
            $('#outputCollapse').show();
        });
        $('#outputCollapse').click(function(){
            $('#ouputPannel').addClass('col-md-5').removeClass('col-md-10');
            $('#descPannel').show();
            $(this).hide();
            $('#outputExpand').show();
        });
        
        $('#refresh').click(function(e){
            $.ajax({
                url:'/api/auth/token?rd='+ Math.random(),
                success: function(data, textStatus, xhr){
                    if(!data){
                        $('#loginErrorText').text('获取验证码失败').show();
                        return false;
                    }
                    
                    if(data.code>0){
                        $('#loginErrorText').text(data.error_msg).show();
                        return false;
                    }
                    
                    $(e.target).attr('src', data.captcha);
                }
            });
        });
        
        $('#loginBtn').click(function(e){
            e.preventDefault();
            var timestamp = (new Date()).valueOf();
            $.getJSON(signUrl+'?'+$('#loginForm').serialize()+'&interface='+$('#loginForm').attr('action')+'&method='+$('#loginForm').attr('method'),function(data){
                if(data.code > 0){
                    $('#loginErrorText').html(data.error_msg).show();
                    return false;
                }
                $('#loginForm-signInput').val(data.sign);
                $('#loginErrorText').hide();
                $('#loginForm').ajaxSubmit(function(result){
                    if(typeof result !== 'object'){
                        var result = $.parseJSON(result);
                    }
                    if(result.code==0){
                        $('#accountBtn').show();
                        $('#loginModalBtn').hide();

                        $('#loginModal').modal('hide');
                    } else {
                        $('#loginErrorText').text(result.error_msg + '[code=' + result.code +']').show();
                    }
                });
            });
        });
    });
</script>

<script>
    //将调用参数存入localStorage浏览器缓存中
    function reStoreInputVal(paramsObj,apiUrl)
    {
        if(window.localStorage){
            for( var key in paramsObj ){
                var inputName = paramsObj[key]['name'];
                var inputValue = paramsObj[key]['value'];
                var inputType = paramsObj[key]['type'];
                if(inputType=='hidden'){
                    continue;
                }
                var localStorageKey = apiUrl+'_'+inputName;
                localStorage.setItem(localStorageKey,inputValue);
            }
        }
    }

    //从缓存中将参数填充到输入框中
    function rePutDataToInputFromlocalStorageData()
    {
        var inputObj = $('#invokeForm').find('input');

        if(!inputObj){
            return false;
        }

        var formActionUrl = $("#invokeForm").attr("action");

        for(var i=0 ; i<inputObj.length; i++ )
        {
            var inputType = $(inputObj[i]).attr("type");
            var inputName = $(inputObj[i]).attr("name");
            if(inputType=='hidden' || inputName=='uid' || inputName=='phone' || inputName=='sid' || inputName=='account' ){
                continue;
            }

            var localStorageKey = formActionUrl+'_'+inputName;

            var cacheValue = localStorage.getItem(localStorageKey);
            if(cacheValue){
                $(inputObj[i]).val(cacheValue);
            }
        }
    }
    rePutDataToInputFromlocalStorageData();
</script>