<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Wiki{
    /**
     * @todo 查询模块列表
     * @return array
     * @author fanghang@fujiacaifu.com
     */
    public function getModuleList($moduleName=''){
        $module = array();
        
        $dirs = scandir(APPLICATION_PATH.'/application/modules/');
        foreach($dirs as $_dir){
            if(preg_match('/^[\.]+$/', $_dir) || preg_match('/^index\.html$/', $_dir)){
                continue;
            }
            
            if($moduleName!='' && strtolower($_dir)!==strtolower($moduleName)){
                continue;
            }

            $reflectClass = $this->getReflectionModuleClass($_dir);

            //检测是否存在开关标记
            if(!preg_match('/@test-enable/', $reflectClass->getDocComment())){
                continue;
            }
            
            $properties = $this->extractProperty($reflectClass->getDocComment(), 'test-');
            foreach(array('id', 'name') as $_value){
                if(empty($properties[$_value])){
                    lExit(4, sprintf('模块[%s]缺少注解属性@test-%s', $_dir, $_value));
                }
            }

            $modules[ucfirst(strtolower($_dir))] = $properties;
        }
        
        return $modules;
    }
    
    /**
     * @todo 查询控制器列表
     * @param type $module 模块目录名称(Linux区分大小写)
     * @return array
     * @author fanghang@fujiacaifu.com
     */
    public function getControllerList($module, $controller=''){
        $module = ucfirst($module);
        $controllers = array();

        $dirs = scandir(APPLICATION_PATH.'/application/modules/'. $module .'/controllers/');
        foreach($dirs as $_dir){
            if($module.'Module'===$_dir){//模块基类不出现在接口列表
                $classPath = APPLICATION_PATH.'/application/modules/'. $module .'/controllers/'. $_dir;
                if(!file_exists($classPath)){
                    lExit(4, '模块控制器不存在, controller: '. $_dir);
                }else{
                    Yaf_Loader::import($classPath);
                }
                continue;
            }
            
            if(preg_match('/^[\.]+$/', $_dir) || preg_match('/^index\.html$/', $_dir)){
                continue;
            }
            
            $classPath = APPLICATION_PATH.'/application/modules/'. $module .'/controllers/'. $_dir;
            if(is_dir($classPath)){
                $subDirs = scandir($classPath);
                foreach($subDirs as $_subDir){
                    if(preg_match('/^[\.]+$/', $_subDir) || preg_match('/^index\.html$/', $_subDir)){
                        continue;
                    }

                    $subClassPath = $classPath . '/'.$_subDir;
                    //每个module下的controllers类
                    if(!file_exists($subClassPath)){
                        lExit(4, '模块控制器不存在, controller: '. $_subDir);
                    }else{
                        Yaf_Loader::import($subClassPath);
                    }
                    
                    $reflectClass = new \ReflectionClass(ucfirst($_dir).'_'.substr($_subDir, 0, -4).'Controller');
                    $reflectMethods = $reflectClass->getMethods(\ReflectionMethod::IS_PUBLIC);
                    $actions = $this->getActions($_subDir, ucfirst($_dir).'_'.substr($_subDir, 0, -4), $subClassPath);
                    $controllers['/'. $module .'/controllers/'. $_dir.'_'.substr($_subDir, 0, -4)] = array_merge(array(
                        'id' => $_dir.'_'.substr($_subDir, 0, -4),
                        'actions' => $actions,
                        'todo' => '未填写todo'
                    ), $this->extractProperty($reflectClass->getDocComment()));
                }
                continue;
            }

            if($controller!='' && strtolower($tmp)!==strtolower($controller)){
                continue;
            }

            //每个module下的controllers类
            if(!file_exists($classPath)){
                lExit(4, '模块控制器不存在, controller: '. $_dir);
            }else{
                Yaf_Loader::import($classPath);
            }
            
            $controllerName = substr($_dir, 0, -4);//去掉尾部的.php
            $reflectClass = new \ReflectionClass(ucfirst($controllerName).'Controller');
            $reflectMethods = $reflectClass->getMethods(\ReflectionMethod::IS_PUBLIC);
            
            $actions = $this->getActions($_dir, $controllerName, $classPath);
            $controllers['/'. $module .'/controllers/'. $controllerName] = array_merge(array(
                'id' => $controllerName,
                'actions' => $actions,
                'todo' => '未填写todo'
            ), $this->extractProperty($reflectClass->getDocComment()));
        }
        return $controllers;
    }
    
    private function getActions($_dir, $controllerName, $classPath){
        
        $actions = array();

        $reflectClass = new \ReflectionClass(ucfirst($controllerName).'Controller');
        $reflectMethods = $reflectClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach($reflectMethods as $_method){
            $methodName = $_method->getName();
            if(strtolower(substr($methodName, -6))==='action'){
                $methodName = substr($methodName, 0,  -6);
                $actions[$methodName] = array_merge(array(
                    'id'=>$methodName,
                    'todo'=>'未填写todo'
                ), $this->extractProperty($_method->getDocComment()));
            }
        }
        
        return $actions;
    }
        
    /**
     * @todo 实例化模块
     * @param type $module 模块目录名称(Linux区分大小写)
     * @return class
     * @author fanghang@fujiacaifu.com
     */
    private function getReflectionModuleClass($module){
        
        $class = APPLICATION_PATH .'/application/modules/'. ucfirst($module) .'/'. ucfirst($module) .'Module.php';
        if(!file_exists($class)){
            lExit(4, '缺少模块定义文件. filename: '. $class);
        }else{
            Yaf_Loader::import($class);
        }

        return new \ReflectionClass(ucfirst($module) .'Module');
    }
    
    /**
     * @todo 提取注解属性
     * @param $comment
     * @param string $prefix
     * @return array
     * @author fanghang@fujiacaifu.com
     */
    private function extractProperty($comment, $prefix='')
    {
        $properties = [];
        if(preg_match_all('/@'.$prefix.'([a-zA-Z]+)\b([^@]+)/u', $comment, $matches))
        {
            for($i=0; $i<count($matches[0]); $i++) {
                if(in_array($matches[1][$i], ['param'])) {
                    $properties[$matches[1][$i]][] = $this->extractParamInfo(str_replace('*','',trim($matches[2][$i], '/')));
                } else{
                    //$properties[$matches[1][$i]] = nl2br(preg_replace('/^\s*\n/','',str_replace('*','',trim($matches[2][$i], '/'))));
                    $properties[$matches[1][$i]] = preg_replace('/^\s*\n/','',str_replace('*','',trim($matches[2][$i], '/')));
                }
            }
        }

        return $properties;
    }
    
    /**
     * @todo 提取参数
     * @author fanghang@fujiacaifu.com
     */
    private function extractParamInfo($paramInfo)
    {
        if(empty($paramInfo)){
            return array();
        }
        
        $param = array(
            'type'=>'unknown',
            'name'=>'unknown',
            'default'=>null,
            'todo'=>'未填写',
            'detail'=>''
        );

        $part = explode(' ', trim($paramInfo));
        if(!empty($part[0])) $param['type'] = $part[0];
        if(!empty($part[1])) $param['name'] = $part[1];
        if(!empty($part[2])) $param['todo'] = $part[2];
        if(!empty($part[3])) $param['detail'] = nl2br(preg_replace('/[\(\)]/', '', implode(' ',array_slice($part,3))));

        $param['name'] = str_replace('$', '', $param['name']);
        if(strpos($param['name'], '=')){
            list($param['name'], $param['default']) = explode('=', $paramInfo);
        }

        return $param;
    }
}