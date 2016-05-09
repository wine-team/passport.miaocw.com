<?php 
class Login extends MJ_Controller
{
    public function _init()
    {
        $this->load->helper(array('email'));
        $this->load->library(array('encrypt', 'sms/sms'));
        $this->load->model('advert_model', 'advert');
        $this->load->model('user_model', 'user');
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
      
        $res = $this->advert->findBySourceState(2)->row_array();
        $data['login_bg'] = isset($res['picture']) ? $this->config->show_image_url('advert', $res['picture']) : 'passport/images/login-bg.jpg';
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
        }else{
            $user = $this->user->quick_login($d);
        }
        
        $this->user->visitCount($user->uid); //统计用户登录次数。
        $userType = $this->usertype($user->user_type);
        $session = array(
            'ACT_UID'      => $user->uid,
            'ACT_UTID'     => $user->user_type,
            'ACT_TYPENAME' => urlencode($userType['type_zh']),
            'ACT_TYPE'     => $userType['type_en'],
            'ACT_EXTRA'    => $user->extra,
            'ALIAS_NAME'   => urlencode($user->alias_name),
            'OWNER_ID'     => $user->uid,
            'OWNER_NAME'   => $user->user_name,
            'PARENT_ID'    => $user->parent_id,
        );
        set_cookie('frontUser', serialize($session), 86400);
        $this->memcache->setData('frontUser', serialize($session));
        if (($user->user_type & UTID_PROVIDER) || ($user->user_type & UTID_TELLER)) {
            $directUrl = $this->config->gongying_url;
        } else if ($this->input->post('backurl')) {
            $directUrl = $this->input->post('backurl');
        } else {
            $directUrl = $this->config->main_base_url;
        }
        echo json_encode(array(
            'status'  => true,
            'messages' => $directUrl
        ));exit;
    }
    
    /**
     * 退出登陆
     */
    public function logout()
    {
        if (get_cookie('frontUser')) {
            delete_cookie('frontUser');
        }
        if (get_cookie('bz_session')) {
            delete_cookie('bz_session');
        }
        $this->memcache->deleteMemcache('frontUser');
        $this->redirect($this->config->main_base_url);
    }
    
    /**
     * 同步手机帐号
     * @param 每页条数 $page_num
     * @param 第几条开始 $num
     */
    public function sync($page_num, $num)
    {
        $total = $this->user->total();
        $result = $this->user->page_list($page_num, $num);
        if ($result->num_rows()) {
            foreach ($result->result() as $item) {
                if (valid_mobile($item->cellphone)) {
                    $this->user->updateUser($item->uid, $item->cellphone);
                }
            }
            echo '信息同步完成，还剩余'.($total-$page_num).'条。';exit;
        } else {
            echo '无信息可同步';exit;
        }
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