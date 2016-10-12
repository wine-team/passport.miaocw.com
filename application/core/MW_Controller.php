<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MW_Controller extends CI_Controller
{
    protected $frontUser = false;
    public $uid;
    public $aliasName; //用户昵称
    public $userPhone; //用户手机号
    public $userEmail; //用户邮箱
    public $parentId;  //上级用户UID
    public $userLevel; //用户级别
    public $userPhoto; //用户头像

    public function __construct()
    {
        parent::__construct();
        $frontUser = get_cookie('frontUser');
        if ($frontUser) {
            $this->frontUser = unserialize(base64_decode($frontUser));
            $this->uid       = $this->frontUser['uid'];
            $this->aliasName = $this->frontUser['aliasName'];
            $this->userPhone = $this->frontUser['userPhone'];
            $this->userEmail = $this->frontUser['userEmail'];
            $this->parentId  = $this->frontUser['parentId'];
            $this->userLevel = $this->frontUser['userLevel'];
            $this->userPhoto = $this->frontUser['userPhoto'];
        }
        $this->_init(); //用着重载
        
        // 开发模式下开启性能分析
        if (ENVIRONMENT === 'development') {
            $this->output->enable_profiler(TRUE);
        }
    }
    
    public function _init() {}
    
    /**
     * 验证get参数，如果get参数有一个值不为空，则返回true
     */
    protected function search_get_validate($params_get)
    {
        $is_empty = false;
        if (is_array($params_get) && !empty($params_get)) {
            foreach ($params_get as $val) {
                if (!empty($val)) {
                    $is_empty = true;
                    break;
                }
            }
        }
        return $is_empty;
    }
    
    /**
     * js提交表单数据提示。
     * @param unknown $error
     * @param string $url
     */
    public function jsonMessage($error, $url='')
    {
        if (!empty($error)) {
            if (is_array($error)) {
                $json = array('status'=>false, 'messages'=>implode('\\n', $error));
            } else {
                $json = array('status'=>false, 'messages'=>$error);
            }
        } else {
            $json = array('status'=>true, 'messages'=>$url);
        }
        echo json_encode($json);exit;
    }
    
    /**
     * 验证参数，如果参数有一个为空，则返回true
     * @param  $postData
     * @return boolean
     */
    protected function validateParam($postData)
    {
        $validate = false;
        if (is_array($postData)) { //验证checkbox，有一个不为空，则通过
            $is_empty = '';
            foreach ($postData as $val) {
                $is_empty .= $val;
            }
            if (empty($is_empty)) {
                $validate = true;
            }
        } else {
            if (empty($postData)) {
                $validate = true;
            }
        }
        return $validate;
    }
    
    /**
     * 验证参数，如果参数有一个为空，则返回true
     * @param  $postData
     * @return boolean
     */
    protected function validateArrayEmpty($postData)
    {
        if (is_array($postData)) { //验证checkbox，有一个不为空，则通过
            foreach ($postData as $val) {
                if (empty($val)){
                    return true;
                }
            }
        } else {
            if (empty($postData)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 程序执行错误跳转
     * @param 跳转路径 $url
     * @param url参数 $param
     * @param 提示信息 $message
     */
    protected function error($url, $param, $message)
    {
        if (is_array($message)) {
            foreach ($message as $val) {
                $this->error .= '<p>' . $val . '</p>';
            }
            $this->session->set_flashdata('error', $this->error);
        } else {
            $this->session->set_flashdata('error', $message);
        }
        
        $this->formatUrl($url, $param);
    }
    
    /**
     * 程序执行成功跳转
     * @param 跳转路径 $url
     * @param url参数  $param
     * @param 提示信息 $message
     */
    protected function success($url, $param, $message)
    {
        $this->session->set_flashdata('success', $message);
        $this->formatUrl($url, $param);
    }
    
    private function formatUrl($url, $param)
    {
        $len = strlen($url)-1;
        if ($url{$len} != '/') {
            $url = $url.'/';
        }
        
        if (is_array($param)) {
            $fullUrl = http_build_query($param);
            $url .= '?'.$fullUrl;
        } else {
            $url .= $param;
        }
        
        $parseUrl = parse_url($url);
        if ($parseUrl && isset($parseUrl['scheme'])) {
            $this->redirect($url);
        } else {
            $this->redirect($url);
        }
    }
    
    /**
     * 程序执行跳转
     * @param string $url
     * @param bool $secure
     */
    protected function redirect($url)
    {
        redirectAction($url);
    }
    
    /**
     * 错误回跳到首页
     * @param unknown $msg
     */
    protected function alertJumpPre($msg)
    {
        echo '<script type="text/javascript">alert("'.$msg.'");location.href="Javascript:window.history.go(-1)"</script>';exit;
    }
    
    /**
     * 分页get参数
     * @param unknown $getParam
     */
    public function pageGetParam($getParam)
    {
        $suffix = '';
        if ($getParam) {
            $param = http_build_query($getParam);
            $suffix = '?'.$param;
        }
        return $suffix;
    }
    
    public function getCaptcha($font_size=20, $img_width=100, $img_height=30, $count=4)
    {
        $this->load->helper('captcha');
        $str = 'abcdefghgkmnpqrstuvwxyzABCDEFGHJKLMNOPQRSTUXWXYZ23456789';
        $word = '';
        for ($i=0; $i < $count; $i++) {
            $word .= $str[mt_rand(0,strlen($str)-1)];
        }
        $vals = array(
            'word'       => $word,
            'img_path'   => $this->config->upload_image_path('common/captcha'),
            'img_url'    => $this->config->show_image_url('common/captcha'),
            'font_path'  => BASEPATH.'fonts/texb.ttf',
            'font_size'  => $font_size.'px',
            'img_width'  => $img_width,
            'img_height' => $img_height,
            'expiration' => '300'
        );
        $captcha = create_captcha($vals);
        set_cookie('captcha', $captcha['word'], 7200);
        return $captcha;
    }
    
    /**
     * @param unknown $telephone
     * @param unknown $content
     * @param number $sms_type 1:56短信；2:第一短信平台;3:
     */
    public function sendToSms($telephone, $content, $sms_type=2)
    {
        $this->load->library('sms/sms', NULL, 'sms');
        $is_send = $this->sms->sendSms($telephone, $content, $sms_type);
        if (!$is_send) { //发送失败，将错误内容保存起来
            $this->load->model('ym_sms_error_model', 'ym_sms_error');
            $this->ym_sms_error->insertYmSmsError($telephone, $content, $this->sms->getError(), $sms_type);
        }
    }
}