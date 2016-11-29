<?php

class AliLogin {
	
	private $format = "json";
	private $charset = "UTF-8";
	private $fileCharset = "UTF-8";
	private $appId = "2016112403231302"; 
	private $postCharset = "UTF-8";  // 表单提交字符集编码
	private $rsaPrivateKeyFilePath;  //私钥文件路径
	private $gatewayUrl = "https://openapi.alipay.com/gateway.do"; //https://openapi.alipay.com/gateway.do
	private $rsaPrivateKey = "MIICXwIBAAKBgQC4tZ5DRmxevtnktPv2YsijWGyPCbErBQhCCS0QKvqzxZAdVoQ+UTxpsY9p/iezPgPvXbBAWU0H9ka/PlD4gJlCOuSL+WjYG5SzPXc+MjJ0DTQVKAygjneFqIU/B4DbbjEa1lG12pWx3qvow+/MgR/CzdWbBqz82Vxx3vTVBtfXeQIDAQABAoGBAIhIoPwcYutJP/Xqv5mca1Nyc67aRNlr/hrTMGekfpPT6jBrXGmqgLuvYhSfjOVIlZiwdNIV0atVP//tV8ry/6BHT+75dcKj/d4u7OeXQk5A/tEnjV8LzyZyU1RIG3vHCnzW7I/qWn9QlmEeX6s725Ntps5rbbUgQ5aPXmfSEaeBAkEA7KVp5MVonR+Buz3rdJCXwg3kJV7XY2yA0DELqXMN/p4XhBiU4F4stRa6PLgDXsEMuNQZ0deulnTRC/DZA1WRkQJBAMfQ0nWaSrzg9fSgF1APJ/eJsH9kGoMhwDK9IMhTQItcrJWgYyezyBXwCsBepEizaUtJLUrbOL72/NDvZPUrc2kCQQC78jwCF88YSzer0Ge6ckQ1OPcjvwlty3Ua5HkQoXJR8JlYrnU/JUx4no5XPwZeMRC7kqjXAmeE005cH3MbtNAhAkEApO+xmdPHl4uWWtv/Al3QXttwLmeiHtYwQXGuas2VvLO93jCrSG11Xu5q4Yn9z+kQpE1+LokwmSllXM4PJvU76QJBAIhc1nvF5j+wUxfBWcJyRkgNWqks95k0OqsaDOwy7qXE12PINB8pceBaxO7dRwMy0zQEApDSSwtgcA9Zj5pdNNk="; //请填写开发者私钥去头去尾去回车，一行字符串
	private $alipayrsaPublicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB";//请填写支付宝公钥，一行字符串

    
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