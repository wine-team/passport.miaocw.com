<?php
class Getpwd_phone_model extends CI_Model
{
    private $table = 'getpwd_phone';
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function insertGetpwdPhone($postData=array())
    {
        $data = array(
            'username' => $postData['mobile_phone'],
            'phone'    => $postData['mobile_phone'],
            'code'     => md5($postData['code']),
            'addtime'  => date('Y-m-d H:i:s'),
            'failtime' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
            'flag'     => 0,
        );
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    
    public function validateName($postData=array(), $failtime=false)
    {
        $this->db->where('username', $postData['mobile_phone']);
        if (!empty($postData['verify'])) {
            $this->db->where('code', md5($postData['verify']));
        }
        if ($failtime) { //validate js验证时和注册需要验证是否过期
            $this->db->where('addtime <', date('Y-m-d H:i:s'));
            $this->db->where('failtime >=', date('Y-m-d H:i:s'));
        }
        return $this->db->get($this->table);
    }
    
    public function updateGetpwdPhone($postData=array())
    {
        $data = array(
            'code'     => md5($postData['code']),
            'addtime'  => date('Y-m-d H:i:s'),
            'failtime' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
            'flag'     => 0,
        );
        $this->db->where('username', $postData['mobile_phone']);
        return $this->db->update($this->table, $data);
    }
}