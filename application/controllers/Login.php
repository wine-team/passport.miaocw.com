<?php 
class Login extends MW_Controller
{
    public function _init()
    {
        $this->load->helper(array('ip','email'));
        $this->load->library(array('encrypt', 'sms/sms'));
        $this->load->library('alipayauth/aliLogin', NULL, 'aliLogin');
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
        $data['advert'] = $this->advert->findBySourceState(2);
        $data['captcha'] = $this->getCaptcha();
        $data['err_count'] = get_cookie('err_count');
        $this->load->view('login/index', $data);
    }
    
     /**
     * 登录提交页面
     */
    public function loginPost()
    {
        $d = $this->input->post();
        //会员登录
        if (!empty($d['act']) && $d['act'] == 1) {
            if ($this->validateParam($d['username'])) {
                $this->jsonMessage('请输入用户名');
            }
            if ($this->validateParam($d['password'])) {
                $this->jsonMessage('请输入密码');
            }
            $err_count = get_cookie('err_count');
            $result = $this->user->login($d);
            if ($result->num_rows() <=0) {
                set_cookie('err_count', $err_count + 1, 43200);
                echo json_encode(array(
                    'status'  => false,
                    'messages' => '用户名或密码错误',
                    'data' => $err_count
                ));exit;
            }
            $user = $result->row();
            if ($user->flag == 2) {
                set_cookie('err_count', $err_count + 1, 43200);
                echo json_encode(array(
                    'status'  => false,
                    'messages' => '此帐号已被冻结，请与管理员联系',
                    'data' => $err_count
                ));exit;
            }
            //验证码验证
            if ($err_count >= 3) {
                if (strtoupper($d['captcha']) != strtoupper(get_cookie('captcha'))) {
                    echo json_encode(array(
                        'status'  => false,
                        'messages' => '验证码不正确',
                        'input' => 'captcha'
                    ));exit;
                }
            }
            delete_cookie('err_count');
            //快捷登录
        } else {
            if ($this->validateParam($d['phone'])) {
                $this->jsonMessage('请输入电话号码');
            }
            if (strtoupper($d['captcha']) != strtoupper(get_cookie('captcha'))) {
                $this->jsonMessage('验证码不正确');
            }
            $user = $this->quickLogin($d);
        }
        $userInfor = array(
            'uid'       => $user->uid,
            'aliasName' => !empty($user->alias_name) ? $user->alias_name : $user->phone,
            'userPhone' => $user->phone,
            'userEmail' => $user->email,
            'parentId'  => $user->parent_id,
            'userPhoto' => $user->photo,
        );
        $expireTime = empty($d['auto_login']) ? 7200 : 7200;//是不是永久登陆
        set_cookie('frontUser',base64_encode(serialize($userInfor)), $expireTime);
        $this->cache->memcached->save('frontUser', base64_encode(serialize($userInfor)), $expireTime);
        $backUrl = empty($d['backurl']) ? $this->config->main_base_url : $d['backurl'];
        $userLog = $this->user_log->insert($user->uid, $ip_from=getIP(), $operate_type=1, $status=1);
        $this->jsonMessage('', $backUrl);
    }

    /**
     * 快速登录验证
     */
    public function quickLogin($data = array())
    {
        if (empty($data['phone'])) {
            $this->jsonMessage('请输入手机号码');
        }
        if (empty($data['verify'])) {
            $this->jsonMessage('请输入动态密码');
        }
        $result = $this->user->validatePhone($data['phone']);
        if ($result->num_rows() <= 0) {
            $this->jsonMessage('手机号码有误');
        }
        $res = $result->row(0);

        $result1 = $this->getpwd_phone->validatePhone(array('phone' => $data['phone'], 'code' => md5($data['verify'])), TRUE);
        if ($result1->num_rows() <= 0) {
            $this->jsonMessage('动态密码输入错误或已失效，请重新获取');
        }
        if ($res->flag == 2) {
            $this->jsonMessage('此帐号已被冻结，请与管理员联系');
        }
        return $res;
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
        $result = $this->getpwd_phone->validatePhone(array('phone'=>$phone));
        if ($result->num_rows() > 0) {
            $result1 = $this->getpwd_phone->update(array('phone'=>$phone, 'code'=>$code));
        } else {
            $result1 = $this->getpwd_phone->insert(array('phone'=>$phone, 'code'=>$code));
        }
        $this->sendToSms($phone, '您于'.date('Y-m-d H:i:s').'正在使用验证码登录会员，验证码为:'.$code.'，有效期为10分钟，请勿向他人泄漏。');
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            echo json_encode(array('status'=> true));exit;
        } else {
            $this->jsonMessage('网络繁忙，请稍后重新获取验证码');
        }
    }
    
     /**
      * 支付宝用户授权登陆
      * app_auth_token  令牌
      * user_id  支付宝用户唯一UID
     */
    public function alipayAuth() {
        
    	$url = "https://openapi.alipaydev.com/gateway.do"; //测试参数
    	$app_id = $this->input->get('app_id');
    	$param = array(
    			'app_id' => $app_id,
    			'method' => "alipay.system.oauth.token",
    			'charset' => "utf-8",
    			'sign_type' => "RSA",
    			'timestamp' => date('Y-m-d H:i:s'),
    			'version' => "1.0",
    			'grant_type' => "authorization_code",
    			'code' => $this->input->get('auth_code'),
    	);
    	$param['sign'] = $this->aliLogin->generateSign($param,$signType = "RSA"); //验证签名
    	$result = json_decode($this->fn_get_contents($url,$param,'post'));
    	$alipayTokenResponse = $result->alipay_system_oauth_token_response;
    	$access_token = $alipayTokenResponse->access_token;//交换令牌--用于获取用户信息
    	$user_id = $alipayTokenResponse->user_id; //用户的userId--支付宝用户的唯一userId
    	$this->getAlipayUserInfor($access_token,$app_id);
    }
    
     /**
     * 获取用户基础信息(姓别，姓名);
     * @param unknown $auth_token
     * https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7386797.0.0.ETCoNL&treeId=53&articleId=104114&docType=1#s5
     */
    private function getAlipayUserInfor($auth_token,$app_id) {
    	
        $url = "https://openapi.alipaydev.com/gateway.do"; //测试参数
        $param = array(
                'method' => "alipay.user.userinfo.share",
                'timestamp' => date('Y-m-d H:i:s'),
                'app_id' => $app_id,
                'auth_token' => $auth_token,
                'charset' => "utf-8",
                'sign_type' => "RSA",
                'version' => "1.0",
        );
        $param['sign'] = $this->aliLogin->generateSign($param,$signType = "RSA"); //验证签名
        $result = json_decode($this->fn_get_contents($url,$param,'post'));
        $alipayUserInfor = $result->alipay_user_userinfo_share_response;
        var_dump($alipayUserInfor);exit;
    }
    
     /**
      * 第三方应用授权(暂时不启用)
      * https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7386797.0.0.UiAPWO&treeId=216&articleId=105193&docType=1#s10
     */ 
    public function thirdAuth() {
    	
    	$this->load->library('alipayauth/aliLogin', NULL, 'aliLogin');
    	$url = "https://openapi.alipaydev.com/gateway.do"; //测试参数
    	$param = array(
    			'app_id' => $this->input->get('app_id'),
    			'method' => "alipay.open.auth.token.app",
    			'charset' => "utf-8",
    			'sign_type' => "RSA",
    			'timestamp' => date('Y-m-d H:i:s'),
    			'version' => "1.0",
    			'grant_type' => "authorization_code",
    	);
    	$param['biz_content'] = json_encode(array(
    			'grant_type' => "authorization_code",
    			'code' => $this->input->get('app_auth_code'),
    	));
    	$param['sign'] = $this->aliLogin->generateSign($param,$signType = "RSA"); //验证签名
    	$result = json_decode($this->fn_get_contents($url,$param,'get'));
    	$alipayTokenResponse = $result->alipay_open_auth_token_app_response;
    	$auth_auth_token = $alipayTokenResponse->app_auth_token;
    	$app_id = $alipayTokenResponse->auth_app_id;
    	var_dump($auth_token.'----'.$app_id);exit;
    }
    
     /**
     * 用户信息授权  --- 最终方法
     * https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7629140.0.0.nboMQG&treeId=216&articleId=105656&docType=1#s4
     */
    public function userAuth(){
    	
    	$url = "https://openapi.alipaydev.com/gateway.do"; //测试参数
    	$app_id = $this->input->get('app_id');
    	$param = array(
    			'app_id' => $app_id,
    			'method' => "alipay.system.oauth.token",
    			'charset' => "utf-8",
    			'sign_type' => "RSA",
    			'timestamp' => date('Y-m-d H:i:s'),
    			'version' => "1.0",
    			'grant_type' => "authorization_code",
    			'code' => $this->input->get('auth_code'),
    	);
    	$param['sign'] = $this->aliLogin->generateSign($param,$signType = "RSA"); //验证签名
    	$result = json_decode($this->fn_get_contents($url,$param,'post'));
    	$alipayTokenResponse = $result->alipay_system_oauth_token_response;
    	$access_token = $alipayTokenResponse->access_token;//交换令牌--用于获取用户信息
    	$user_id = $alipayTokenResponse->user_id; //用户的userId--支付宝用户的唯一userId
    	$this->getAlipayUserInfor($access_token,$app_id);
    }
  
}