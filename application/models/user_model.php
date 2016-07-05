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
        $this->db->where("(`phone`='{$user_name}' OR `email`='{$user_name}')");
        $this->db->where('password', sha1(base64_encode((trim($postData['password'])))));
        return $this->db->get($this->table);
    }
    
    /**
     * 验证用户名
     * @param unknown $userName
     */
    public function validateName($userName)
    {
    	$user_name = trim(addslashes($userName));
    	$this->db->where("(`phone`='{$user_name}' OR `email`='{$user_name}')");
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
    public function insertUser($postData=array())
    {
        $data = array(
            'alias_name'     => $postData['username'],
            'password'       => sha1(base64_encode($postData['password'])),
            'parent_id'      => '0',
            'flag'           => '1',
            'sms'            => '1',
            'created_at'     => date('Y-m-d H:i:s')
        );
        if( $postData['type'] == 1 ){
            $data['phone'] = $postData['username'];
        }
        if( $postData['type'] == 2 ) {
        	$data['email'] = $postData['username'];
        }
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    
    public function modifyPassword($postData=array())
    {
    	$user_name = trim(addslashes($postData['user_name']));
        $data = array(
            'password' => sha1(base64_encode($postData['password'])),
        );
        $this->db->where("(`phone`='{$user_name}' OR `email`='{$user_name}')");
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