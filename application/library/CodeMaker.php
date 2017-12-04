<?php
/**
 * 编码生成类
 */
class CodeMaker
{
    // 编码规则设置相关
    const TABLE_CODE_CONF = 'soft_code';        // 编码规则配置
    const TABLE_CODE_ELEMENTS = 'soft_element'; // 编码规则元素
    const TABLE_CODE_TEMP = 'soft_code_temp';   // 编码规则临时生成记录

    const  BILL_LINE_KEY = 'bill_line';
    private $cache = array();

    protected static $_instance;
    protected static $cachePrefix = '';

    /** @var Database_Drivers_Pdo $db */
    protected $db;
    /** @var  Cache_Drivers_Redis $redis */
    protected $redis;
    protected $barIsUnique = false;

    public function __construct()
    {
        self::$cachePrefix = 'g3.'.BaseModel::getDomain().'.cache.';
        $this->db = Database::getInstance();
        $this->redis = Cache::getInstance('G4');
        $row = G3_BaseConfigModel::getRow(['`key`'=>'barCodeFullLibraryUnique', '`value`'=>1], 'count(`key`) as cnt');
        $this->barIsUnique = $row['cnt'] == 1;
    }

    /**
     * 实例方法
     * @return self
     * @access private
     */
    static public function getInstance()
    {
        if (!(static::$_instance instanceof static)) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * 生成指定表单的编码
     * @param $table
     * @param $type
     * @param $elements
     * @param $fType
     * @param $fId
     * @param $fCode
     * @param string $fWhere
     * @return int|string
     */
    public function createCode($table, $type, $elements, $fType, $fId, $fCode, $fWhere = '')
    {
        // 获得编码的配置信息
        $codeCfg = G3_SoftCodeModel::getRow(['code_name'=>$table, 'code_type'=>$type]);
        $len = $codeCfg['code_len'] ? $codeCfg['code_len'] : 0;     // 如果没有设置长度，默认为0

        if ($len > 10) {    //流水长度不能超过10;除了商品条形码
            $len = 10;
        }

        //取一个规则
        //优先取用户选择的规则；如果用户没有选择，则直接使用初始化设置；如果没有初始化值，则只保留前缀和流水号
        if (!($cRules = trim($codeCfg['code_sign_extends'])) && trim($codeCfg['code_sign_default'])) {
            $cRules = trim($codeCfg['code_sign_default']);
        }

        $join = $codeCfg['code_join'];
        $sign = $codeCfg['code_sign'];

        //取组合前缀（除了流水号外）
        if ($cRules) {
            $fullDate = $this->GetLetter($cRules, $join, $elements);
        } else {
            // code_format 编码格式
            $format = $codeCfg['code_format'];
            $fullDate = '';
            switch ($format) {
                case 'Y':
                {
                    $fullDate .= date("Y");
                }
                    break;
                case 'M':
                {
                    $fullDate .= date("Y") . $join . date("m");
                }
                    break;
                case 'D':
                {
                    $fullDate .= date("Y") . $join . date("m") . $join . date("d");
                }
                    break;
            }
        }
        $fullDateLen = strlen($fullDate);

        $fieldSignLen = strlen($sign); //编码前缀长度
        $fieldJoinLen = strlen($join); //编码段连接符号长度
        $fieldSubLen = $fieldSignLen + $fieldJoinLen;
        $fullDate = trim($fullDate);

        $fieldSumLen = 0;
        $fullData = '';
        if ($sign && trim($fullDate)) {
            $fullData = $sign . $join . $fullDate;
            $fieldSumLen = intval($fieldSubLen + $fullDateLen);
        } elseif ($sign) {
            $fullData = $sign;
            $fieldSumLen = intval($fieldSignLen);
        } elseif (trim($fullDate)) {
            $fullData = $fullDate;
            $fieldSumLen = intval($fullDateLen);
        }

        //得到流水号
        $codeLine = $this->GetLine($fullData, $fieldSumLen, $len, $table, $type, $fType, $fId, $fCode, $fWhere, $codeCfg['code_id'], $join);
        if ($fullData && $codeLine) {
            $soft_code = $fullData . $join . $codeLine;
        } elseif ($fullData) {
            $soft_code = $fullData;
        } elseif ($codeLine) {
            $soft_code = $codeLine;
        } else {
            $soft_code = '';
        }

        return $soft_code;
    }

    //获得编码组合元素部分及流水
    private function GetLetter($signExtends, $join = '', $elements = array())
    {
        //根据不同的字段串查询不同的表中的对应字段的值
        $eleList = G3_SoftElementModel::getList(['element_id'=>$signExtends]);

        //按顺序组合编码元素
        $eleSelected = array();
        $includes = explode(",", $signExtends);
        foreach ($includes as $ele) {
            foreach ($eleList as $rows) {
                if ($rows['element_id'] == $ele) {
                    $eleSelected[] = $rows;
                }
            }
        }

        $elePrev = "";
        foreach ($eleSelected as $rows) {
            $pre = "";
            if ($rows['element_type'] == 'D') { //取日期格式
                if ($element_code = $rows['element_code']) {
                    $pre = date($element_code);
                }
            } elseif ($rows['element_code'] == 'shop_code' && isset($elements[$rows['element_code']])) {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $elements[$rows['element_code']]);
            }

            //加入间隔
            if ($elePrev && $pre) {
                $elePrev .= $join . $pre;
            } elseif (!$elePrev && $pre) {
                $elePrev = $pre;
            }
        }

        return $elePrev;
    }

    /**
     * 获得指定元素的编码数据
     * @param $element_code
     * @param $element_table
     * @param $element_field
     * @param $element_id
     * @return string
     */
    private function GetString($element_code, $element_table, $element_field, $element_id)
    {
        $pre = '';
        if ($element_id) {
            /** @var BaseModel $model */
            $model = 'G3_' . ucfirst(line2Hump($element_table)) . 'Model';
            $rows = $model::getRow([$element_field=>$element_id], "$element_code as str");
            $pre = $rows['str'];
        }

        return $pre;
    }

    /**
     * 获得编码的流水部分
     * @param $fullData
     * @param $fieldSumLen
     * @param $cLen
     * @param $table
     * @param $type
     * @param $fType
     * @param $fId
     * @param $fCode
     * @param $fWhere
     * @param number $codeId 编码配置ID
     * @param string $join 连接字符串
     * @return int|string
     */
    private function GetLine($fullData, $fieldSumLen, $cLen, $table, $type, $fType, $fId, $fCode, $fWhere, $codeId, $join = '')
    {
        $db = Database::getInstance();

        //取得指定表的编码字段的流水号最大值
        //如果长度为0，则返回为空
        if (!$cLen) {
            return '';
        }

        $sumLen = $fieldSumLen + $cLen;
        if ($fieldSumLen > 0) {
            $joinLen = strlen($join);
            $sumLen += $joinLen;
        }

        $getting = true;
        $lLen = $cLen;
        $sLen = $sumLen;
        while ($getting) {
            $sql = "SELECT MAX(RIGHT(`{$fCode}`, {$lLen})+0) AS str1 FROM `{$table}` WHERE `$fId`>0 AND LENGTH(`$fCode`)={$sLen} ";
            if (trim($fWhere) && trim($type)) {
                $sql .= "AND $fType='$type' ";
            }
            if (trim($fWhere)) {
                $sql .= "AND $fWhere ";
            }
            if ($fieldSumLen && $fullData) {
                $sql .= "AND LEFT(`$fCode`, $fieldSumLen)='$fullData'";
            }

            $row = $db->query($sql)[0];
            $maxVal = pow(10, $lLen) - 1;
            if ($getting = ($row['str1'] == $maxVal)) {
                $lLen += 1;
                $sLen += 1;
            }
        }

        $getting = true;
        $codeLenTemp = $cLen;
        $sumLenTemp = $sumLen;
        while ($getting) {
            // 获得临时编码表中的最大
            $sql = "SELECT MAX(RIGHT(`code_value`, {$codeLenTemp})+0) AS str1 FROM `" . self::TABLE_CODE_TEMP . "` WHERE `code_id`='{$codeId}' AND LENGTH(`code_value`)='{$sumLenTemp}' ";
            if ($fieldSumLen && $fullData) {
                $sql .= "AND LEFT(`code_value`, $fieldSumLen) = '$fullData'";
            }

            $cTemp = $db->query($sql)[0];
            $maxVal = pow(10, $codeLenTemp) - 1;
            if ($getting = ($cTemp['str1'] == $maxVal)) {
                $codeLenTemp += 1;
                $sumLenTemp += 1;
            } else {
                if ($cTemp['str1'] > 0 && $cTemp['str1'] > $row['str1']) {
                    $row['str1'] = $cTemp['str1'];
                }
            }
        }

        if ($row['str1'] > 0) {
            //流水码生成
            $str1 = $row['str1'];
            $int1 = (int)($str1);
            $int1 = $int1 + 1;

            $len = $cLen - strlen($int1);
            //流水码补0
            for ($i = 0; $i < $len; $i++) {
                $int1 = "0" . $int1;
            }
            $code = $int1;
        } else {
            //没有取到的情况下生成
            $len = $cLen;
            $int1 = '';
            //流水码补0
            for ($i = 0; $i < $len - 1; $i++) {
                $int1 = "0" . $int1;
            }
            $code = $int1 . "1";
        }

        //判断流水号的第一位数是否为0，如果是0；则补一个符号位即00001，就加10000
        if (strlen($code) != strlen((int)$code)) {
            $lenNum = strlen($code);
            $adds = '';
            for ($i = 0; $i < $lenNum - 1; $i++) {
                $adds = "0" . $adds;
            }

            //没有前缀，则在前面加上符号位
            if ($fieldSumLen == $lenNum || !$fieldSumLen) {
                $adds_num = (int)('1' . $adds);
                $code0 = $adds_num + $code;
            } else {
                $code0 = $code;
            }
            //如果超出正常数字，则转为为字符
            if (2147483648 == $code0) {
                $code = 'a' . $code;
            } else {
                $code = $code0;
            }
        }

        return $code;
    }

    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    // * * * * * * * * * * * * * * * * * * * 单据编码 * * * * * * * * * * * * * * * * * * * * *
    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    public static function getTempBillCode() {
        return date('ymd').'XXXXXXX';
    }
    /**
     * 获得采购单单号(日期+供货商[5]+流水[2])
     * @param number $supplier 供应商
     * @param &string $line 最大流水
     * @return string
     */
    public function purchase($supplier, &$line) {
        return $this->billBySupplier('buy_come', $supplier, $line);
    }

    /**
     * 获得分货单单号(日期+供货商[5]+流水[2])
     * @param number $supplier 供应商
     * @param &string $line 最大流水
     * @return string
     */
    public function sort($supplier, &$line) {
        return $this->billBySupplier('buy_sort', $supplier, $line);
    }

    /**
     * 获得退货单单号(日期+供货商[5]+流水[2])
     * @param number $supplier 供应商
     * @param &string $line 最大流水
     * @return string
     */
    public function back($supplier, &$line) {
        return $this->billBySupplier('buy_return', $supplier, $line);
    }

    /**
     * 获得结构相同单据单号(日期+供货商[5]+流水[2])
     * @param string $table 单据表
     * @param number $supplier 供应商
     * @param &string $line 最大流水
     * @return string
     */
    public function billBySupplier($table, $supplier, &$line) {
        $date = date('Y-m-d');
        $max = $this->getMax($table, self::BILL_LINE_KEY, "`bill_date`='{$date}' AND `bill_client`='{$supplier}'");

        return date('ymd', strtotime($date, time())).self::addZero($this->getClient($supplier), 3).self::addZero($line = $max, 4);
    }

    /**
     * 调拨单单号(日期[6]+流水[5])
     * @param &string $line 最大流水
     * @return string
     */
    public function allot(&$line) {
        return $this->line('depot_move', $line);
    }


    /**
     * 流水型编号获取
     * @param string $table 数据表
     * @param string $field 流水编号字段
     * @param int $length 有效长度
     * @return string
     */
    public function line($table, $field, $length = 5) {
        $max = $this->getMax($table, $field, "LENGTH(`{$field}`)={$length} AND `{$field}`>0") + 1;
        return self::addZero($max, $length, '1');
    }

    /**
     * 获得日期型单据号
     *
     * @param $table
     * @param $field
     * @param $lineLen
     * @param $prefix
     * @return string
     */
    public function getDateCode($table, $field, $lineLen, $prefix) {
        $date = date('ymd');
        $dLen = strlen($date);
        $len = $dLen + $lineLen;
        if ($prefix) {
            $pLen = strlen($prefix);
            $len += $pLen;
            $where = "LENGTH(`{$field}`)={$len} AND LEFT(`{$field}`, ".($pLen + $dLen).")='".($prefix.$date)."'";
        } else {
            $where = "LENGTH(`{$field}`)={$len} AND LEFT(`{$field}`, {$dLen})='{$date}'";
        }
        $max = $this->getMax($table, "RIGHT(`{$field}`, {$lineLen})", $where);

        return ($prefix?:'').$date.self::addZero($max, $lineLen, '1');
    }

    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    // * * * * * * * * * * * * * * * * * * * 条码部分 * * * * * * * * * * * * * * * * * * * * *
    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    /**
     * 获得成品条码
     * @param int $line 最大流水
     * @param int $client
     * @param int $variety
     * @param bool $fullMatch 联合技术模式，批量模式请手动关闭
     * @return null|string
     */
    public function bar(&$line, $client = 0, $variety = 0, $fullMatch = true) {
        if ($fullMatch) {
            if (false === $this->startBatchMode()) {
                return null;
            }
        }
        $bar = null;
        $bRule = G3_BaseConfigModel::getRow(['key'=>'goodsBarRule'], 'value');
        switch ($bRule['value']) {
            case '8': {
                $line = $bar = $this->bar8();
            } break;
            case '10': {
                $bar = $this->bar10($line);
            } break;
            case '12': {
                $bar = $this->bar12($line, $client);
            } break;
            case '14': {
                $bar = $this->bar14($line, $client, $variety);
            } break;
        }
        $fullMatch && $this->endBatchMode();

        return $bar;
    }

    /**
     * 设置批量模式
     * @return bool
     * @throws Exception
     */
    public function startBatchMode() {
        $rKey = self::$cachePrefix.'bar.request';
        $mInterval = 0;
        $waitPer = 500;

        while ($this->redis->exists($rKey) && intval($this->redis->get($rKey)) == 1) {
            usleep($waitPer);
            $mInterval += $waitPer;
            if ($mInterval == 6*$waitPer) {
                return false;
            }
        }
       $this->redis->incr($rKey);

        return true;
    }

    /**
     * 结束批量模式
     */
    public function endBatchMode() {
        $this->redis->delete(self::$cachePrefix.'bar.request');
        $keys  = $this->redis->keys(self::$cachePrefix.'bar.*');
        $this->redis->delete($keys);
    }

    /**
     * 是否批量获取条码模式
     * @return bool
     */
    private function batchMode() {
        return $this->redis->get(self::$cachePrefix.'bar.request') > 0;
    }

    /**
     * 商品物料条码(流水[6])
     * @param bool $fullMatch 联合技术模式，批量模式请手动关闭
     * @return string|null
     */
    public function bar6($fullMatch = true) {
        if ($fullMatch) {
            if (false === $this->startBatchMode()) {
                return null;
            }
        }
        $bar = self::addZero($this->getJustGoodsLineMax(100000, 999999)+1, 6, '1');
        $fullMatch && $this->endBatchMode();

        return $bar;
    }

    /**
     * 商品条码(流水[8])
     * @return string
     */
    private function bar8() {
        return self::addZero($this->getJustGoodsLineMax(10000000, 99999999)+1, 8, '1');
    }

    /**
     * 商品10位条码(日期[6]+流水[4])
     * @param int $line 最大流水
     * @return string
     */
    private function bar10(&$line) {
        return date('ymd').self::addZero($line = $this->getMaxLineByDate()+1, 4);
    }

    /**
     * 12位商品条码(供应商[3]+年月[4]+流水[4])
     * @param int $line 最大流水
     * @param number $client 供应商
     * @return string
     */
    private function bar12(&$line, $client) {
        return $this->getClient($client).date('ym').self::addZero($line = $this->getMaxLineByDate($client, 0, true)+1);
    }

    /**
     * 14位商品条码(供应商[3]+年月[4]+样式[3]+流水[4])
     * @param int $line 最大流水
     * @param number $client 供应商
     * @param number $variety 样式
     * @return string
     */
    private function bar14(&$line, $client, $variety) {
        return $this->getClient($client).date('ym').$this->getVariety($variety).self::addZero($line = $this->getMaxLineByDate($client, $variety, true)+1, 4);
    }

    /**
     * 获得供应商编号
     * @param $id
     * @return string
     */
    private function getClient($id) {
        if (isset($this->cache['client'][$id])) {
            $code = $this->cache['client'][$id];
        } else {
            $client = G3_BaseClientModel::getRow(['client_id'=>$id], 'client_code');
            if (count($client) && is_numeric($client['client_code'])) {
                $code = $client['client_code'];
            } else {
                $code = '000';
            }
            $this->cache['client'][$id] = $code;
        }

        return $code;
    }

    /**
     * 获得样式编码代号
     * @param $id
     * @return string
     */
    private function getVariety($id) {
        if (isset($this->cache['variety'][$id])) {
            $code = $this->cache['variety'][$id];
        } else {
            $client = G3_BaseClassVarietyModel::getRow(['variety_id'=>$id], 'variety_symbol');
            if (count($client) && is_numeric($client['variety_symbol'])) {
                $code = $client['variety_symbol'];
            } else {
                $code = '000';
            }
            $this->cache['variety'][$id] = $code;
        }

        return $code;
    }

    private function getJustGoodsLineMax($start, $end) {
        $rKey = self::$cachePrefix.'bar.max.'.$start.'-'.$end;
        $rKeyMiddle = self::$cachePrefix.'bar.max.linesMiddle.'.$start.'-'.$end;
        $rKeyOuter = self::$cachePrefix.'bar.max.maxOuter.'.$start.'-'.$end;
        if ($this->batchMode() && $this->redis->exists($rKey)) {
            $max = $this->redis->get($rKey);
            if (!$this->barIsUnique) {
                $this->redis->incr($rKey);
            } else {
                if ($this->redis->lLen($rKeyMiddle) > 0) {
                    $unusedLine = $this->redis->lPop($rKeyMiddle);
                    $this->redis->incrBy($rKey, ($unusedLine - $max - 1));
                    $max = $unusedLine - 1;
                } else {
                    if ($this->redis->exists($rKeyOuter)) {
                        $eMax = $this->redis->get($rKeyOuter);
                        if ($max < $eMax) {
                            $max = $eMax;
                            $this->redis->incr($rKeyOuter);
                        } else {
                            $this->redis->incr($rKey);
                        }
                    } else {
                        $this->redis->incr($rKey);
                    }
                }
            }

            return $max;
        }

        $select  = "COALESCE(MAX(IF(`goods_inner_bar`=1,`goods_max_line`,0)), 0) AS `max`,";
        $select .= "COALESCE(MAX(IF(`goods_inner_bar`=0,`goods_max_line`,0)), 0) AS `maxOuter` ";
        $_where = ['goods_max_line >= ' => $start, 'goods_max_line <=' => $end];
        if (!$this->barIsUnique) {
            $_where['goods_inner_bar'] = 1;
        }
        $maxLine = G3_BaseGoodsModel::getRow($_where, $select);

        // 获得跳跃条码区间的最小值
        if ($maxLine['max'] < $maxLine['maxOuter']) {
            $_db = Database::getInstance();
            $_db->select('`goods_max_line` AS `line`');
            $_db->where(['goods_max_line > ' => $maxLine['max'], 'goods_max_line < ' => $maxLine['maxOuter'], 'goods_inner_bar' => 0]);
            $_db->groupBy('line')->orderBy('line')->limit(10);
            $_db->get('base_goods');
            $res1 = $_db->resultArray();

            $_db->select('`goods_max_line` AS `line`');
            $_db->where(['goods_max_line'=>null, 'goods_bar > '=>$maxLine['max'], 'goods_bar < '=>$maxLine['maxOuter'], 'goods_inner_bar' => 0]);
            $_db->groupBy('line')->orderBy('line')->limit(10);
            $_db->get('base_goods');
            $res2 = $_db->resultArray();

            $maxMiddleLines = array_merge($res1, $res2);

            if (count($maxMiddleLines) > 0) {
                $lines = [];
                foreach ($maxMiddleLines as $lItem) {
                    $lines[] = $lItem['line'];
                }
            }
            $aCount = 0;
            for ($i = ($maxLine['max'] + 1); $i < $maxLine['maxOuter'] && $aCount < 10; $i++) {
                if (isset($lines) && !in_array($i, $lines)) {
                    $this->redis->rPush($rKeyMiddle, $i);
                    $aCount++;
                }
            }
        }

        $max = isset($maxLine['max']) ? $maxLine['max'] : 0;
        $maxNext = $max + 1;
        if ($this->barIsUnique) {
            if ($max < $maxLine['maxOuter']) {
                if ($this->redis->lLen($rKeyMiddle) > 0) {
                    $maxNext = $this->redis->lPop($rKeyMiddle);
                    $max = $maxNext - 1;
                } else {
                    $max = $maxLine['maxOuter'];
                    $maxNext = $max + 1;
                }
            }
        }

        if ($this->batchMode()) {
            $this->redis->set($rKey, $maxNext);
            if ($maxNext < $maxLine['maxOuter']) {
                $this->redis->set($rKeyOuter, ($maxLine['maxOuter']));
            }
        }
        return $max;
    }

    private function getMaxLineByDate($client = 0, $variety = 0, $byMonth = false) {
        $rKey = self::$cachePrefix.'bar.date.max.'.$client.'-'.$variety;
        $rKeyMiddle = self::$cachePrefix.'bar.date.linesMiddle.'.$client.'-'.$variety;
        $rKeyOuter = self::$cachePrefix.'bar.date.maxOuter.'.$client.'-'.$variety;
        if ($this->batchMode() && $this->redis->exists($rKey)) {
            $max = $this->redis->get($rKey);
            if (!$this->barIsUnique) {
                $this->redis->incr($rKey);
            } else {
                if ($this->redis->lLen($rKeyMiddle) > 0) {
                    $unusedLine = $this->redis->lPop($rKeyMiddle);
                    $this->redis->incrBy($rKey, ($unusedLine - $max - 1));
                    $max = $unusedLine - 1;
                } else {
                    if ($this->redis->exists($rKeyOuter)) {
                        $eMax = $this->redis->get($rKeyOuter);
                        if ($max < $eMax) {
                            $max = $eMax;
                            $this->redis->incr($rKeyOuter);
                        } else {
                            $this->redis->incr($rKey);
                        }
                    } else {
                        $this->redis->incr($rKey);
                    }
                }
            }

            return $max;
        }

        $select  = "COALESCE(MAX(IF(`goods_inner_bar`=1,`goods_max_line`,0)), 0) AS `max`,";
        $select .= "COALESCE(MAX(IF(`goods_inner_bar`=0,`goods_max_line`,0)), 0) AS `maxOuter` ";
        $where['goods_max_line > '] = 0;
        $where['goods_max_line < '] = 10000;
        if (!$this->barIsUnique) {
            $where['goods_inner_bar'] = 1;
        }
        if ($client > 0) {
            $where['goods_client'] = $client;
        }
        if ($variety > 0) {
            $where['goods_variety'] = $variety;
        }
        // 是否按月生成流水
        if ($byMonth) {
            $monStart = date('Y-m-01');
            $monEnd = date('Y-m-31');
            $where['goods_date >= '] = $monStart;
            $where['goods_date <= '] = $monEnd;
        } else {
            $date = date('Y-m-d');
            $where['goods_date'] = $date;
        }
        $maxLine = G3_BaseGoodsModel::getRow($where, $select);

        // 获得跳跃条码区间的最小值
        if ($maxLine['max'] < $maxLine['maxOuter']) {
            $_select = "`goods_max_line` AS `line`";
            $_where = [
                'goods_inner_bar'   => 0,
                'goods_max_line > ' => $maxLine['max'],
                'goods_max_line < ' => $maxLine['maxOuter'],
            ];
            if ($client > 0) {
                $_where['goods_client'] = $client;
            }
            if ($variety > 0) {
                $_where['goods_variety'] = $variety;
            }

            // 是否按月生成流水
            if ($byMonth) {
                $monStart = date('Y-m-01');
                $monEnd = date('Y-m-31');
                $_where['goods_date >= '] = $monStart;
                $_where['goods_date <= '] = $monEnd;
            } else {
                $date = date('Y-m-d');
                $_where['goods_date'] = $date;
            }

            $maxMiddleLines = G3_BaseGoodsModel::getList($_where, $_select, '10', 'goods_max_line asc', 'goods_max_line');
            if (count($maxMiddleLines) > 0) {
                $lines = [];
                foreach ($maxMiddleLines as $lItem) {
                    $lines[] = $lItem['line'];
                }
            }
            $aCount = 0;
            for ($i = ($maxLine['max'] + 1); $i < $maxLine['maxOuter'] && $aCount < 10; $i++) {
                if (isset($lines) && !in_array($i, $lines)) {
                    $this->redis->rPush($rKeyMiddle, $i);
                    $aCount++;
                }
            }
        }

        $max = isset($maxLine['max']) ? $maxLine['max'] : 0;
        $maxNext = $max + 1;
        if ($this->barIsUnique) {
            if ($max < $maxLine['maxOuter']) {
                if ($this->redis->lLen($rKeyMiddle) > 0) {
                    $maxNext = $this->redis->lPop($rKeyMiddle);
                    $max = $maxNext - 1;
                } else {
                    $max = $maxLine['maxOuter'];
                    $maxNext = $max + 1;
                }
            }
        }

        if ($this->batchMode()) {
            $this->redis->set($rKey, $maxNext);
            if ($maxNext < $maxLine['maxOuter']) {
                $this->redis->set($rKeyOuter, ($maxLine['maxOuter']));
            }
        }

        return $max;
    }

    /**
     * 首位补全零
     * @param $code
     * @param int $length
     * @param string $prevChar
     * @return string
     */
    public static function addZero($code, $length = 5, $prevChar = '0') {
        for ($i = strlen($code); $i < $length; $i++) {
            $code = ($i == ($length - 1) ? $prevChar : '0').$code;
        }

        return $code;
    }

    /**
     * 外部条码拆分出流水和日期
     *
     * @param string $bar 条码
     * @param int &$line 流水
     * @param string|date &$date 日期
     */
    public function splitBarToLineAndDate($bar, &$line, &$date)
    {
        $len = strlen(''.$bar);
        switch ($len) {
            case 6:
            case 8: {
                if (is_numeric($bar) > 0 && $bar >= 0 && $bar <= ($len == 6 ? 999999 : 99999999)) {
                    $line = $bar;
                }
            } break;
            case 10: {
                $dStr = substr($bar, 0, 6);
                $lStr = intval(substr($bar, 6, 4));
                $dTime = strtotime('20'.$dStr);
                $dPre = date('ymd', $dTime);
                if ($dPre == $dStr && is_numeric($lStr) && $lStr >= 0 && $lStr <= 9999) {
                    $line = $lStr;
                    $date = date('Y-m-d', $dTime);
                }
            } break;
            case 12: {
                $sStr = substr($bar, 0, 3);
                $dStr = substr($bar, 3, 4);
                $lStr = intval(substr($bar, 7, 5));
                $dTime = strtotime('20'.$dStr.'01');
                $dPre = date('ymd', $dTime);
                if ($dPre == $dStr && is_numeric($lStr) && $lStr >= 0 && $lStr <= 99999) {
                    $line = $lStr;
                    $date = date('Y-m-d', $dTime);
                }
            } break;
            case 14: {
                $sStr = substr($bar, 0, 3);
                $dStr = substr($bar, 3, 4);
                $vStr = substr($bar, 7, 3);
                $lStr = intval(substr($bar, 10, 4));
                $dTime = strtotime('20'.$dStr.'01');
                $dPre = date('ymd', $dTime);
                if ($dPre == $dStr && is_numeric($lStr) && $lStr >= 0 && $lStr <= 9999) {
                    $line = $lStr;
                    $date = date('Y-m-d', $dTime);
                }
            } break;
            default: {
                $line = 0;
                $date = date('Y-m-d');
            }
        }
    }
    
    protected function getMax($table_name, $field_id, $where_array)
    {
        $sql = "SELECT max($field_id) as field_id FROM $table_name ";

        if ($where_array) {
            $sql .= "WHERE $where_array ";
        }
        $row = $this->db->query($sql)[0];

        if ($row) {
            $max_id = $row['field_id'] + 1;
        } else {
            $max_id = 1;
        }
        return $max_id;
    }
}
