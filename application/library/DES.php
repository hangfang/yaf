<?php
/**
 * 利用mcrypt做AES加密解密
 * @link http://bask.iteye.com/blog/558900 兼容JAVA加密参考
 */
class DES {
    /**
     * 加密方式
     */
    const CIPHER = MCRYPT_DES;

    /**
     * 模式
     */
    const MODE = MCRYPT_MODE_ECB;

    const KEY = '!dk*,du*-+%mi--_65d8`|';

    /**
     * 加密密钥采用固定KEY+动态令牌
     * @return string
     */
    static private function getKey() {
        return self::KEY . (REST_TOKEN ? REST_TOKEN : '');
    }

    /**
     * 加密
     * @param string $key 密钥
     * @param string $str 需加密的字符串
     * @return string 密文
     */
    static public function encode($key, $str) {
        if(is_object($str) || is_array($str)){
            $str = json_encode($str);
        }
        $size = @mcrypt_get_block_size('des', 'ecb');
        $input = self::pkcs5_pad($str, $size);
        $td = @mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $data;
    }

    /**
     * 解密
     * @param string $key 密钥
     * @param string $str 需解密的字符串
     * @return string 明文
     */
    static public function decode($key, $str) {
        $td = @mcrypt_module_open('des', '', 'ecb', '');
        //使用MCRYPT_DES算法,cbc模式
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = @mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        //初始处理
        $decrypted = @mdecrypt_generic($td, $str);
        //解密
        @mcrypt_generic_deinit($td);
        //结束
        @mcrypt_module_close($td);

        return self::pkcs5_unpad($decrypted);
    }

    static public function pkcs5_pad($text, $blockSize) {
        $pad = $blockSize - (strlen($text) % $blockSize);

        return $text . str_repeat(chr($pad), $pad);
    }

    static public function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;

        return substr($text, 0, -1 * $pad);
    }

    /**
     * 加密并转换结果为Base64 Code
     * @param string $str 需加密的字符串
     * @return string string
     */
    static public function encodeWithBase64($str) {
        return base64_encode(self::encode(self::getKey(), $str));
    }

    /**
     * 解密并转换结果为Base64 Code
     * @param string $str 需解密的字符串
     * @return string string 明文
     */
    static public function decodeWithBase64($str) {
        return rtrim(self::decode(self::getKey(), base64_decode($str, true)));
    }

    /**
     * 加密并转换结果为Base64 Code
     * @param array $dat 需加密的字符串
     * @return string 密文
     */
    static public function arrayToJsonEncodeWithBase64($dat) {
        return self::encodeWithBase64(json_encode($dat));
    }

    /**
     * 解密并转换结果为Base64 Code
     * @param string $dat 需解密的字符串
     * @return array 明文
     */
    static public function decodeWithBase64ToArray($dat) {
        return json_decode(rtrim(self::decodeWithBase64($dat)), true);
    }

    /**
     * 加密数据
     * @param $dat
     * @return string
     */
    static public function result($dat) {
        global $as;

        return self::arrayToJsonEncodeWithBase64(array('token' => '', 'data' => $dat));
    }
}