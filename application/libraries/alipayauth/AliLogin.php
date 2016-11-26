<?php

class AliLogin {
	
	private $format = "json";
	private $charset = "UTF-8";
	private $fileCharset = "UTF-8";
	private $appId = "2016102100728300";
	private $postCharset = "UTF-8";  // 表单提交字符集编码
	private $rsaPrivateKeyFilePath;  //私钥文件路径
	private $gatewayUrl = "https://openapi.alipaydev.com/gateway.do"; //https://openapi.alipay.com/gateway.do
	private $rsaPrivateKey = "MIICXQIBAAKBgQDst7bCTETY6DD5em21VZiS5Gz0pJ0Of/d+yjbqRibLRNKqBNm0WxqBFaJtI5yOkOOx4nvTIpfAaF8767iXhkVeDidOcI7wAYEmwB5quAgHRgJ88D7czvx1hqnky049dtNqVpN/8Rm7RfuAIsPNbcjv8cBMG3E9PHkwx6ND1gSdBwIDAQABAoGAKTV+QmrmnWAmjnfKu4OwMPpFIX26vbh3TIJFdxlJTZ+okOKRR72IaqpqgVduvL7qdKA58DKYem1xDsxlcTN1xak3bWMTcSjV7VVuNnzxfiJiO2ij4uVou8xFncZIR37ZLAx2ALY4KvZ0rZ+so+vYaK0gUeulMscuKRRGqEFWERECQQD5Y5JacqO2EYOfnLWqKUdZKhh66LK36fY55zzEzOvaOxgvlyc3SBHfaXWAB7iYw9M2DFHOEr5Bv8BwH5/9cGf5AkEA8v4mnNtsWlsOsFMgfGE6N6WS9CEnHyLOK7jCjMaGHWBoGvUj1WBGuZQJaoVAPVQsbZhPY7cSy5Uma32GXLBs/wJBAKwH27U4z2WQv1MjLs2qm+UN/MUML/xeD9PxhyHamfd4PD7X7d1cgbezb7JZoSUAMHpgFS4qD8QbGgw+RIb3O0ECQEkwvDiq7uwYWUhLAZH1Ry/Ts3vNMJd0SF1q/U6hzWuzyie0huKSaTskl+F52WufmvI32lHSptqjRjtCR+JLUckCQQDhhyGu41vefeK6flcMPz7KukTDSWMTkPnXbgsXWmFt0h+xtxDqQ7ohmMT+ElAXkoeMAoAMKgkNaVwf1leHC2Xe"; //请填写开发者私钥去头去尾去回车，一行字符串
	private $alipayrsaPublicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDIgHnOn7LLILlKETd6BFRJ0GqgS2Y3mn1wMQmyh9zEyWlz5p1zrahRahbXAfCfSqshSNfqOmAQzSHRVjCqjsAw1jyqrXaPdKBmr90DIpIxmIyKXv4GGAkPyJ/6FTFY99uhpiq0qadD/uSzQsefWo0aTvP/65zi3eof7TcZ32oWpwIDAQAB";//请填写支付宝公钥，一行字符串

    
	 /**
	 * 生成sign
	 * @param unknown $params
	 * @param string $signType
	 */
	public function generateSign($params, $signType = "RSA") {
	    return $this->sign($this->getSignContent($params), $signType);
	}
	
	protected function sign($data, $signType = "RSA") {
	    if($this->checkEmpty($this->rsaPrivateKeyFilePath)){
	        $priKey=$this->rsaPrivateKey;
	        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
	                wordwrap($priKey, 64, "\n", true) .
	                "\n-----END RSA PRIVATE KEY-----";
	    }else {
	        $priKey = file_get_contents($this->rsaPrivateKeyFilePath);
	        $res = openssl_get_privatekey($priKey);
	    }
	
	    ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
	
	    if ("RSA2" == $signType) {
	        openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
	    } else {
	        openssl_sign($data, $sign, $res);
	    }
	
	    if(!$this->checkEmpty($this->rsaPrivateKeyFilePath)){
	        openssl_free_key($res);
	    }
	    $sign = base64_encode($sign);
	    return $sign;
	}
	
	/**
	 * 
	 * @param unknown $params
	 * @return string
	 */
	protected function getSignContent($params) {
	    
	    ksort($params);
	    $stringToBeSigned = "";
	    $i = 0;
	    foreach ($params as $k => $v) {
	        if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
	
	            // 转换成目标字符集
	            $v = $this->characet($v, $this->postCharset);
	
	            if ($i == 0) {
	                $stringToBeSigned .= "$k" . "=" . "$v";
	            } else {
	                $stringToBeSigned .= "&" . "$k" . "=" . "$v";
	            }
	            $i++;
	        }
	    }
	    unset ($k, $v);
	    return $stringToBeSigned;
	}
	
	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *    if is null , return true;
	 **/
	protected function checkEmpty($value) {
	    if (!isset($value))
	        return true;
	    if ($value === null)
	        return true;
	    if (trim($value) === "")
	        return true;
	
	    return false;
	}
	
	/**
	 * 转换字符集编码
	 * @param $data
	 * @param $targetCharset
	 * @return string
	 */
	function characet($data, $targetCharset) {
	
	    if (!empty($data)) {
	        $fileType = $this->fileCharset;
	        if (strcasecmp($fileType, $targetCharset) != 0) {
	            $data = mb_convert_encoding($data, $targetCharset, $fileType);
	        }
	    }
	    return $data;
	}
}