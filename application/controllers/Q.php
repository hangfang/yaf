<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class QController extends BaseController {
   
    public function loginAction()
    {
        $qq = new Login(15000103);//15000103是广点通的appid
        $login = $qq->login();
        if(!$login){
            echo '@'. date('Y-m-d H:i:s') .'---qq('. $qq->getUin() .') login failed...msg: '. $qq->errorInfo() ."\n";
        }else{
            echo '@'. date('Y-m-d H:i:s') .'---qq('. $qq->getUin() .') login succ...' ."\n";
        }
        
        $param = array(
                        'mod'=>'report',
                        'act'=>'adlist',
                        'owner'=>$qq->getUin(),
                        'unicode'=>true,
                        'g_tk'=>$qq->csrfToken(),
                        'status'=>999,
                        'page'=>$pageNum,
                        'pageSize'=>10,
                        'sdate'=>date('Y-m-d', strtotime('-2 days')),
                        'edate'=>date('Y-m-d', strtotime('-1 days')),
                        'searchname'=>'',
                        'reportonly'=>0,
                        'isHours'=>false,
                        'time_rpt'=>1
        );
        exit($qq->http('http://e.qq.com/ec/api.php?'.http_build_query($param), null, 'GET'));
    }
}
