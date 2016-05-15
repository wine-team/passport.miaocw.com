<?php
header('Content-Type:text/html;Charset=utf-8');

function getIP()
{
    if (@$_SERVER["HTTP_X_FORWARDED_FOR"])
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if (@$_SERVER["HTTP_CLIENT_IP"])
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if (@$_SERVER["REMOTE_ADDR"])
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (@getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (@getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (@getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "122.234.148.84";//贝竹电子商务,默认IP;
    $ip = strpos($ip,'27.0.0.1') ? '122.234.148.84' : $ip;
    return $ip;
}
/**
 * 根据ip进行位置定位，调新浪微博接口
 * @param string $ip
 * @return boolean|Ambigous <mixed, Ambigous, unknown, string>
 */
function getIpLookup($ip = ''){
    if(empty($ip)){
        $ip = getIp();
    }
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip='.$ip); // 新浪微博api
    if(empty($res)){ return false; }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if(!isset($jsonMatches[0])){ return false; }
    $json = json_decode($jsonMatches[0], true);
    if(isset($json['ret']) && $json['ret'] == 1){
        $json['ip'] = $ip;
        unset($json['ret']);
    }else{
        return false;
    }
    return $json;
}