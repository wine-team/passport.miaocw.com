<?php 
class Register extends MW_Controller
{
    public function _init()
    {
        $this->load->helper(array('ip','email'));
        $this->load->library(array('encrypt'));
        $this->load->model('user_model', 'user');
        $this->load->model('user_log_model','user_log');
        $this->load->model('getpwd_phone_model', 'getpwd_phone');
        $this->load->model('User_coupon_set_model','user_coupon_set');
        $this->load->model('User_coupon_get_model','user_coupon_get');
    }
    
     /**
     *注册页面
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
        if (isset($_SERVER['HTTP_REFERER'])) {
            $parseUrl = parse_url($_SERVER['HTTP_REFERER']);
            if (isset($parseUrl['query']) && strpos($parseUrl['query'], 'backurl') !== false) {
                $data['backurl'] = urldecode(strstr($parseUrl['query'], 'http'));
            } else {
                $data['backurl'] = $this->input->get('backurl') ? urldecode($this->input->get('backurl')) : $_SERVER['HTTP_REFERER'];
            }
        } else {
            $data['backurl'] = $this->config->main_base_url;
        }
        $data['captcha'] = $this->getCaptcha();
        $this->load->view('register/index',$data);
    }
    
    /**
     *注册提交页面
     */
    public function doRegister()
    {
        $username = $this->input->post('phone',true);
        if (empty($username)) {
            $this->jsonMessage('请输入手机号码');
        }
        if (strlen($this->input->post('password'))<6 || strlen($this->input->post('confirm_password'))<6){
            $this->jsonMessage('密码长度不小于6位');
        }
        if (!valid_mobile($username) ) {
            $this->jsonMessage('手机号码格式有误');
        }
        if ($this->input->post('password') != $this->input->post('confirm_password')) {
            $this->jsonMessage('密码输入不一致');
        }
        $result = $this->user->validateName($username);
        if ($result->num_rows() > 0) {
            $this->jsonMessage('该用户名已经存在');
        }
        if ($this->input->post('parent_id')) {
            $parent = $this->user->validateName($this->input->post('parent_id'));
            if ($parent->num_rows() > 0) {
                $parent_id = $parent->row(0)->uid;
            } else {
                $this->jsonMessage('推荐码无效');
            }
        } else {
            $parent_id = 1;// 妙处网总部
        }
        $this->db->trans_start();
        $userId = $this->user->insert($this->input->post(), $parent_id);
        $getCoupon = $this->getCoupon($coupon_set_id=1, $userId);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->jsonMessage('服务器忙，请稍候再试');
        }
        $userInfor = array(
            'uid'      => $userId,
            'userName' => $username
        );
        $userInfor = array(
            'uid'       => $user->uid,
            'aliasName' => $user->alias_name,
            'userPhone' => $user->phone,
            'userEmail' => $user->email,
            'parentId'  => $user->parent_id,
            'userPhoto' => $user->photo,
        );
        set_cookie('frontUser', base64_encode(serialize($userInfor)), 7200);
        $this->cache->memcached->save('frontUser', base64_encode(serialize($userInfor)),7200);
        $backurl = $this->input->post('backurl') ? urldecode($this->input->post('backurl')) : $this->config->ucenter_url;
        $params = array(
            'uid'  =>  $userId,
            'log_time' => date('Y-m-d H:i:s'),
            'ip_from'  => getIP(),
            'operate_type'  => 1,
            'status' => 1
        );
        $this->user_log->insert($params);
        $this->jsonMessage('',$backurl);
    }
    
     /**
     * 获取优惠劵
     * @param unknown $coupon_set_id
     */
    private function getCoupon($coupon_set_id, $uid)
    {
        $couponRes = $this->user_coupon_set->findById($coupon_set_id);
        if ($couponRes->num_rows()<=0) {
            return false;
        }
        $couponSet = $couponRes->row(0);
        $param = array(
            'coupon_set_id' => $couponSet->coupon_set_id,
            'coupon_name'   => $couponSet->coupon_name,
            'uid'           => $uid,
            'scope'         => $couponSet->scope,
            'related_id'    => $couponSet->related_id,
            'amount'        => $couponSet->amount,
            'condition'     => $couponSet->condition,
            'note'          => $couponSet->note,
            'start_time'    => $couponSet->start_time,
            'end_time'      => $couponSet->end_time,
            'status'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
        );
        
        $status = $this->user_coupon_get->insert($param);
        if ($status) {
             $res = $this->user_coupon_set->setCouponNum($coupon_set_id,$num=1);
        }
        return $status;
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
    public function validatePhone()
    {
        $result = $this->user->validatePhone($this->input->post('phone'));
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
        $result = $this->user->validatePhone($phone);
        if ($result->num_rows() > 0) {
            $this->jsonMessage('手机号已注册');
        }
        $code = mt_rand(1000, 9999);
        $this->db->trans_start();
        $result = $this->getpwd_phone->validateName(array('phone'=>$phone));
        if ($result->num_rows() > 0) {
            $result1 = $this->getpwd_phone->update(array('phone'=>$phone, 'code'=>$code));
        } else {
            $result1 = $this->getpwd_phone->insert(array('phone'=>$phone, 'code'=>$code));
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