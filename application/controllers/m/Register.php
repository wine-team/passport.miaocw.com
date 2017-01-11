<?php 
class Register extends MW_Controller
{
    private $d;

    public function _init()
    {
        $this->d = $this->input->post();
        $this->load->helper(array('ip', 'email', 'common'));
        $this->load->model('m/user_model', 'user');
        $this->load->model('m/user_log_model','user_log');
        $this->load->model('m/user_coupon_set_model','user_coupon_set');
        $this->load->model('m/user_coupon_get_model','user_coupon_get');
        $this->load->model('m/user_invite_code_model','user_invite_code');
    }

    /**
     *注册页面
     */
    public function index()
    {
        if ($this->frontUser) {
            $this->redirect($this->config->main_base_url);
        }
        $inviteCode = $this->input->get('invite_code');
        if (!empty($inviteCode)) {
            $data['invite_code'] = $inviteCode;
        } else {
            $data['invite_code'] = get_cookie('invite_code');
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
        $this->load->view('m/register/index', $data);
    }

    /**
     *注册提交页面
     */
    public function doRegister()
    {
        $phone = $this->input->post('phone', TRUE);
        if (empty($phone)) {
            $this->jsonMessage('请输入正确的手机号');
        }
        if (!valid_mobile($phone)) {
            $this->jsonMessage('手机号码格式有误');
        }
        if (strlen($this->d['password']) < 6) {
            $this->jsonMessage('密码长度不能小于6位');
        }
        $result = $this->user->validatePhone($phone);
        if ($result->num_rows() > 0) {
            $this->jsonMessage('该用户名已经存在');
        }
        if (!empty($this->d['invite_code'])) {
            $parent = $this->user_invite_code->validateInviteCode($this->d['invite_code']);
            if ($parent->num_rows() > 0) {
                $parent_id = $parent->row(0)->uid;
            } else {
                $this->jsonMessage('您传入了无效的参数');
            }
        } else {
            $parent_id = 1;// 妙处网总部
        }
        $this->d['photo'] = rand(0, 9).'.jpg'; //默认生成一张0-9的jpg图片
        $this->db->trans_start();
        $userId = $this->user->insert($this->d, $parent_id);
        $inviteCode = $this->user_invite_code->insert(array('uid'=>$userId)); //自动生成唯一邀请码
        $getCoupon = $this->getCoupon($coupon_set_id = 1, $userId);
        $userLog = $this->user_log->insert($userId, $ip_from=getIP(), $operate_type=1, $status=1);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->jsonMessage('服务器忙，请稍候再试');
        }
        $userInfor = array(
            'uid'       => $userId,
            'aliasName' => $phone,
            'userPhone' => $phone,
            'userEmail' => '',
            'parentId'  => $parent_id,
            'userPhoto' => $this->d['photo'],
        );
        set_cookie('frontUser', base64_encode(serialize($userInfor)), 7200);
        $this->cache->memcached->save('frontUser', base64_encode(serialize($userInfor)), 7200);
        $backurl = $this->input->post('backurl') ? urldecode($this->input->post('backurl')) : $this->config->passport_url.'m/register/regsuccess.html';
        $this->jsonMessage('', $backurl);
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
}