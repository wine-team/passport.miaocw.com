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
        $user_name = trim(addslashes($postData['user_name']));
        $this->db->where("(`user_name`='{$user_name}' OR `phone`='{$user_name}')");
        $this->db->where('password', sha1(base64_encode((trim($postData['password'])))));
        return $this->db->get($this->table);
    }
    

    /**
     * 验证用户名
     * @param unknown $userName
     */
    public function validateName($userName)
    {
        $this->db->where('user_name', $userName);
        return $this->db->get($this->table);
    }
    
    /**
     * 验证手机号码
     * @param unknown $userName
     */
    public function validateMobilePhone($phone)
    {
        $this->db->where('phone', $phone);
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
        );
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    
    public function modifyPassword($postData=array())
    {
        $data = array(
            'password' => sha1($postData['password']),
        );
        $this->db->where('user_name', $postData['user_name']);
        return $this->db->update($this->table, $data);
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