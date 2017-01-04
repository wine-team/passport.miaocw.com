<?php 
class Login extends MW_Controller
{
    public function _init()
    {
        $this->load->helper(array('ip','email'));
        $this->load->library(array('encrypt', 'sms/sms'));
        $this->load->library('alipayauth/aliLogin', NULL, 'aliLogin');
        $this->load->model('pc/advert_model', 'advert');
        $this->load->model('pc/user_model', 'user');
        $this->load->model('pc/user_bind_model', 'user_bind');
        $this->load->model('pc/user_log_model', 'user_log');
        $this->load->model('pc/user_coupon_set_model', 'user_coupon_set');
        $this->load->model('pc/user_coupon_get_model', 'user_coupon_get');
        $this->load->model('pc/getpwd_phone_model', 'getpwd_phone');
        $this->load->model('pc/user_invite_code_model', 'user_invite_code');
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
        $this->load->view('pc/login/index', $data);
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
      * access_token  令牌
      * user_id  支付宝用户唯一UID
     */
    public function alipayAuth() {
        
    	$url = "https://openapi.alipay.com/gateway.do"; //测试参数
    	$app_id = $this->input->get('app_id');
    	$backurl = $this->input->get('backurl');
    	$invite_code = $this->input->get('invite_code');
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
    	$this->getAlipayUserInfor($access_token,$app_id,$backurl,$invite_code);
    }
    
     /**
     * 获取用户基础信息(姓别，姓名);
     * @param unknown $auth_token
     * https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7386797.0.0.ETCoNL&treeId=53&articleId=104114&docType=1#s5
     */
    private function getAlipayUserInfor($auth_token,$app_id,$backurl,$invite_code) {
    	
        $url = "https://openapi.alipay.com/gateway.do"; //测试参数
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
        $this->alipayLoginOperate($alipayUserInfor,$backurl,$invite_code);
    }
    
     /**
     * 阿里授权登陆操作和妙处网会员综合处置
     * @param unknown $alipayUserInfor
     */
    private function alipayLoginOperate($alipayUserInfor,$backurl,$invite_code)
    {
    	if (empty($alipayUserInfor)) { //如果授权失败
    		$this->redirect(site_url('pc/register'));
    	}
    	$isAuth = $this->user_bind->getResultByRes(array('other_id'=>$alipayUserInfor->alipay_user_id,'type'=>1),'bind_id,user_id');
    	if ($isAuth->num_rows()>0) { //以前授权过
    		$user_id = $isAuth->row(0)->user_id;
    		$userResult = $this->user->findByUid($user_id);
    		if ($userResult->num_rows()<=0) {
    			$this->redirect(site_url('pc/register'));
    		}
    		$user = $userResult->row(0);
    		$userInfor = array(
    			'uid'       => $user->uid,
    			'aliasName' => !empty($user->alias_name) ? $user->alias_name : $user->phone,
    			'userPhone' => $user->phone,
    			'userEmail' => $user->email,
    			'parentId'  => $user->parent_id,
    			'userPhoto' => $user->photo,
    		);
    		set_cookie('frontUser',base64_encode(serialize($userInfor)), 7200);
    		$this->cache->memcached->save('frontUser', base64_encode(serialize($userInfor)), 7200);
    		$userLog = $this->user_log->insert($user->uid, $ip_from=getIP(), $operate_type=1, $status=1);
    		$url = empty($backurl) ? $this->config->main_base_url : $backurl;
    		$this->redirect($url); // 直接跳转
    		
    	} else {  // 没有授权登陆过
    		if ( !empty($invite_code)) {
    			$parent = $this->user_invite_code->validateInviteCode($invite_code);
    			if ($parent->num_rows() > 0) {
    				$parent_id = $parent->row(0)->uid; 
    			} else {
    				$parent_id = 1;// 妙处网总部
    			}
    		} else {
    			$parent_id = 1;// 妙处网总部
    		}
    		$param = array(
    			'alias_name' => empty($alipayUserInfor->nick_name) ? '妙处网会员' : $alipayUserInfor->nick_name,
    		    'sex' => empty($alipayUserInfor->gender) ? '1' : (($alipayUserInfor->gender==m)? 1 : 2),
    		    'photo' => rand(0, 9).'.jpg',//默认生成一张0-9的jpg图片
    		);
    		$this->db->trans_start();
    		$userId = $this->user->authInsert($param, $parent_id);
    		$bindId = $this->user_bind->insert($alipayUserInfor->alipay_user_id,$userId,$type=1);
    		$inviteCode = $this->user_invite_code->insert(array('uid'=>$userId)); //自动生成唯一邀请码
    		$getCoupon = $this->getCoupon($coupon_set_id = 1, $userId);
    		$userLog = $this->user_log->insert($userId, $ip_from=getIP(), $operate_type=1, $status=1);
    		$this->db->trans_complete();
    		if ($this->db->trans_status() === FALSE) {
    			$this->redirect(site_url('pc/register'));
    		}
    		$userInfor = array(
                'uid'       => $userId,
                'aliasName' => $param['alias_name'],
                'userPhone' => '',
                'userEmail' => '',
                'parentId'  => $parent_id,
                'userPhoto' => $param['photo'],
    		);
    		set_cookie('frontUser', base64_encode(serialize($userInfor)), 7200);
    		$this->cache->memcached->save('frontUser', base64_encode(serialize($userInfor)), 7200);
    		$url = empty($backurl) ? $this->config->main_base_url : $backurl;
    		$this->redirect($url); // 直接跳转
    	}
    }
    
    
    /**
     * 获取优惠劵
     * @param unknown $coupon_set_id
     */
    private function getCoupon($coupon_set_id, $uid)
    {
    	$couponRes = $this->user_coupon_set->findByCouponSetId($coupon_set_id);
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
    		$res = $this->user_coupon_set->setCouponNum($coupon_set_id, $num=1);
    	}
    	return $status;
    }
    
    
    
     /**
      * 第三方应用授权(暂时不启用) --请保留--kxx
      * https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7386797.0.0.UiAPWO&treeId=216&articleId=105193&docType=1#s10
     */ 
    public function thirdAuth()
    {
    	$this->load->library('alipayauth/aliLogin', NULL, 'aliLogin');
    	$url = "https://openapi.alipaydev.com/gateway.do"; //测试url参数
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
    }
}