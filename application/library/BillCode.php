<?php

/**
 * 商品条码
 * Class billCode
 */
class billCode
{
    public function __construct()
    {
        $this->table_name = "soft_code";
        $this->table_element = "soft_element";
        $this->lineType = '';
        $this->db = Database::getInstance();
    }

    function makeString($str = '', $pere = 'N')
    {
        if (!$str) return '';
        if ($pere == 'N') {
            //转化为实数
            $newstr = (float)$str;
            return $newstr;
        } else {
            return $str;
        }
    }

    function getGoodsCode($type = 'goods_code', $code_letter = '', $classe_id = 0, $branch_id = 0)
    {
        $goods_code = $this->GetBillCode('base_goods', $type, "", 'goods_id', $type, "", $code_letter, 0, 0, 0, $classe_id, $branch_id, 0, 0, 0, 0, 0, 0);

        return $goods_code;
    }

    /**
     * 生成指定表单的编码
     * @param $table_name 表名
     * @param $table_type 类型[eg:S,T]
     * @param $field_type
     * @param $field_id
     * @param $field_code
     * @param string $field_where
     * @param string $code_letter 拼音码
     * @param int $depot_id 入仓库id
     * @param int $client_id 往来单位id
     * @param int $emplee_id 员工id
     * @param int $classe_id 类别id
     * @param int $branch_id
     * @param int $pos_id pos机号
     * @param int $shop_id 门店id
     * @param int $user_id 用户id
     * @param int $job_id 角色id
     * @param int $subject_id 科目
     * @param int $depot_id_out 出仓库
     * @return int|string
     */
    function GetBillCode($table_name, $table_type, $field_type, $field_id, $field_code, $field_where = '', $code_letter = '',
                         $depot_id = 0, $client_id = 0, $emplee_id = 0, $classe_id = 0, $branch_id = 0, $pos_id = 0, $shop_id = 0, $user_id = 0,
                         $job_id = 0, $subject_id = 0, $depot_id_out = 0)
    {
        $soft->GetView("soft_code", "", "code_name='$table_name' and code_type='$table_type'");

        //组合新的字符串
        //设置默认标志
        $soft->code_len = $soft->code_len ? $soft->code_len : 0; //如果没有设置长度，默认为0
        //流水长度不能超过10;除了商品条形码
        if ($soft->code_len > 10) {
            $soft->code_len = 10;
        }
        //取一个规则
        $code_sign_extends = '';
        //优先取用户选择的规则；如果用户没有选择，则直接使用初始化设置；如果没有初始化值，则只保留前缀和流水号
        if (trim($soft->code_sign_extends)) {
            $code_sign_extends = trim($soft->code_sign_extends);
        } elseif (trim($soft->code_sign_default)) {
            $code_sign_extends = trim($soft->code_sign_default);
        }
        //取组合前缀（除了流水号外）
        if ($code_sign_extends) {
            $fullDate = $this->GetLetter($soft->code_join, $code_letter, $depot_id, $client_id, $emplee_id, $classe_id,
                $branch_id, $pos_id, $shop_id, $user_id, $job_id, $subject_id, $depot_id_out);
        } else {
            //code_format 编码格式
            if ($soft->code_format == "") {
                $fieldYear = "";
                $fieldMonth = "";
                $fieldDay = "";
            }
            if ($soft->code_format == "Y") {
                $fieldYear = date("Y");
                $fieldMonth = "";
                $fieldDay = "";
            }
            if ($soft->code_format == "M") {
                $fieldYear = date("Y");
                $fieldMonth = $soft->code_join . date("m");
                $fieldDay = "";
            }
            if ($soft->code_format == "D") {
                $fieldYear = date("Y");
                $fieldMonth = $soft->code_join . date("m");
                $fieldDay = $soft->code_join . date("d");
            }
            $fullDate = $fieldYear . $fieldMonth . $fieldDay;

        }
        $fullDateLen = strlen($fullDate);

        $fieldSignLen = strlen($soft->code_sign); //编码前缀长度
        $fieldJoinLen = strlen($soft->code_join); //编码段连接符号长度
        $fieldSublen = $fieldSignLen + $fieldJoinLen;
        $fullDate = trim($fullDate);
        if ($soft->code_sign && trim($fullDate)) {
            $fullData = $soft->code_sign . $soft->code_join . $fullDate;
            $fieldSumLen = intval($fieldSublen + $fullDateLen);
        } elseif ($soft->code_sign) {
            $fullData = $soft->code_sign;
            $fieldSumLen = intval($fieldSignLen);
        } elseif (trim($fullDate)) {
            $fullData = $fullDate;
            $fieldSumLen = intval($fullDateLen);
        }
        $fieldSumLen = $fieldSumLen ? $fieldSumLen : 0;
        //得到流水号
        $codeLine = $this->GetLine($fullData, $fieldSumLen, $soft->code_len, $table_name, $table_type, $field_type, $field_id, $field_code, $field_where);
        if ($fullData && $codeLine) {
            $soft_code = $fullData . $soft->code_join . $codeLine;
        } elseif ($fullData) {
            $soft_code = $fullData;
        } elseif ($codeLine) {
            $soft_code = $codeLine;
        } else {
            $soft_code = '';
        }
        return $soft_code;
    }

    /**
     * 获得编码组合元素部分及流水
     * @param string $code_join
     * @param string $code_letter
     * @param int $depot_id
     * @param int $client_id
     * @param int $emplee_id
     * @param int $classe_id
     * @param int $branch_id
     * @param int $pos_id
     * @param int $shop_id
     * @param int $user_id
     * @param int $job_id
     * @param int $subject_id
     * @param $depot_id_out
     * @return bool|int|string
     */
    function GetLetter($code_join = '', $code_letter = '', $depot_id = 0, $client_id = 0, $emplee_id = 0, $classe_id = 0, $branch_id = 0, $pos_id = 0, $shop_id = 0, $user_id = 0, $job_id = 0, $subject_id = 0, $depot_id_out)
    {
        global $soft;
        //根据不同的字段串查询不同的表中的对应字段的值
        $pre0 = "";
        $sql = "select * from " . $this->table_element . " where element_id in($soft->code_sign_extends)";
        $rowse = $soft->fetchAll($sql);
        //排序、$code_sign_extends
        $tmp = array();
        $includes = explode(",", $soft->code_sign_extends);
        foreach ($includes as $includ) {
            foreach ($rowse as $rows) {
                if ($rows['element_id'] == $includ) {
                    $tmp[] = $rows;
                }
            }
        }
        foreach ($tmp as $rows) {
            $pre = "";
            if ($rows['element_type'] == 'D') //取日期格式
            {
                if ($rows['element_code']) {
                    $element_code = $rows['element_code'];
                    $pre = date("$element_code");
                }
            } elseif ($rows['element_type'] == 'P') //取本对象的拼音码
            {
                $pre = $code_letter;
            } elseif ($rows['element_code'] == 'shop_code') {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $shop_id);
            } elseif ($rows['element_code'] == 'job_code') {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $job_id);
            } elseif ($rows['element_code'] == 'client_code') {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $client_id);
            } elseif ($rows['element_code'] == 'pos_code') {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $pos_id);
            } elseif ($rows['element_code'] == 'subject_code') {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $subject_id);
            } elseif ($rows['element_code'] == 'emplee_code') {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $emplee_id);
            } elseif ($rows['element_code'] == 'classe_code') {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $classe_id);
            } elseif ($rows['element_code'] == 'classe_letter') {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $classe_id);
                if ($pre == '' || !$pre) {
                    $pre = $this->GetString('classe_name', $rows['element_table'], $rows['element_field'], $classe_id);
                    $pre = $soft->getLetterFirst($pre);
                }
            } elseif ($rows['element_code'] == 'depot_code_in') //入仓库
            {
                $pre = $this->GetString('depot_code', $rows['element_table'], $rows['element_field'], $depot_id);
            } elseif ($rows['element_code'] == 'depot_code_out') //出仓库
            {
                $pre = $this->GetString('depot_code', $rows['element_table'], $rows['element_field'], $depot_id_out);
            } elseif ($rows['element_code'] == 'branch_code') {
                $pre = $this->GetString($rows['element_code'], $rows['element_table'], $rows['element_field'], $branch_id);
            } elseif ($rows['element_code'] == 'user_id') {
                $pre = $user_id;
            }
            if ($pre0 && $pre) //加入间隔
            {
                $pre0 .= $code_join . $pre;
            } elseif (!$pre0 && $pre) {
                $pre0 = $pre;
            }
        }
        return $pre0;
    }

    /**
     * 获得指定元素的编码数据
     * @param $element_code
     * @param $element_table
     * @param $element_field
     * @param $element_id
     * @return string
     */
    function GetString($element_code, $element_table, $element_field, $element_id)
    {
        global $soft;
        $pre = '';
        if ($element_id) {
            $sql = "select $element_code as f from " . $element_table . " where $element_field='$element_id'";
            $rows = $soft->fetchOne($sql);
            $pre = $rows['f'];
        }
        return $pre;
    }

    /**
     * 获得编码的流水部分
     * @param $fullData
     * @param int $fieldSumLen
     * @param int $code_len
     * @param $table_name
     * @param $table_type
     * @param $field_type
     * @param $field_id
     * @param $field_code
     * @param $field_where
     * @return int|string
     */
    function GetLine($fullData, $fieldSumLen = 5, $code_len = 5, $table_name, $table_type, $field_type, $field_id, $field_code, $field_where)
    {
        global $db, $soft;

        //取得指定表的编码字段的流水号最大值
        if (!$code_len) //如果长度为0，则返回为空
        {
            return '';
        }

        $sumnum = $fieldSumLen + $code_len;
        if ($fieldSumLen > 0) {
            $joinLen = strlen($soft->code_join);
            $sumnum += $joinLen;
        }

        $getting = true;
        $codeLen = $code_len;
        $sumLen = $sumnum;
        while ($getting) {
            $sql = "select max(right({$field_code}, {$codeLen})+0) as str1 from {$table_name} where $field_id>0 and length($field_code)={$sumLen} ";
            if (trim($field_where) && trim($table_type) && trim($table_type)) {
                $sql .= " and $field_type='$table_type' ";
            }
            if (trim($field_where)) {
                $sql .= "and $field_where ";
            }
            if ($fieldSumLen && $fullData) {
                $sql .= "and left($field_code,$fieldSumLen) = '$fullData'";
            }

            $rst = $db->query($sql);
            $row = $db->fetch_array($rst);
            $maxVal = pow(10, $codeLen) - 1;
            if ($getting = ($row['str1'] == $maxVal)) {
                $codeLen += 1;
                $sumLen += 1;
            }
        }

        $getting = true;
        $codeLenTemp = $code_len;
        $sumLenTemp = $sumnum;
        while ($getting) {
            // 获得临时编码表中的最大
            $sql = "select max(right(code_value, {$codeLenTemp})+0) as str1 from soft_code_temp where code_id={$soft->code_id} and length(code_value)={$sumLenTemp} ";
            if ($fieldSumLen && $fullData) {
                $sql .= "and left(code_value,$fieldSumLen) = '$fullData'";
            }
            $cTemp = $soft->fetchOne($sql, false, '', true);
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

        $code_len = max($codeLen, $codeLenTemp);
        $sumnum = max($sumLen, $sumLenTemp);
        if ($row['str1'] > 0) {
            //流水码生成
            $str1 = $row['str1'];
            $int1 = (int)($str1);
            $int1 = $int1 + 1;

            $len = $code_len - strlen($int1);
            //流水码补0
            for ($i = 0; $i < $len; $i++) {
                $int1 = "0" . $int1;
            }
            $soft_code = $int1;
        } else {
            //没有取到的情况下生成
            $len = $code_len;
            $int1 = '';
            //流水码补0
            for ($i = 0; $i < $len - 1; $i++) {
                $int1 = "0" . $int1;
            }
            $soft_code = $int1 . "1";
        }

        //判断流水号的第一位数是否为0，如果是0；则补一个符号位即00001，就加10000
        if (strlen($soft_code) != strlen((int)$soft_code)) {
            $lennum = strlen($soft_code);
            $adds = '';
            for ($i = 0; $i < $lennum - 1; $i++) {
                $adds = "0" . $adds;
            }
            if ($fieldSumLen == $lennum || !$fieldSumLen) //没有前缀，则在前面加上符号位
            {
                $adds_num = (int)('1' . $adds);
                $soft_code0 = $adds_num + $soft_code;
            } else {
                $soft_code0 = $soft_code;
            }
            //如果超出正常数字，则转为为字符
            if (2147483648 == $soft_code0) {
                $soft_code = 'a' . $soft_code;
            } else {
                $soft_code = $soft_code0;
            }
        }

        return $soft_code;
    }

    /*
	*检测包含中文
	*/
    function isChinese($getStr)
    {
        return (preg_match("/[\x80-\xff]./", $getStr)) ? 1 : 0;
    }
}
