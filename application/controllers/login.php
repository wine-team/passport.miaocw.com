<?php 
class Login extends MJ_Controller
{
    public function _init()
    {
        $this->load->helper(array('ip'));
        $this->load->library(array('encrypt', 'sms'));
        $this->load->model('advert_model', 'advert');
        $this->load->model('user_model', 'user');
        $this->load->model('user_log_model','user_log');
        $this->load->model('getpwd_phone_model', 'getpwd_phone');
    }
    
    public function index()
    {   
    	if ($this->frontUser) {
            $this->redirect($this->config->main_base_url);
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $parseUrl = parse_url($_SERVER['HTTP_REFERER']);
            if (isset($parseUrl['query']) && strpos($parseUrl['query'], 'backurl') !== false) {
                $data['backurl'] = urldecode(strstr($parseUrl['query'], 'http'));
            } else {
                $data['backurl'] = $_SERVER['HTTP_REFERER'];
            }
        } else {
            $data['backurl'] = $this->config->main_base_url;
        }
        $this->load->view('login/index', $data);
    }
    
     /**
     * 登录提交页面
     */
    public function loginPost()
    {
        $postData = $this->input->post();
        if($this->validateParam($postData['user_name'])){
        	$this->jsonMessage('请输入用户名');
        }
        if($this->validateParam($postData['password'])){
        	$this->jsonMessage('请输入用户名');
        }
        $result = $this->user->login($postData);
        if( $result->num_rows()<=0 ){
        	$this->jsonMessage('账号或密码错误');
        }
        $user = $result->row(0);
        if($user->flag==2){
        	$this->jsonMessage('账号被冻结');
        }
        $userInfor = array(
        	'uid' => $user->uid,
            'userName' => $postData['user_name']
        );
        $expireTime = empty($postData['remember']) ? 7200 : 435200;//是不是永久登陆
        set_cookie('frontUser',base64_encode(serialize($userInfor)),$expireTime);
        $this->cache->memcached->save('frontUser', base64_encode(serialize($userInfor)),$expireTime);
        $backUrl = empty($postData['back_url']) ? $this->config->main_base_url : $postData['back_url'];
        $param = array(
        		   'uid'  => $user->uid,
        		   'log_time' => date('Y-m-d H:i:s'),
                   'ip_from'  => getIP(),
        		   'operate_type'  => 1,
        		   'status' => 1
        );
        $this->user_log->insertUserLog($param);
        $this->jsonMessage('',$backUrl);
    }
    
     /**
     * 退出登陆
     */
    public function logout()
    {
        if (get_cookie('frontUser')) {
            delete_cookie('frontUser');
        }
        $this->cache->memcached->delete('frontUser');
        $this->redirect($this->config->main_base_url);
    }
    
    
     /**
     * 验证登录页手机动态码
     * cyl
     */
    public function checkPhone()
    {
        $phone = $this->input->post('phone');
        $captcha = $this->input->post('captcha');
    
        if (strtoupper($captcha) != strtoupper(get_cookie('captcha'))) {
            $this->jsonMessage('验证码不正确');
        }
        if (!valid_mobile($phone)) {
            $this->jsonMessage('手机号码有误');
        }
        $code = mt_rand(1000, 9999);
        $this->db->trans_start();
        $result = $this->getpwd_phone->validateName(array('mobile_phone'=>$phone));
        if ($result->num_rows() > 0) {
            $result1 = $this->getpwd_phone->updateGetpwdPhone(array('mobile_phone'=>$phone, 'code'=>$code));
        } else {
            $result1 = $this->getpwd_phone->insertGetpwdPhone(array('mobile_phone'=>$phone, 'code'=>$code));
        }
        $this->sendToSms($phone, '您于'.date('Y-m-d H:i:s').'正在使用验证码登录会员，验证码为:'.$code.'，有效期为10分钟，请勿向他人泄漏。');
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            echo json_encode(array('status'=> true));exit;
        } else {
            $this->jsonMessage('网络繁忙，请稍后重新获取验证码');
        }
    }
}