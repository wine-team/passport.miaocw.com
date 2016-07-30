<?php
class User_model extends CI_Model
{
    private $table = 'user';
    private $table_2 = 'getpwd_phone';
    /**
     * 登陆获取
     * @param unknown $postData
     */
    public function login($postData)
    {
        $username = trim(addslashes($postData['username']));
        $this->db->where("(`phone`='{$username}' OR `email`='{$username}')");
        $this->db->where('password', sha1(base64_encode((trim($postData['password'])))));
        return $this->db->get($this->table);
    }
    
    /**
     * 快速登录验证
     * cyl
     */
    public function quick_login($data=array())
    {
    	if (empty($data['mobile_phone'])) {
    		echo json_encode(array(
    				'status'  => false,
    				'messages' => '请输入手机号码'
    		));exit;
    	}
    	if (empty($data['verify'])) {
    		echo json_encode(array(
    				'status'  => false,
    				'messages' => '请输入动态密码'
    		));exit;
    	}
    	$res = $this->db->select(array('uid', 'alias_name','flag'))
    	->where(array('phone' => $data['mobile_phone']))
    	->get($this->table)
    	->row(0);
    	$_res = $this->db->select('id,addtime,failtime')
    	->where(array('username' => $data['mobile_phone'], 'code' => md5($data['verify'])))
    	->get($this->table_2)
    	->row_array(0);
    	if (!$res || !$_res) {
    		echo json_encode(array(
    				'status'  => false,
    				'messages' => '手机号码有误或者动态密码无效'
    		));exit;
    	}
    	if ( !((time()>=strtotime($_res['addtime'])) && (time()<=strtotime($_res['failtime']))) ){
    		echo json_encode(array(
    				'status'  => false,
    				'messages' => '动态密码失效，请重新获取'
    		));exit;
    	}
    	if($res->flag == 2) {
    		echo json_encode(array(
    				'status'  => false,
    				'messages' => '此帐号已被冻结，请与管理员联系'
    		));exit;
    	}
    	return $res;
    }
    
    public  function visitCount($uid)
    {
    	$this->db->set('login_count', 'login_count+1', false);
    	$this->db->where('uid', $uid);
    	return $this->db->update($this->table);
    }
    
    /**
     * 验证用户名
     * @param unknown $userName
     */
    public function validateName($userName)
    {
    	$userName = trim(addslashes($userName));
    	$this->db->where("(`phone`='{$userName}' OR `email`='{$userName}')");
    	return $this->db->get($this->table);
    }
    
    /**
     * 验证手机号码
     * @param unknown $userName
     */
    public function validateMobilePhone($mobilePhone)
    {
    	$this->db->where('mobile_phone', $mobilePhone);
    	return $this->db->get($this->table);
    }
    
    /**
     * 注册时保存数据
     * @param unknown $postData
     * @param string $parent_id
     */
    public function insertUser($postData=array(), $parent_id=UTID_BEIZHU)
    {
    	$data = array(
    			'user_name'      => $postData['username'],
    			'alias_name'     => $postData['username'],
    			'mobile_phone'   => $postData['mobile_phone'],
    			'user_type'      => UTID_CUSTOMER,
    			'parent_id'      => $parent_id,
    			'owner_id'       => $parent_id,
    			'pw'             => md5($postData['password']),
    			'sms'            => 0,
    			'login_count'    => 1,
    	);
    	if (!empty($postData['alias_name'])) {
    		$data['alias_name'] = $postData['alias_name'];
    	}
    	if (!empty($postData['user_type'])) {
    		$data['user_type'] = $postData['user_type'];
    	}
    	if (!empty($postData['owner_id'])) {
    		$data['owner_id'] = $postData['owner_id'];
    	}
    	if (!empty($postData['personal_photo'])) {
    		$data['personal_photo'] = $postData['personal_photo'];
    	}
    	if (isset($postData['extra'])) {
    		$data['extra'] = $postData['extra'];
    	}
    	if (isset($postData['sms'])) {
    		$data['sms'] = $postData['sms'];
    	}
    	if (isset($postData['key'])) {
    		$data['key'] = $postData['key'];
    	}
    	$this->db->insert($this->table, $data);
    	return $this->db->insert_id();
    }
    
    public function modifyPassword($postData=array())
    {
    	$userName = trim(addslashes($postData['username']));
    	$data = array(
    			'password' => sha1(base64_encode($postData['password'])),
    	);
    	$this->db->where("(`phone`='{$userName}' OR `email`='{$userName}')");
    	return $this->db->update($this->table, $data);
    }
    
    public function total()
    {
    	$this->db->from($this->table.' AS user');
    	$this->db->join('user_detail AS user_detail', 'user.uid = user_detail.uid');
    	$this->db->where('user.mobile_phone IS NULL');
    	$this->db->where('user_detail.cellphone !=', '');
    	return $this->db->count_all_results();
    }
    
    public function page_list($page_num, $num)
    {
    	$this->db->select('user.uid, user.user_name, user_detail.cellphone');
    	$this->db->from($this->table.' AS user');
    	$this->db->join('user_detail AS user_detail', 'user.uid = user_detail.uid');
    	$this->db->where('user.mobile_phone IS NULL');
    	$this->db->where('user_detail.cellphone !=', '');
    	$this->db->limit($page_num, $num);
    	$this->db->order_by('user.uid', 'ASC');
    	return $this->db->get();
    }
    
    public function updateUser($uid, $cellphone)
    {
    	$data = array(
    			'mobile_phone' => $cellphone,
    	);
    	$this->db->where('uid', $uid);
    	$this->db->update($this->table, $data);
    }
    
    /**
     * 验证用户名称
     * @param unknown $user_name
     */
    public function validateExistUser($user_name)
    {
    	$this->db->where('user_name', $user_name);
    	$this->db->where('user_type &'.UTID_SELLER.'=', UTID_SELLER);
    	return $this->db->count_all_results($this->table);
    }
    
    //验证用户名是否已存在
    public function findByUserName($postData)
    {
    	if (!empty($postData['user_name'])) {
    		$this->db->where('user_name', $postData['user_name']);
    		if (!empty($postData['alias_name'])) {
    			$this->db->or_where('alias_name', $postData['alias_name']);
    		}
    	}
    	return $this->db->get($this->table);
    }
    
    /**
     * 发现用户ID
     * @param unknown $uid
     */
    public function findByUid($uid)
    {
    	$this->db->where('uid', $uid);
    	return $this->db->get($this->table);
    }
}