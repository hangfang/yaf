<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class TestController extends BasicController{
    
    /**
     * @todo 接口调试页面
     * @param string $module 模块名称
     * @param string $controller 控制器名称
     * @param string $action action方法名称(去掉Action后缀)
     * @author fanghang@fujiacaifu.com
     */
    public function indexAction(){
        $module = $this->_request->getQuery('module', 'api');
        $controller = '/'. ucfirst($module) .'/controllers/'. ucfirst($this->_request->getQuery('controller', 'auth'));
        $action = $this->_request->getQuery('action', 'in');
        
        $wiki = new Wiki();
        $moduleList = $wiki->getModuleList();
        $controllerList = $wiki->getControllerList($module);
        
        $controller = explode('_', $controller);
        $controller = count($controller)>1 ? ucfirst($controller[0]).'_'.ucfirst($controller[1]) : ucfirst($controller[0]);
        $params = isset($controllerList[$controller]['actions'][$action]['param']) ? $controllerList[$controller]['actions'][$action]['param'] : array();

        $this->_view->assign('module', $module);
        $this->_view->assign('controller', $controller);
        $this->_view->assign('action', $action);
        
        $this->_view->assign('modules', $moduleList);
        $this->_view->assign('controllers', $controllerList);

        $this->_view->assign('method', isset($controllerList[$controller]['actions'][$action]['method']) ? $controllerList[$controller]['actions'][$action]['method']:'POST');
        $this->_view->assign('todo', isset($controllerList[$controller]['actions'][$action]['todo']) ? $controllerList[$controller]['actions'][$action]['todo']:'未填写');
        $this->_view->assign('function', isset($controllerList[$controller]['actions'][$action]['function']) ? $controllerList[$controller]['actions'][$action]['function']:'未填写');
        $this->_view->assign('params', $params);
        $this->_view->assign('return', isset($controllerList[$controller]['actions'][$action]['return']) ? $controllerList[$controller]['actions'][$action]['return']:'未填写');
        $this->_view->assign('table', isset($controllerList[$controller]['actions'][$action]['table']) ? $controllerList[$controller]['actions'][$action]['table']:'未填写');
        
        $this->_view->assign('title', 'Saas接口调试系统');
    }
    
    /**
     * @todo 对请求参数生成签名
     * @return array
     * @author fanghang@fujiacaifu.com
     */
    public function signatureAction(){
        $params = array_merge_recursive($this->_request->getQuery(), $this->_request->getPost());

        unset($params['interface'], $params['method'], $params['s'], $params['sign']);

        ksort($params);
        
        lExit(array('code'=>0, 'sign'=>md5(http_build_query($params))));
        return false;
    }
}