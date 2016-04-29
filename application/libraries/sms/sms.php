<?php
class Sms
{
    private $sms;
    public function __construct()
    {

    }

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
            case 3:
                include_once('sms_cr.php');
                $this->sms = new Sms_cr();
                break;
            case 4:
                include_once('sms_qc.php');
                $this->sms = new Sms_qc();
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