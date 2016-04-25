<?php
/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
class LotteryModel{
    
    
    public function checkLottery($data, $lotteryType){
        $db = Database::getInstance();
        foreach($data as $_k=>$_v){
            //$db->where($_k, $_v, true);
        }
        $query = $db->get('app_'.$lotteryType);
        var_dump($query->row_array());
        exit;

        return $this->fList(array('where'=>$data));
    }
    
    public function selectSample() {
        return 'Hello World!';
    }

    public function insertSample($arrInfo) {
        return true;
    }
}
