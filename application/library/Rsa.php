<?php

class Rsa {
    private static $PRIVATE_KEY = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQC6enHO+8Uo0NjXYKnByZnAa3MbUhhzp3UztZ9aAR7aOBrJrbNt
fsDaNq7Hp/4/bQ9XoOcLlAD4oUe7/+4uLK906BHso1Dc9AKCLtqSJ+34uyCggX2j
aUG8hnBvHnEl2d7t3I/4FgT4WaHGp4ru37/YakD8QvCRu+gBCJ8ovlU1YQIDAQAB
AoGBAKrHwLsDMUl0G2MEj/N+ImVrAnwe723cCyZUS6AuLodDoqTZg93fm9c9BUys
udh0lPx3y6F65njNm9i9RvDa08P25ZpLNfPv+gk6xBRiVBL5zVTmY4QWRmwXykNy
5owsR3rDBleQ66ZLz1GthgN1K6MUWXm9p63+vykxpqeT+rTRAkEA8tUS8Ga1iKpP
aZN7wKtywYeFSZcQktAjCLZPys8ZCpFvBXYP+Sgc4xIENYrATYwmfFB9JT4AIGdp
fVBhHtwYDwJBAMSXFLE5fb1ZJgoqEgaFq3eO11V7jnq/Ub2CC9IuzpxGWdvBDyJk
0c8Vh0HWwPDTc/bKrjVAsC/6pX8geJXS648CQQCHQdfCv/LtpK+HBvcvYlARLAM5
8kLxA63/9EyNkr1H/anxSMms5oLwl+BwUlC64Q2uwMZ0MAyx/+fqPRNbtQxRAkBb
p8Cq/A3mqNi2ZnTu+4U9AajdnvSHwAlBHI+cV9xWOeqNLU58D5tOPFBKXvCnlz56
snZUN6utQuPECISP/b17AkAIOAChuQPX0Ot/YwPPQn29qaUWTC9RBCn7JZccUztV
qMwGW5JhlQ36CmTe18MGhHj3C+MaJriCK04z9ybGewga
-----END RSA PRIVATE KEY-----
';
    private static $PUBLIC_KEY = '-----BEGIN RSA PRIVATE KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC6enHO+8Uo0NjXYKnByZnAa3Mb
Uhhzp3UztZ9aAR7aOBrJrbNtfsDaNq7Hp/4/bQ9XoOcLlAD4oUe7/+4uLK906BHs
o1Dc9AKCLtqSJ+34uyCggX2jaUG8hnBvHnEl2d7t3I/4FgT4WaHGp4ru37/YakD8
QvCRu+gBCJ8ovlU1YQIDAQAB
-----END RSA PRIVATE KEY-----
';

    /**
     * 获取私钥
     * @return bool|resource
     */
    private static function getPrivateKey(){
        return openssl_pkey_get_private(self::$PRIVATE_KEY);
    }

    /**
     * 获取公钥
     * @return bool|resource
     */
    public static function getPublicKey(){
        return openssl_pkey_get_public(self::$PUBLIC_KEY);
    }

    /**
     * 私钥加密
     * @param string $data
     * @return null|string
     */
    public static function privEncrypt($data = ''){
        if(is_array($data) || is_object($data)){
            $data = json_encode($data);
        }
        
        if (!is_string($data)) {
            return null;
        }
        $crypto = '';
        foreach (str_split($data, 117) as $chunk) {
            $encryptData = '';
            if(openssl_private_encrypt($chunk, $encryptData, self::getPrivateKey())){
                $crypto .= $encryptData;
            }else{
                die('加密失败');
            }
        }
        return base64_encode($crypto);
    }

    /**
     * 公钥加密
     * @param string $data
     * @return null|string
     */
    public static function publicEncrypt($data = ''){
        if(is_array($data) || is_object($data)){
            $data = json_encode($data);
        }
        
        if (!is_string($data)) {
            return null;
        }
        $crypto = '';
        foreach (str_split($data, 117) as $chunk) {
            $encryptData = '';
            if(openssl_public_encrypt($chunk, $encryptData, self::getPublicKey())){
                $crypto .= $encryptData;
            }else{
                die('加密失败');
            }
        }
        return base64_encode($crypto);
    }

    /**
     * 私钥解密
     * @param string $encrypted
     * @return null
     */
    public static function privDecrypt($encrypted = ''){

        if (!is_string($encrypted)) {
            return null;
        }
        
        $crypto = '';
        $encrypted = base64_decode($encrypted);
        foreach (str_split($encrypted, 128) as $chunk) {
            $decryptData = '';
            if(openssl_private_decrypt($chunk, $decryptData, self::getPrivateKey())){
                $crypto .= $decryptData;
            }else{
                die('解密失败');
            }

        }

        return json_decode($crypto,true);
    }

    /**
     * 公钥解密
     * @param string $encrypted
     * @return null
     */
    public static function publicDecrypt($encrypted = ''){
        if (!is_string($encrypted)) {
            return null;
        }
        
        $crypto = '';
        foreach (str_split(base64_decode($encrypted), 128) as $chunk) {
            if(openssl_public_decrypt($chunk, $decryptData, self::getPublicKey())){
                $crypto .= $decryptData;
            }else{
                die('解密失败');
            }

        }
        return $crypto;
    }
}