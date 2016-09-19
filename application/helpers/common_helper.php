<?php
function js($dirname, $file_name, $version=1.0)
{
    echo '<script type="text/javascript" src="'.$dirname.'/js/'.$file_name.'.js?v='.$version.'"></script>';
}

function css($dirname, $file_name, $version=1.0)
{
    echo '<link rel="stylesheet" type="text/css" href="'.$dirname.'/css/'.$file_name.'.css?v='.$version.'"/>';
}

function pr($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function redirectAction($uri = '', $method = 'location', $http_response_code = 302)
{
    if (!preg_match('#^https?://#i', $uri)) {
        $uri = base_url($uri);
    }

    switch($method) {
        case 'refresh' :
            header("Refresh:0;url=".$uri);
            break;
        default :
            header("Location: ".$uri, TRUE, $http_response_code);
            break;
    }
    exit;
}

/**
 * Validate mobile phone
 * @param unknown $mobile
 * @return boolean
 */
function valid_mobile($mobile)
{
    return (!preg_match('/^1[23456789]\d{9}$/', $mobile)) ? FALSE : TRUE;
}

/**
 * Validate email address
 *
 * @access    public
 * @return    bool
 */
function valid_email($address)
{
    return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
}


/**
 * 发送邮件
 * @param string $mail_to
 * @param string $mail_subject
 * @param string $mail_message
 * @param string $mail_from
 * @param string $mail_name
 */
function sendEmails($mail_to, $mail_subject, $mail_message, $mail_from, $mail_name='')
{
    $CI = & get_instance();
    $CI->load->library('email');
    $config['protocol'] = 'sendmail';
    $config['charset'] = 'utf-8';
    $config['wordwrap'] = TRUE;
    $config['mailtype'] = 'html';
    $CI->email->initialize($config);

    $CI->email->from($mail_from, $mail_name);
    $CI->email->to($mail_to);
    $CI->email->subject($mail_subject);
    $CI->email->message($mail_message);
    $CI->email->send();
    $CI->email->clear();
}

/**
 * 发送邮件
 * @param unknown $recipient
 * @param string $subject
 * @param string $message
 */
function send_email($recipient, $subject = 'Test email', $message = 'Hello World')
{
    return mail($recipient, $subject, $message);
}


function callApi($url, $postParams)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//https请求，不验证证书
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//https请求，不验证hosts
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
    $receive = curl_exec($ch);
    if ($receive === FALSE) {
        return '{"code":"1","msg":"'.curl_error($ch).'"}';
    }
    curl_close($ch);

    return $receive;
}

/**
 * 导入文件读取函数
 * @param 传入的文件名称 $filename
 * @return string
 */
function getCSVfileContent($filename)
{
    $row = 1;//第一行开始
    if (($handle = fopen($filename, 'r')) !== false) {
        while(($dataSrc = fgetcsv($handle)) !== false) {
            $num = count($dataSrc);
            for ($c=0; $c < $num; $c++) {
                if($row === 1) {
                    $dataName[] = mb_convert_encoding($dataSrc[$c], 'UTF-8', 'GBK');//字段名称
                } else {
                    foreach ($dataName as $k=>$v) {
                        if($k == $c) {
                            $data[$v] = mb_convert_encoding($dataSrc[$c], 'UTF-8', 'GBK');
                        }
                    }
                }
            }
            if(!empty($data)) {
                $CSVfileContent[] = $data;
                unset($data);
            }
            $row++;
        }
        fclose($handle);
        return $CSVfileContent;
    }
}



