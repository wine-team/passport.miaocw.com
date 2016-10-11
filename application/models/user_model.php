<?php
class User_model extends CI_Model
{
    private $table = 'user';

    public function findByUid($uid)
    {
        $this->db->where('uid', $uid);
        return $this->db->get($this->table);
    }

    /**
     * 登陆获取
     * @param unknown $postData
     */
    public function login($params=array())
    {
        $userName = trim(addslashes($params['username']));
        if (valid_mobile($userName)) {
            $this->db->where('phone', $userName);
        } else {
            $this->db->where('email', $userName);
        }
        //$this->db->where("(`phone`='{$userName}' OR `email`='{$userName}')");
        $this->db->where('password', sha1(base64_encode(($params['password']))));
        $this->db->limit(1);
        return $this->db->get($this->table);
    }
    
    /**
     * 验证用户名
     * @param unknown $userName
     */
    public function validateName($userName)
    {
        $userName = trim(addslashes($userName));
        if (valid_mobile($userName)) {
            $this->db->where('phone', $userName);
        } else {
            $this->db->where('email', $userName);
        }
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
    public function insert($postData=array(), $parent_id=0)
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
        if (valid_mobile($userName)) {
            $this->db->where('phone', $userName);
        } else {
            $this->db->where('email', $userName);
        }
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
}