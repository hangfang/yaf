<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * @name WseController
 * @author root
 * @desc Command Line控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class WsController extends Yaf_Controller_Abstract {
    
    public $_error = array();
	public function init() {
        $this->_error = get_var_from_conf('error');
	}
    
    /**
     * @todo 查询订单状态
     * @method GET
     * @param string flow_id 流水id (00149311932010000790354)
     */
    public function queryAction(){
        $server = new swoole_websocket_server("0.0.0.0", RECHARGE_QUERY_WS_PORT);
        $server->set([
            'heartbeat_idle_time' => 600,
            'heartbeat_check_interval'=> 10,
            'worker_num' => 4,
            'task_worker_num' => 4,
        ]);

        $server->on('open', function (swoole_websocket_server $server, $request) {
            log_message('info', "server: handshake success with fd{$request->fd}");
        });

        $server->on('message', function (swoole_websocket_server $server, $frame) {
            if($frame->opcode == 1){
                $server->task($frame);
            }
        });
        
        $server->on('task', function ($server, $worker_id, $task_id, $data) {
            log_message('info', "worker_id: ". $worker_id .' task_id: '. $task_id ."\ndata: ".json_encode($data));
            
            $cache = Cache::getInstance();
            $key = 'websocket_'.$data->fd;
                
            while(1){
                $return = $this->doQuery($data->data);
                $msg = json_encode($return);
                $value = md5($msg);
                if($cache->hGet($key, 'msg') !== $value){
                    $cache->hSet($key, 'msg', $value);
                    $server->push($data->fd, $msg);
                }
                
                if($return['rtn']==0 && $return['status']==='SUCC'){
                    $server->close($data->fd);
                    log_message('info', 'connection closed, flow_status:'. $return['status']);
                    return true;
                }
                
                unset($return, $msg, $value);
                usleep(3000000);
            }
        });
        
        $server->on('finish', function ($server, $task_id, $result){
            
        });
        
        $server->on('close', function ($ser, $fd) {
            log_message('info', "client {$fd} closed");
            $cache = Cache::getInstance();
            $key = 'websocket_'.$fd;
            $cache->del($key);
        });

        $server->start();
        return false;
    }
    
    private function doQuery($flowId){
        if(strlen($flowId)!==23){
            return $this->_error[1813];
        }

        $rechargeFlow = new RechargeFlowModel();
        //查询支付流水是否存在
        $flow = $rechargeFlow->getFlowByFlowId($flowId);
        if(!$flow){                
            log_message('error', 'query action, query flow failed, flow_id['. $flowId .']');
            return $this->_error[1801];
        }

        //是否重复的回调
        if($flow['flow_status']==='SUCC' || $flow['flow_status']==='FAIL'){
            if(!empty($flow['query_ret'])){
                $data = json_decode($flow['query_ret'], true);
            }else if(!empty($flow['pay_ret'])){
                $data = json_decode($flow['pay_ret'], true);
            }else{
                log_message('error', 'query action, query_ret & pay_ret both empty, flow_id['. $flowId .']');
                return array();
            }
            
            isset($data['detail']['data']['record']) && $data['detail'] = $data['detail']['data']['record'];
            return $data;
        }

        switch($flow['channel']){
            case 'tfb':
                return $this->_tfbQuery($flow);
                break;
            case 'ddb':
                return $this->_ddbQuery($flow);
                break;
        }
    }
    
    private function _tfbQuery($flow){
        $methods = array('card', 'wx', 'ali');
        foreach($methods as $method){
            $payModel = new PayModel($flow['channel']);
            $data = $payModel->payQuery($method, array('c_id'=>$flow['c_id'], 'flow_id'=>$flow['flow_id']));
            
            if($data['rtn']>0){
                continue;
            }

            if($data['status']==='WAIT'){
                continue;
            }
            
            $flow['method'] = $method;
            
            $cache = Cache::getInstance();
            $order = array('flow'=>$flow, 'response'=>$data);
            $MSG_QUEUE_FUNC_NAME = MSG_QUEUE_FUNC_NAME;
            $cache->$MSG_QUEUE_FUNC_NAME('recharge', json_encode($order));
            return $data;
        }
        
        return $data;
    }
    
    private function _ddbQuery($flow){
        $method = 'card';
        $payModel = new PayModel($flow['channel']);
        $data = $payModel->payQuery($method, array('c_id'=>$flow['c_id'], 'flow_id'=>$flow['flow_id']));
        
        if($data['rtn']>0){
            return $data;
        }
        
        if($data['status']==='WAIT'){
            return $data;
        }
        
        $flow['method'] = $method;
        $cache = Cache::getInstance();
        $order = array('flow'=>$flow, 'response'=>$data);
        $MSG_QUEUE_FUNC_NAME = MSG_QUEUE_FUNC_NAME;
        $cache->$MSG_QUEUE_FUNC_NAME('recharge', json_encode($order));

        return $data;
    }
}