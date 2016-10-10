<?php
class Forget extends MW_Controller
{
    public function _init()
    {
        $this->load->helper(array('email'));
        $this->load->library(array('encrypt', 'sms/sms'));
        $this->load->model('user_model', 'user');
        $this->load->model('getpwd_phone_model', 'getpwd_phone');
    }
    
    /**
     * 填写账户名称
     */
    public function index()
    {
        if ($this->frontUser) { //如果已经登录，就跳转到首页。
            $this->redirect($this->config->main_base_url);
        }
        $captcha = $this->getCaptcha(18, 130, 36);
        $data['captcha'] = $captcha;
        $this->load->view('forget/index', $data);
    }
    
     /**
     * 验证码
     */
    public function ajaxJsonCaptcha()
    {
        $captcha = $this->getCaptcha(18, 130, 36, 4);
        echo json_encode($captcha);exit;
    }
    
    /**
     * 验证账户
     */
    public function alidateUser()
    {
        $username = $this->input->post('username');
        if ($this->validateParam($username)) {
            $this->jsonMessage('用户名必填');
        }
        if (strtolower($this->input->post('captcha')) != strtolower(get_cookie('captcha'))) {
            $this->jsonMessage('验证码错误');
        }
        $result = $this->user->validateName($username);
        if ($result->num_rows() <= 0) {
            $this->jsonMessage('用户名不存在');
        }
        $encodename = $this->encrypt->encode($username);
        $this->jsonMessage('', base_url('forget/confirm').'?keycode='.urlencode($encodename));
    }
    
    /**
     * 使用手机或邮箱找回
     */
    public function confirm()
    {
        if ($this->uid) { //如果已经登录，就跳转到首页。
            $this->redirect($this->config->main_base_url);
        }
        $username = urldecode($this->encrypt->decode($this->input->get('keycode')));
        $result = $this->user->validateName($username);
        if ($result->num_rows() <= 0) {
            $this->alertJumpPre('帐号有误，请联系客服解决问题');
        }
        $user = $result->row();
        if (valid_mobile($username)) {
            $phone = $username;
        } else {
            $phone = $user->phone;
        }
        $data['user_name'] = empty($user->phone) ? $user->email : $user->phone;
        $data['encode_phone'] = $this->encrypt->encode($phone);
        $data['phone'] = substr_replace($phone, '****', 3, 4);
        $this->load->view('forget/confirm', $data);
    }
    
    //发送短信
    public function checkPhone()
    {
        $phone = $this->encrypt->decode($this->input->post('phone'));
        if (!valid_mobile($phone)) {
            $this->jsonMessage('手机号码有误');
        }
        $code = mt_rand(1000, 9999);
        $this->db->trans_start();
        $result = $this->getpwd_phone->validateName(array('phone'=>$phone));
        if ($result->num_rows() > 0) {
            $this->getpwd_phone->updateGetpwdPhone(array('phone'=>$phone, 'code'=>$code));
        } else {
            $this->getpwd_phone->insertGetpwdPhone(array('phone'=>$phone, 'code'=>$code));
        }
        $this->sendToSms($phone, '您于'.date('Y-m-d H:i:s').'找回密码操作，验证码为:'.$code.'，为了你的账户安全，请不要将验证码泄露给任何人。有效期为10分钟。');
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            echo json_encode(array('status'=> true));exit;
        } else {
            $this->jsonMessage('手机号码有误');
        }
    }
    
    /**
     * 使用手机或邮箱找回验证
     */
    public function confirmValidate()
    {
        $phone = $this->encrypt->decode($this->input->post('phone'));
        if (!valid_mobile($phone)) {
            $this->jsonMessage('手机号码有误');
        }
        $validate = array(
                'phone'  => $phone,
                'verify' => $this->input->post('verify')
        );
        $verify = $this->getpwd_phone->validateName($validate, true);
        if ($verify->num_rows() <= 0) { //验证码无效
            $this->jsonMessage('验证码无效');
        }
        $encodename = $this->encrypt->encode($this->input->post('username'));
    
        $this->jsonMessage('', base_url('forget/modify').'?keycode='.urlencode($encodename));
    }
    
    /**
     * 设置密码页面
     */
    public function modify()
    {
        if ($this->frontUser) { //如果已经登录，就跳转到首页。
            $this->redirect($this->config->main_base_url);
        }
        $username = urldecode($this->encrypt->decode($this->input->get('keycode')));
        $result = $this->user->validateName($username);
        if ($result->num_rows() <= 0) {
            $this->alertJumpPre('帐号有误，请联系客服解决问题');
        }
        $data['user'] = $result->row();
        $this->load->view('forget/modify', $data);
    }
    
    /**
     * 设置密码页面
     */
    public function modifyValidate()
    {
        if ($this->input->post('password') != $this->input->post('confirm_password')) {
            $this->jsonMessage('密码输入不一致');
        }
        $result = $this->user->validateName($this->input->post('username'));
        if ($result->num_rows() <= 0) {
            $this->jsonMessage('不可修改密码，请联系管理员');
        }
        $modify = $this->user->modifyPassword($this->input->post());
        if (!$modify) {
            $this->jsonMessage('服务器忙，请稍候再试');
        }
        $this->jsonMessage('', base_url('forget/setSuccess'));
    }
    
    /**
     * 设置密码成功页面
     */
    public function setSuccess()
    {
        if ($this->frontUser) { //如果已经登录，就跳转到首页。
            $this->redirect($this->config->main_base_url);
        }
        $this->load->view('forget/success');
    }
}