<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * 创蓝短信接口
 */
class ChuanglanSMS{
	
	const ISENDURL='https://intapi.253.com/mt';//单发接口
	const IQUERYURL='https://intapi.253.com/bi';
	const BAT_SENDURL='https://intapi.253.com/batchmt';	//群发接口

	private $_sendUrl='';				// 发送短信接口url
	private $_queryBalanceUrl='';	// 查询余额接口url

	private $_un;			// 账号
	private $_pw;			// 密码

	/**
	 * 构造方法
	 * @param string $account  接口账号
	 * @param string $password 接口密码
	 */
	public function __construct($account,$password){
		$this->_un=$account;
		$this->_pw=$password;
	}

	
	/**
	 * 国际短信发送
	 * @param string $phone   	手机号码
	 * @param string $content 	短信内容
	 * @param integer $isreport	是否需要状态报告
	 * @return void
	 */
	public function sendInternational($phone,$content,$isreport=0){
	    $count = count($phone);
	    if(is_array($phone)){   //如果是数组多个手机号情况
            $phone = implode(',',$phone);
        }

		$requestData=array(
			'un'=>$this->_un,
			'pw'=>$this->_pw,
			'sm'=>$content,
			'da'=>$phone,
			'rd'=>$isreport,
			'rf'=>2,
			'tf'=>3,
		);

		$param='un='.$this->_un.'&pw='.$this->_pw.'&sm='.urlencode($content).'&da='.$phone.'&rd='.$isreport.'&rf=2&tf=3';
        $url=ChuanglanSMS::ISENDURL.'?'.$param;//单发接口
        //$url=ChuanglanSMS::BAT_SENDURL.'?'.$param;//群发接口
        if($count>1){
            $url=ChuanglanSMS::BAT_SENDURL.'?'.$param;//群发接口
        }

		return $this->_request($url);
	}

	

	/**
	 * 查询余额
	 * @return String 余额返回
	 */
	public function queryBalanceInternational(){
		$requestData=array(
			'un'=>$this->_un,
			'pw'=>$this->_pw,
			'rf'=>2
		);

		$url=ChuanglanSMS::IQUERYURL.'?'.http_build_query($requestData);
		return $this->_request($url);
	}

	/* ========== 业务模块 ========== */

	/* ========== 功能模块 ========== */
	/**
	 * 请求发送
	 * @return string 返回状态报告
	 */
	private function _request($url){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	/* ========== 功能模块 ========== */
}