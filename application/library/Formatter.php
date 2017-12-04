<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

/**
 * 格式化处理器
 */
class Formatter {
    const DECIMAL_PRICE = 2; // 价格的精度
    const DECIMAL_MONEY = 2; //
    const DECIMAL_WEIGHT = 6;
    const DECIMAL_WEIGHT_D3 = 3;
    const DECIMAL_PERCENT = 4;
    const DECIMAL_CENT = 4;
    const DECIMAL_NUMBER = 0;

    const NUMBER_DEVIATION = 0.000009;

    // 精度常量
    static private $precisions = array('-100', '-10', '1', '10', '100', '1000');

    /**
     * 数据格式化
     * @param $value
     * @param $mode
     * @param string $dot
     * @param string $spit
     * @return float|string
     */
    static public function FormatData($value, $mode, $dot = '.', $spit = '') {
        $_value = $value;
        $value = self::getExactNumber($value);
        if ($mode === "string") {
            $value = stripslashes($_value);
        } elseif ($mode === "price") {
            $value = number_format($value, self::DECIMAL_PRICE, '.', '') * 100 / 100;
        } elseif ($mode === "money") {
            $value = number_format($value, self::DECIMAL_MONEY, '.', '') * 100 / 100;
        } elseif ($mode === "number") {
            $value = number_format($value, self::DECIMAL_NUMBER, '.', '') * 100 / 100;
        } elseif ($mode === "per") {
            $value = number_format($value, self::DECIMAL_PERCENT, '.', '');
            $value = $value . "%";
        } elseif ($mode === "cent") {
            $value = number_format($value, self::DECIMAL_CENT, '.', '');
        } elseif ($mode === "weight") {
            $value = number_format($value, self::DECIMAL_WEIGHT, '.', '') * 100 / 100;
            $value = self::numberReplaceZero($value);
        } elseif ($mode === "integer") {
            $value = number_format($value, 0, '.', '') * 100 / 100;
        } elseif ($mode === "date") {
            $value = is_integer($_value) ? date('Y-m-d', $_value) : $_value;
        } elseif ($mode === "datetime") {
            $value = is_integer($_value) ? date('Y-m-d H:i:s', $_value) : $_value;
        } else {
            $_mode = is_numeric($mode) ? intval($mode) : -1;
            if ($_mode >= 0 && $_mode <= 12) {
                $value = number_format($value, $_mode, $dot, $spit);
            } else {
                $value = $_value;
            }
        }

        return $value;
    }

    /**
     * 数字格式化处理(n舍m入)
     *
     * @param float $val 数字
     * @param int $precision 保留小数位数
     * @param int $rand 进位权值
     *
     * @return float
     */
    static public function numberFormat($val, $precision = 0, $rand = 5) {
        if ($precision >= 0 && is_numeric($val) && ($dotPos = strpos($val, '.')) !== false) {
            $len = strlen($val);
            $dotLen = $len - $dotPos - 1;
            if ($dotLen > $precision) {
                $baseVal = substr($val, 0, $precision > 0 ? ($dotPos + 1 + $precision) : $dotPos);
                $randNum = substr($val, $dotPos + 1 + $precision, 1);
                if ($randNum > 0 && $randNum >= $rand) {
                    $val = $baseVal + pow(10, -1*$precision)*($baseVal >= 0 ? 1 : -1);
                } else {
                    $val = $baseVal;
                }
            }
        }

        return floatval($val);
    }

    /**
     * 字符串数字精确转换为浮点数
     * @param $number
     * @return string
     */
    static public function getExactNumber($number) {
        $_flag = '1';
        $_num = sprintf('%.6f', $number);
        if ($_num < 0) $_flag = -1;
        $_num = abs($_num);
        $_exactNumber = sprintf('%.6f', abs(self::NUMBER_DEVIATION));
        if ($_num <= $_exactNumber) {
            $_num = 0;
        }

        return sprintf('%.6f', $_flag * $_num);
    }

    /**
     * 抹去数字末尾的零
     * @param $value
     * @return int|string
     */
    static public function numberReplaceZero($value) {
        if (is_numeric($value)) {
            $p = explode('.', (string)$value);
            if (count($p) == 2) {
                $l = $p[1];
                while (strlen($l) > 0 && substr($l, strlen($l) - 1) == 0) {
                    $l = substr($l, 0, strlen($l) - 1);
                }
                if ($l == 0) {
                    $value = $p[0];
                } else {
                    $value = $p[0] . '.' . $l;
                }
            }
            if ('' == $value) $value = 0;
        }

        return $value;
    }

    /**
     * 重量转换
     * @param number $curWeight 待转换重量
     * @param string $curUnit 待转换重量单位
     * @param string $tarUnit 目标重量单位
     * @returns boolean|number
     */
    static public function weightTransform($curWeight, $curUnit, $tarUnit) {
        $curUnitRadixToG = self::getWeightUnitRadixToG($curUnit);
        $tarUnitRadixToG = self::getWeightUnitRadixToG($tarUnit);

        if (is_numeric($curWeight) && $curUnitRadixToG !== false && $tarUnitRadixToG != false) {
            return self::FormatData($curWeight * $curUnitRadixToG / $tarUnitRadixToG, self::DECIMAL_WEIGHT);
        }

        return false;
    }

    /**
     * 获得指定单位转换成克的转换率
     * @param string $needToTsfUnit 单位
     * @returns boolean|number
     */
    static public function getWeightUnitRadixToG($needToTsfUnit) {
        $radix = false;
        switch ($needToTsfUnit) {
            case 'g':
                $radix = 1;
                break;
            case 'ct':
                $radix = 0.2;
                break;
            case 'mi':
                $radix = 0.2 / 100;
                break;
        }

        return $radix;
    }

    /**
     *
     * @param $unit
     * @return string
     */
    static public function getWeightUnitName($unit) {
        $name = '';
        switch ($unit) {
            case 'g':
                $name = '克';
                break;
            case 'ct':
                $name = '卡拉';
                break;
            case 'mi':
                $name = '分';
                break;
        }

        return $name;
    }

    /**
     * 验证单位,若单位不存在默认为g
     * @param $unit
     * @return string
     */
    static public function checkWeightUnit($unit) {
        switch ($unit) {
            case 'g':
            case 'ct':
            case 'mi':
                break;
            default:
                $unit = 'g';
        }

        return $unit;
    }

    /**
     * 截取以数字开头的字符串中的数字
     * @param $string
     * @return float
     */
    static public function checkNumber($string) {
        if (!is_numeric($string)) {
            $numStr = (floatval($string));

            return $numStr;
        }

        return $string;
    }

    /**
     * 将指定位数后的值抹去,不执行X舍N入
     * @param $number
     * @param int $precision 精度位数
     * @param int $precisionType 精度取舍类型
     * @return float
     */
    static public function maling($number, $precision = -100, $precisionType = 10) {
        return self::numberRoundCus($number, $precision, $precisionType);
    }

    /**
     * 金额执行N舍M入计算-用于解决在非小数为上抹零操作
     *
     * @param $money
     * @param int $precision 精度位数
     * @param int $precisionType 精度取舍类型
     * @return float|string
     */
    static public function numberRoundCus($money, $precision = -100, $precisionType = 5) {
        if (in_array($precision, self::$precisions)) {
            $money = $precision > 0 ? self::numberFormat($money/$precision, self::DECIMAL_NUMBER, $precisionType)*$precision :
                self::numberFormat($money*abs($precision), self::DECIMAL_NUMBER, $precisionType)/abs($precision);
        }

        return $money;
    }

    /**
     * 拆分区间型字符串到区间对象中
     *
     * @param $zString
     * @return \stdClass
     */
    static public function parseZoneString($zString) {
        /**
         * @var $zone
         * @property $start
         * @property $end
         */
        $zone = new \stdClass();
        $zStr = explode(',', $zString);
        $zone->start = $zStr[0]*1;
        $zone->end = isset($zStr[1]) ? $zStr[1]*1 : $zone->start;

        return $zone;
    }

    /**
     * 日期字符拆分到日期对象
     *
     * @param $date
     * @param $splitStr
     * @return \stdClass
     */
    static public function dateToYearMonthDay($date, $splitStr = '-') {
        /**
         * @var $zone
         * @property $start
         * @property $end
         */
        $dObj = new stdClass();
        $dObj->year = 0;
        $dObj->month = 0;
        $dObj->day = 0;
        if ($date) {
            $dArr = explode($splitStr, $date);
            if (count($dArr) == 1) {
                $dObj->day = array_pop($dArr);
            } else if (count($dArr) == 2) {
                $dObj->day = array_pop($dArr);
                $dObj->month = array_pop($dArr);
            } else {
                $dObj->day = array_pop($dArr);
                $dObj->month = array_pop($dArr);
                $dObj->year = array_pop($dArr);
            }
        }

        return $dObj;
    }

    /**
     * 格式化excel中的时间
     *
     * @param $date
     * @return bool|string
     */
    static public function formatExcelDate($date) {
        return date('Y-m-d', -2209017600 + ($date - 2) * 86400);
    }

    /**
     * 去指定长度的unicode字符
     *
     * @param $utf8_str
     * @param $retLen
     * @return string
     */
    static public function utf8StrToUnicodeStr($utf8_str, $retLen) {
        $unicodeArr = self::utf8_str_to_unicode($utf8_str);
        $newUniArr = [];
        $curLen = 0;
        foreach ($unicodeArr as $uni) {
            if ((strlen($uni) + $curLen) <= $retLen) {
                $newUniArr[] = $uni;
            } else {
                break;
            }
        }

        return self::unicode_to_utf8($newUniArr);
    }

    /**
     * utf8字符转换成Unicode字符
     * @param  string $utf8_str Utf-8字符
     * @return string Unicode字符
     */
    static public function utf8_str_to_unicode($utf8_str) {
        $str = iconv('UTF-8', 'UCS-2', $utf8_str);
        $arrstr = str_split($str, 2);
        $decArr = [];
        for($i = 0, $len = count($arrstr); $i < $len; $i++) {
            $dec = hexdec(bin2hex($arrstr[$i]));
            $decArr[] = $dec;
        }
        return $decArr;
    }

    /**
     * Unicode字符转换成utf8字符
     * @param  string $unicode_str_arr Unicode字符数组
     * @return string Utf-8字符
     */
    static public function unicode_to_utf8($unicode_str_arr) {
        $unistr = '';
        for($i = 0, $len = count($unicode_str_arr); $i < $len; $i++) {
            $temp = intval($unicode_str_arr[$i]);
            $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
        }
        return iconv('UCS-2', 'UTF-8', $unistr);
    }

    /**
     * 将EXCEL5位数字型时间格式转换为自然格式
     * 
     * @param $days
     * @param bool $time
     * @param string $str
     * 
     * @return bool|string
     */
    static public function excelDTToNormal($days, $time = false, $str = '-') {
        if (!$days) return false;
        if (function_exists("gregoriantojd")) {
            if (is_numeric($days)) {
                //based on 1900-1-1
                $jd = gregoriantojd(1, 1, 1970);
                $gregorian = jdtogregorian($jd + intval($days) - 25569);
                $myDate = explode('/', $gregorian);
                $myDateStr = str_pad($myDate[2], 4, '0', STR_PAD_LEFT)
                    . $str . str_pad($myDate[0], 2, '0', STR_PAD_LEFT)
                    . $str . str_pad($myDate[1], 2, '0', STR_PAD_LEFT)
                    . ($time ? " 00:00:00" : '');
                return $myDateStr;
            }
        } else {
            $date = $days > 25568 ? $days + 1 : 25569;
            $ofs = (70 * 365 + 17 + 2) * 86400;
            $days = date("Y" . $str . "m" . $str . "d", ($date * 86400) - $ofs) . ($time ? " 00:00:00" : "");
        }

        return $days;
    }

}