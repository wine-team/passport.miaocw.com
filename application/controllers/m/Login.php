<?php
class Login extends MW_Controller {

	public function _init() {

		$this->d = $this->input->post();
		$this->load->model('user_model','user');
		$this->load->model('user_coupon_set_model','user_coupon_set');
		$this->load->model('user_coupon_get_model','user_coupon_get');
		$this->load->model('user_invite_code_model','user_invite_code');
		$this->load->model('user_log_model','user_log');
	}

	 /**
	 * 移动端登陆
	 */
	public function grid() {

		if (empty($this->d['username'])) {
			$this->jsonMessage('请输入用户名');
		}
		if (empty($this->d['password'])) {
			$this->jsonMessage('请输入密码');
		}
		$result = $this->user->login($this->d,'uid,alias_name,phone,email,parent_id,photo');
		if ($result->num_rows() <=0) {
			$this->jsonMessage('用户名或密码错误');
		}
		$user = $result->row();
		if ($user->flag == 2) {
			$this->jsonMessage('此帐号已被冻结');
		}
		$userInfor = array(
			'uid'       => $user->uid,
			'aliasName' => !empty($user->alias_name) ? $user->alias_name : $user->phone,
			'userPhone' => $user->phone,
			'userEmail' => $user->email,
			'parentId'  => $user->parent_id,
			'userPhoto' => $user->photo,
		);
		$this->jsonMessage('',$userInfor);
	}
	
	 /**
	 * 注册
	 */
	public function reg() {
		
		$phone = $this->input->post('phone', TRUE);
		if (empty($phone)) {
			$this->jsonMessage('请输入手机号码');
		}
		if (empty($this->d['password'])) {
			$this->jsonMessage('请输入密码');
		}
		if (empty($this->d['confirm_password'])) {
			$this->jsonMessage('请输入确认密码');
		}
		if (strlen($this->d['password']) < 6 || strlen($this->d['confirm_password']) < 6) {
			$this->jsonMessage('密码长度不小于6位');
		}
		if (!valid_mobile($phone) ) {
			$this->jsonMessage('手机号码格式有误');
		}
		if ($this->d['password'] != $this->d['confirm_password']) {
			$this->jsonMessage('密码输入不一致');
		}
		$result = $this->user->validatePhone($phone);
		if ($result->num_rows() > 0) {
			$this->jsonMessage('该用户名已经存在');
		}
		if (isset($this->d['invite_code'])) {
			$parent = $this->user_invite_code->validateInviteCode($this->d['invite_code']);
			if ($parent->num_rows() > 0) {
				$parent_id = $parent->row(0)->uid;
			} else {
				$this->jsonMessage('邀请码无效');
			}
		} else {
			$parent_id = 1; //妙处网总部
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
		$this->jsonMessage('', $userInfor);
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