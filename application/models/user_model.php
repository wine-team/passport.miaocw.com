<?php
class User_model extends CI_Model
{
    private $table = 'user';
    private $table_2 = 'getpwd_phone';

    public function findByUid($uid)
    {
        $this->db->where('uid', $uid);
        return $this->db->get($this->table);
    }

    /**
     * 登陆获取
     * @param unknown $postData
     */
    public function login($postData)
    {
        $username = trim(addslashes($postData['username']));
        $this->db->where("(`phone`='{$username}' OR `email`='{$username}')");
        $this->db->where('password', sha1(base64_encode(($postData['password']))));
        return $this->db->get($this->table);
    }
    
    /**
     * 快速登录验证
     * cyl
     */
    public function quick_login($data=array())
    {
         if (empty($data['phone'])) {
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
                ->where(array('phone' => $data['phone']))
        		->get($this->table)
        		->row(0);
        $_res = $this->db->select('id, addtime, failtime')
        		->where(array('username' => $data['phone'], 'code' => md5($data['verify'])))
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
    public function validatePhone($phone)
    {
        $this->db->where('phone', $phone);
        return $this->db->get($this->table);
    }
    
    /**
     * 注册时保存数据
     * @param unknown $postData
     * @param string $parent_id
     */
    public function insertUser($postData=array(), $parent_id=0)
    {
        $data = array(
            'alias_name'     => $postData['phone'],
            'phone'          => $postData['phone'],
            'password'       => sha1(base64_encode($postData['password'])),
            'sex'            => 1,
            'birthday'       => date('Y-m-d H:i:s'),
            'user_money'     => 0,
            'frozen_money'   => 0,
            'pay_points'     => 0,
            'flag'           => 1,
            'sms'            => 1,
            'parent_id'      => $parent_id,
            'photo'          => rand(0, 9).'.jpg',
            'created_at'     => date('Y-m-d H:i:s')
        );
        if (!empty($postData['email'])) {
            $data['email'] = $postData['email'];
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
    
    public function updateUser($uid, $cellphone)
    {
        $data = array(
            'phone' => $cellphone,
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
}