<?php

/***********
 * Class Sms 56 公司短信接口
 */
class Sms_56
{
    private $userName = 'hwh8836';    //用户账号
    private $password = '4gb4k1to';   //MD5位32密码
	private $smsnumber = '1016';      //平台
	private $comid = '1612';           //企业id
    private $signature = '【贝竹】';
    private $http = 'http://api.56dxw.com/sms/HttpInterfaceMd5.aspx';
    private $error;
    
    function sendSMS($mobile,$content,$time='')
    {
        $content = $content.$this->signature;
        $content=@mb_convert_encoding($content,'gb2312','');
        $data = array(
                    'comid'=>$this->comid,				
                    'username'=>$this->userName,					
                    'userpwd'=>md5($this->password),	
                    'smsnumber'=>$this->smsnumber,
                    'handtel'=>$mobile,				//号码
                    'sendcontent'=>$content,			//内容
                    'sendtime'=>$time		       //定时发送
                );
        $re= $this->postSMS($this->http,$data);			//POST方式提交
        if(trim($re) == '1')
        {
            return true;  ///发送成功
        }
        else
        {
            $this->error = $re;
            return false;
        }
    }
    
    function postSMS($url,$data='')
    {
        $row = parse_url($url);
        $host = $row['host'];
        if(!empty($row['port']))
        {
            $port = $row['port'];
        }
        else
        {
            $port =80;
        }
        //$port = $row['port'] ? $row['port']:80;
        $file = $row['path'];
        $post="";
        while (list($k,$v) = each($data))
        {
            $post .= $k."=".$v."&";	//转URL标准码
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

    public function getError()
    {
        return $this->error;
    }
}