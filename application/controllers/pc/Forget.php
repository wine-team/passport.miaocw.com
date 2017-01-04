<?php
class Forget extends MW_Controller
{
    public function _init()
    {
        $this->load->helper(array('email'));
        $this->load->library(array('encrypt', 'sms/sms'));
        $this->load->model('pc/user_model', 'user');
        $this->load->model('pc/getpwd_phone_model', 'getpwd_phone');
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
        $this->load->view('pc/forget/index', $data);
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
        $this->jsonMessage('', base_url('pc/forget/confirm').'?keycode='.urlencode($encodename));
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
        $data['user_name'] = empty($user->phone) ? $this->encrypt->encode($user->email) : $this->encrypt->encode($user->phone);
        $data['encode_phone'] = $this->encrypt->encode($phone);
        $data['phone'] = substr_replace($phone, '****', 3, 4);
        $this->load->view('pc/forget/confirm', $data);
    }
    
    //发送短信
    public function checkPhone()
    {
        $phone = $this->encrypt->decode($this->input->post('phone'));
        if (!valid_mobile($phone)) {
            $this->jsonMessage('手机号码有误');
        }
        $code = mt_rand(100000, 999999);
        $this->db->trans_start();
        $result = $this->getpwd_phone->validatePhone(array('phone'=>$phone));
        if ($result->num_rows() > 0) {
            $this->getpwd_phone->update(array('phone'=>$phone, 'code'=>$code));
        } else {
            $this->getpwd_phone->insert(array('phone'=>$phone, 'code'=>$code));
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
        $verify = $this->getpwd_phone->validatePhone($validate, true);
        if ($verify->num_rows() <= 0) { //验证码无效
            $this->jsonMessage('验证码无效');
        }
        $encodename = $this->encrypt->encode($this->input->post('username'));
    
        $this->jsonMessage('', base_url('pc/forget/modify').'?keycode='.urlencode($encodename));
    }
    
    /**
     * 设置密码页面
     */
    public function modify()
    {
        if ($this->frontUser) { //如果已经登录，就跳转到首页。
            $this->redirect($this->config->main_base_url);
        }
        $username = $this->encrypt->decode(urldecode($this->input->get('keycode')));
        $result = $this->user->validateName($username);
        if ($result->num_rows() <= 0) {
            $this->alertJumpPre('帐号有误，请联系客服解决问题');
        }
        $user = $result->row();
        $data['user'] = $user;
        $username = $user->phone ? $user->phone : $user->email;
        $data['username'] = $this->encrypt->encode($username);
        $this->load->view('pc/forget/modify', $data);
    }
    
    /**
     * 设置密码页面
     */
    public function modifyValidate()
    {
        if ($this->input->post('password') != $this->input->post('confirm_password')) {
            $this->jsonMessage('密码输入不一致');
        }
        $username = urldecode($this->encrypt->decode($this->input->post('username')));
        $result = $this->user->validateName($username);
        if ($result->num_rows() <= 0) {
            $this->jsonMessage('不可修改密码，请联系管理员');
        }
        $_POST['username'] = $username;
        $modify = $this->user->modifyPassword($this->input->post());
        if (!$modify) {
            $this->jsonMessage('服务器忙，请稍候再试');
        }
        $this->jsonMessage('', base_url('pc/forget/setSuccess'));
    }
    
    /**
     * 设置密码成功页面
     */
    public function setSuccess()
    {
        if ($this->frontUser) { //如果已经登录，就跳转到首页。
            $this->redirect($this->config->main_base_url);
        }
        $this->load->view('pc/forget/success');
    }
}