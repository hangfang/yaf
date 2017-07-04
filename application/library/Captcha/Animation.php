<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Captcha_Animation{
    private $word = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
    private $width = 75;
    private $height = 25;
    private $codelen = 4;
    private $img = null;
    private $fontsize = 6;
    private $code = '';
    private $str = array();
    
    public function __construct($conf=array()){
        isset($conf['word']) && $this->word = $conf['word'];
        isset($conf['len']) && $this->codelen = $conf['len'];
        
        $num1 = rand(1,99);
        $num2 = rand($num1,99);
        $code = array_rand(array(0,1));
        switch ($code) {
            case 0:
                $this->str[] = $num2;
                $this->str[] = ' +';
                $this->str[] = ' '.$num1;
                $this->str[] = ' =';
                $this->str[] = ' ?';
                $this->code = $num1 + $num2;
                break;
            case 1:
                $this->str[] = $num2;
                $this->str[] = ' -';
                $this->str[] = ' '. $num1;
                $this->str[] = ' =';
                $this->str[] = ' ?';
                $this->code = $num2 - $num1;
                break;
        }

        $this->codelen = count($this->str);
        
        isset($conf['width']) && $this->width = $conf['width'];
        isset($conf['height']) && $this->height = $conf['height'];
        isset($conf['fontsize']) && $this->fontsize = $conf['fontsize'];
    }
    /** 
    *ImageCode 生成包含验证码的GIF图片的函数 
    *@param $string 字符串 
    *@param $width 宽度 
    *@param $height 高度 
    **/ 
    function doImg($output=false){
        // 生成一个32帧的GIF动画 
        for($i=0;$i<32;$i++){ 
            ob_start(); 
            $this->img = imagecreate($this->width,$this->height); 
            imagecolorallocate($this->img, 0,0,0); 
            // 设定文字颜色数组  
            $colorList[]=ImageColorAllocate($this->img,15,73,210); 
            $colorList[]=ImageColorAllocate($this->img,0,64,0); 
            $colorList[]=ImageColorAllocate($this->img,0,0,64); 
            $colorList[]=ImageColorAllocate($this->img,0,128,128); 
            $colorList[]=ImageColorAllocate($this->img,27,52,47); 
            $colorList[]=ImageColorAllocate($this->img,51,0,102); 
            $colorList[]=ImageColorAllocate($this->img,0,0,145); 
            $colorList[]=ImageColorAllocate($this->img,0,0,113); 
            $colorList[]=ImageColorAllocate($this->img,0,51,51); 
            $colorList[]=ImageColorAllocate($this->img,158,180,35); 
            $colorList[]=ImageColorAllocate($this->img,59,59,59); 
            $colorList[]=ImageColorAllocate($this->img,0,0,0); 
            $colorList[]=ImageColorAllocate($this->img,1,128,180); 
            $colorList[]=ImageColorAllocate($this->img,0,153,51); 
            $colorList[]=ImageColorAllocate($this->img,60,131,1); 
            $colorList[]=ImageColorAllocate($this->img,0,0,0); 
            $fontcolor=ImageColorAllocate($this->img,0,0,0); 
            $gray=ImageColorAllocate($this->img,245,245,245); 
            $color=imagecolorallocate($this->img,255,255,255); 
            $color2=imagecolorallocate($this->img,255,0,0); 
            imagefill($this->img,0,0,$gray); 
            $space=15;// 字符间距 
            if($i>0){// 屏蔽第一帧 
                $top=0; 
                for($k=0;$k<count($this->str);$k++){  
                    $colorRandom=mt_rand(0,sizeof($colorList)-1); 
                    $float_top=rand(0,4); 
                    $float_left=rand(0,3);
                    imagestring($this->img, $this->fontsize,$space*($k+0.5),$top+$float_top+($k*2),$this->str[$k], is_numeric($this->str[$k]) ? $colorList[$colorRandom] : $color2); 
                } 
           } 
            for($k=0;$k<20;$k++){  
                $colorRandom=mt_rand(0,sizeof($colorList)-1); 
                imagesetpixel($this->img,rand()%70,rand()%15,$colorList[$colorRandom]); 

            } 
            // 添加干扰线 
            for($k=0;$k<3;$k++){ 
                $colorRandom=mt_rand(0,sizeof($colorList)-1); 
                $todrawline=1; 
                if($todrawline){ 
                    imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$colorList[$colorRandom]); 
                }else{ 
                    $w=mt_rand(0,$this->width); 
                    $h=mt_rand(0,$this->width); 
                    imagearc($this->img,$this->width-floor($w / 2),floor($h / 2),$w,$h, rand(90,180),rand(180,270),$colorList[$colorRandom]); 
                } 
            } 
            imagegif($this->img); 
            imagedestroy($this->img); 
            $this->imgdata[]=ob_get_contents(); 
            ob_end_clean(); 
            ++$i; 
        }
        
        if($output){
            Header('Content-type:image/gif');
            $gif=new GIFEncoder($this->imgdata);
            echo $gif->GetAnimation();
        }else{
            $gif=new GIFEncoder($this->imgdata);
            $img = 'data:image/gif;base64,'.base64_encode($gif->GetAnimation());
            return $img;
        }
    }
    
    public function getCode(){
        return $this->code;
    }
}