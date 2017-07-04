<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Captcha_Elephant {
    private $word = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';//随机因子
    private $code;//验证码
    private $codelen = 4;//验证码长度
    private $width = 130;//宽度
    private $height = 50;//高度
    private $img;//图形资源句柄
    private $font;//指定的字体
    private $fontsize = 10;//指定字体大小
    private $fontcolor;//指定字体颜色
    //构造方法初始化
    public function __construct($conf=array()) {
        isset($conf['word']) && $this->word = $conf['word'];
        isset($conf['len']) && $this->codelen = $conf['len'];
        isset($conf['width']) && $this->width = $conf['width'];
        isset($conf['height']) && $this->height = $conf['height'];
        isset($conf['fontsize']) && $this->fontsize = $conf['fontsize'];
        isset($conf['fontcolor']) && $this->fontcolor = $conf['fontcolor'];
        $this->font = APPLICATION_PATH .'/application/library/Captcha/Elephant.ttf';//注意字体路径要写对，否则显示不了图片
    }
    //生成随机码
    private function createCode() {
        $_len = strlen($this->word)-1;
        for ($i=0;$i<$this->codelen;$i++) {
            $this->code .= $this->word[mt_rand(0,$_len-1)];
        }
    }
    //生成背景
    private function createBg() {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
        imagefilledrectangle($this->img,0,$this->height,$this->width,0,$color);
    }
    //生成文字
    private function createFont() {
        $_x = $this->width / $this->codelen;
        for ($i=0;$i<$this->codelen;$i++) {
            $this->fontcolor = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
            imagettftext($this->img,$this->fontsize,mt_rand(-30,30),$_x*$i+mt_rand(1,5),$this->height / 1.4,$this->fontcolor,$this->font,$this->code{$i});
        }
    }
    //生成线条、雪花
    private function createLine() {
        //线条
        for ($i=0;$i<6;$i++) {
            $color = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
            imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$color);
        }
        //雪花
        for ($i=0;$i<100;$i++) {
            $color = imagecolorallocate($this->img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
            imagestring($this->img,mt_rand(1,5),mt_rand(0,$this->width),mt_rand(0,$this->height),'*',$color);
        }
    }
    //输出
    private function outPut() {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }
    //对外生成
    public function doImg($output=false) {
        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
        if($output){
            $this->outPut();
        }else{
            ob_start();
            imagepng($this->img);
            imagedestroy($this->img);
            $tmp = ob_get_contents();
            ob_end_clean();
            $img = 'data:image/png;base64,'.base64_encode($tmp);
            
            return $img;
        }
    }
    //获取验证码
    public function getCode() {
        return strtolower($this->code);
    }
}