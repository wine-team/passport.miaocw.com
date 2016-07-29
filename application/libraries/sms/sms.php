<?php
class Sms
{
    private $sms;
    
    public function sendSms($mobile,$content,$type) {
        switch($type){
            case 1:
                include_once('sms_56.php');
                $this->sms = new Sms_56();
                break;
            case 2:
                include_once('sms_eee.php');
                $this->sms = new Sms_eee();
                break;
            default:
                break;
        }
        $status = $this->sms->sendSMS($mobile, $content);
        if($status){
            return true;
        }else{
            return false;
        }
    }

    public function getError()
    {
        return $this->sms->getError();
    }

}