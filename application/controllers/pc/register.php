<?php 
class Register extends MJ_Controller
{
    public function _init()
    {
        $this->load->helper(array('email'));
        $this->load->library(array('encrypt', 'sms/sms'));
        $this->load->model('user_model', 'user');
        $this->load->model('user_account_model', 'user_account');
        $this->load->model('user_detail_model', 'user_detail');
        $this->load->model('getpwd_phone_model', 'getpwd_phone');
    }
    
    /**
     * 注册页面
     */
    public function index()
    {
        if ($this->frontUser) {
            $this->redirect($this->config->main_base_url);
        }
        $username = $this->input->get('username');
        if (!empty($username)) {
            $data['parent_id'] = $username;
        } else {
            $data['parent_id'] = get_cookie('user_name');
        }
        $data['captcha'] = $this->getCaptcha();
        $this->load->view('pc/register/index', $data);
    }
    
    /**
     * 注册提交页面
     */
    public function doRegister()
    {
        $username = $this->input->post('username');
        $mobilePhone = $this->input->post('mobile_phone');
        if (!valid_mobile($mobilePhone)) {
            $this->jsonMessage('手机号码格式有误');
        }
        if ($this->input->post('password') != $this->input->post('confirm_password')) {
            $this->jsonMessage('密码输入不一致');
        }
        if (!$this->input->post('is_check')) {
            $this->jsonMessage('用户协议必选');
        }
        $verify = $this->getpwd_phone->validateName($this->input->post(), true);
        if ($verify->num_rows() <= 0) { //验证码无效
            $this->jsonMessage('动态密码无效');
        }
        $result = $this->user->validateName($this->input->post('username'));
        if ($result->num_rows() > 0) {
            $this->jsonMessage('该用户名已经存在');
        }
        $result = $this->user->validateMobilePhone($this->input->post('mobile_phone'));
        if ($result->num_rows() > 0) {
            $this->jsonMessage('该手机号码已经注册');
        }
        if ($this->input->post('parent_id')) {
            $parent = $this->user->validateName($this->input->post('parent_id'));
            if ($parent->num_rows() > 0) {
                $parent_id = $parent->row()->uid;
            } else {
                $this->jsonMessage('推荐人不存在');
            }
        } else {
            $parent_id = UTID_BEIZHU;
        }
        
        $this->db->trans_start();
        $userId = $this->user->insertUser($this->input->post(), $parent_id);
        $userAccountId = $this->user_account->insertUserAccount($this->input->post(), $userId, $parent_id);
        $userDetailId = $this->user_detail->insertUserDetail($this->input->post(), $userId);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->jsonMessage('服务器忙，请稍候再试');
        }
        
        $userType = $this->usertype(UTID_CUSTOMER);
        $session = array(
            'ACT_UID'      => $userId,
            'ACT_UTID'     => UTID_CUSTOMER,
            'ACT_TYPENAME' => urlencode($userType['type_zh']),
            'ACT_TYPE'     => $userType['type_en'],
            'ACT_EXTRA'    => 0,
            'ALIAS_NAME'   => urlencode($username),
            'OWNER_ID'     => $userId,
            'OWNER_NAME'   => $username,
            'PARENT_ID'    => $parent_id,
        );
        set_cookie('frontUser', serialize($session), 43250);
        $this->memcache->setData('frontUser', serialize($session));
        $backurl = $this->input->post('backurl') ? urldecode($this->input->post('backurl')) : site_url('pc/register/regsuccess');
        echo json_encode(array(
            'status'   => true,
            'messages' => $backurl,
        ));exit;
    }
    
    /**
     * 注册成功页面
     */
    public function regsuccess()
    {
        $frontUser = $this->frontUser;
        $data['username'] = $frontUser['OWNER_NAME'];
        $this->load->view('pc/register/regsuccess', $data);
    }
    
    /**
     * 验证用户是否注册过。
     */
    public function validateName()
    {
        $result = $this->user->validateName($this->input->post('username'));
        if ($result->num_rows() > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
        exit;
    }
    
    /**
     * 验证用户是否注册过。
     */
    public function validateMobilePhone()
    {
        $result = $this->user->validateMobilePhone($this->input->post('mobile_phone'));
        if ($result->num_rows() > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
        exit;
    }
    
    /**
     * 验证推荐人是否存在。
     */
    public function validateParentId()
    {
        $result = $this->user->validateName($this->input->post('parent_id'));
        if ($result->num_rows() > 0) {
            echo 'true';
        } else {
            echo 'false';
        }
        exit;
    }
    
    /**
     * 验证验证码是否一致
     */
    public function validateVerify()
    {
        $result = $this->getpwd_phone->validateName($this->input->post(), true);
        if ($result->num_rows() > 0) { //验证码有效
            echo 'true';
        } else {
            echo 'false';
        }
        exit;
    }
    
    public function checkPhone()
    {
        $phone = $this->input->post('phone');
        $captcha = $this->input->post('captcha');

        if (strtoupper($captcha) != strtoupper(get_cookie('captcha'))) {
            $this->jsonMessage('验证码不正确');
        }
        if (!valid_mobile($phone)) {
            $this->jsonMessage('手机号码格式有误');
        }
        $result = $this->user->validateMobilePhone($phone);
        if ($result->num_rows() > 0) {
            $this->jsonMessage('手机号已注册');
        }
        $code = mt_rand(1000, 9999);
        $this->db->trans_start();
        $result = $this->getpwd_phone->validateName(array('mobile_phone'=>$phone));
        if ($result->num_rows() > 0) {
            $result1 = $this->getpwd_phone->updateGetpwdPhone(array('mobile_phone'=>$phone, 'code'=>$code));
        } else {
            $result1 = $this->getpwd_phone->insertGetpwdPhone(array('mobile_phone'=>$phone, 'code'=>$code));
        }
        $this->sendToSms($phone, '您于'.date('Y-m-d H:i:s').'注册贝竹会员，验证码为:'.$code.'，有效期为10分钟。');
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            echo json_encode(array('status'=> true));exit;
        } else {
            $this->jsonMessage('网络繁忙，请稍后重新获取验证码');
        }
    }
}