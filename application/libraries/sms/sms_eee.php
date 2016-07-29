<?php

/**************************************************
 * Class Sms_eee
 * 公司 第一信息短信平台
 * http://www.eee1.cn/
 * http://web.1xinxi.cn/default.aspx  15088760762 密码 298354
 */
class Sms_eee
{
    private $userName = '15088760762';    //用户账号
    private $password = '275DC5999D196889D464721C2498';
    private $signature = '贝竹';
    private $http = 'http://sms.1xinxi.cn/asmx/smsservice.aspx';
    private $error;

    function sendSMS($tel,$content,$date=''){
        $content = str_replace(array('【', '】'), array('<', '>'), $content);
//        $content=@mb_convert_encoding($content,'UTF-8','');
        $data = array(
            'name'    => $this->userName,
            'pwd'     => $this->password,
            'content' => $content,
            'mobile'  => $tel,
            'stime'   => $date,				//号码
            'sign'    => $this->signature,			//内容
            'type'    => 'pt',		       //定时发送
            'extno'   => ''
        );

        $info = $this->postSMS($this->http, $data);
        if($info != 0){
            $this->error = $this->getErrorMessage($info);
            return false;
        }
        return true;
    }

    /**
     * POST提交短信数据
     */
    function postSMS($url,$data=''){
        $row = parse_url($url);
        $host = $row['host'];
        $port = !empty($row['port']) ? $row['port']:80;
        $file = $row['path'];
        $post="";
        while (list($k,$v) = each($data)){
            $post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
        }
        $post = substr( $post , 0 , -1 );
        $len = strlen($post);
        $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
        if (!$fp) {
            return "$errstr ($errno)\n";
        } else {
            $receive = '';
            $out = "POST $file HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n\r\n";
            $out .= $post;
            fwrite($fp, $out);
            while (!feof($fp)) {
                $receive .= fgets($fp, 128);
            }
            fclose($fp);
            $receive = explode("\r\n\r\n",$receive);
            unset($receive[0]);
            return implode("",$receive);
        }
    }

    private function getErrorMessage($key){
        $data = array(
            0=>'提交成功',
            1=>'含有敏感词',
            2=>'余额不足',
            3=>'没有号码',
            10=>'帐号不存在',
            11=>'帐号注销',
            12=>'帐号停用',
            13=>'IP鉴权失败',
            14=>'格式错误',
            -1=>'系统异常'
        );
        if(isset($data[$key])){
            return $data[$key];
        }
        return $key;
    }

    public function getError()
    {
        return $this->error;
    }
}