<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

class Captcha_Twist{
    private $word = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
    private $width = 160;
    private $height = 40;
    private $codelen = 4;
    private $img = null;
    private $fontsize = 6;
    private $code = '';
    
    public function __construct($conf=array()){
        isset($conf['word']) && $this->word = $conf['word'];
        isset($conf['len']) && $this->codelen = $conf['len'];
        
        for($i=0;$i<$this->codelen;$i++){
            $this->code .= $this->word[rand(0, strlen($this->word)-1)];
        }
        
        isset($conf['width']) && $this->width = $conf['width'];
        isset($conf['height']) && $this->height = $conf['height'];
        isset($conf['fontsize']) && $this->fontsize = $conf['fontsize']+2;
    }
    
    function doImg($output=false) {

        $this->img = imagecreatetruecolor($this->width, $this->height);
        $text_c = ImageColorAllocate($this->img, mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
        $tmpC0=mt_rand(100,255);
        $tmpC1=mt_rand(100,255);
        $tmpC2=mt_rand(100,255);
        $buttum_c = ImageColorAllocate($this->img,$tmpC0,$tmpC1,$tmpC2);
        imagefill($this->img, 16, 13, $buttum_c);

        $font = APPLICATION_PATH .'/application/library/Captcha/Twist.ttf';

        for ($i=0;$i<strlen($this->code);$i++)
        {
            $tmp =substr($this->code,$i,1);
            $array = array(-1,1);
            $p = array_rand($array);
            $an = $array[$p]*mt_rand(1,10);
            imagettftext($this->img, $this->fontsize, $an, $i*($this->fontsize+3), 22, $text_c, $font, $tmp);
        }


        $distortion_im = imagecreatetruecolor ($this->width, $this->height);

        imagefill($distortion_im, 16, 13, $buttum_c);
        for ( $i=0; $i<$this->width; $i++) {
            for ( $j=0; $j<$this->height; $j++) {
                $rgb = imagecolorat($this->img, $i , $j);
                if( (int)($i+20+sin($j/$this->height*2*M_PI)*10) <= imagesx($distortion_im)&& (int)($i+20+sin($j/$this->height*2*M_PI)*10) >=0 ) {
                    imagesetpixel ($distortion_im, (int)($i+10+sin($j/$this->height*2*M_PI-M_PI*0.1)*4) , $j , $rgb);
                }
            }
        }
        //�����������;
        $count = 160;//�������ص�����
        for($i=0; $i<$count; $i++){
            $randcolor = ImageColorallocate($distortion_im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($distortion_im, mt_rand()%$this->width , mt_rand()%$this->height , $randcolor);
        }

        $rand = mt_rand(5,30);
        $rand1 = mt_rand(15,25);
        $rand2 = mt_rand(5,10);
        for ($yy=$rand; $yy<=+$rand+2; $yy++){
            for ($px=-80;$px<=80;$px=$px+0.1)
            {
                $x=$px/$rand1;
                if ($x!=0)
                {
                    $y=sin($x);
                }
                $py=$y*$rand2;

                imagesetpixel($distortion_im, $px+80, $py+$yy, $text_c);
            }
        }

        if($output){
            Header("Content-type: image/JPEG");
            ImagePNG($distortion_im);
            ImageDestroy($distortion_im);
            ImageDestroy($this->img);
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
    
    public function getCode(){
        return $this->code;
    }
}