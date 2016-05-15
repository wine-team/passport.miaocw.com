<?php 
class Register extends MJ_Controller
{
    public function _init()
    {
        $this->load->helper(array('email'));
        $this->load->library(array('encrypt'));
        $this->load->model('user_model', 'user');
        $this->load->model('getpwd_phone_model', 'getpwd_phone');
    }
    
     /**
     *注册页面
     */
    public function index()
    {
        if ($this->frontUser) {
            $this->redirect($this->config->main_base_url);
        }
        $this->load->view('login/index',$data);
    }
    
    /**
     *注册提交页面
     */
    public function doRegister()
    {
        $username = $this->input->post('username');
        $type = $this->input->post('type');  //1手机注册  2邮箱注册
        $type = ( ($type==1)||($type==2) ) ? $type : '1' ;//以防别人修改type值
        if ($this->validateParam($username)){
        	$this->jsonMessage('请输入用户名');
        }
        if (strlen($this->input->post('password'))<6 || strlen($this->input->post('confirm_password'))<6){
        	$this->jsonMessage('密码长度不小于6位');
        }
        if ($type == 1){
	        if (!valid_mobile($username) ) {
	            $this->jsonMessage('手机号码格式有误');
	        }
    	}
    	if ($type == 2){
    		if (!valid_email($username)){
    			$this->jsonMessage('邮箱格式有误');
    		}
    	}
        if ($this->input->post('password') != $this->input->post('confirm_password')) {
            $this->jsonMessage('密码输入不一致');
        }
        $result = $this->user->validateName($username);
        if ($result->num_rows() > 0) {
            $this->jsonMessage('该用户名已经存在');
        }
        $this->db->trans_start();
        $userId = $this->user->insertUser($this->input->post());
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->jsonMessage('服务器忙，请稍候再试');
        }
        $userInfor = array(
        		'uid' => $userId,
        		'userName' => $username
        );
        set_cookie('frontUser', serialize($userInfor), 43250);
        $backurl = $this->input->post('back_url') ? urldecode($this->input->post('back_url')) : $this->config->ucenter_url;
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
        $this->load->view('register/regsuccess', $data);
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
        $this->sendToSms($phone, '您于'.date('Y-m-d H:i:s').'注册会员，验证码为:'.$code.'，有效期为10分钟。');
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            echo json_encode(array('status'=> true));exit;
        } else {
            $this->jsonMessage('网络繁忙，请稍后重新获取验证码');
        }
    }
}