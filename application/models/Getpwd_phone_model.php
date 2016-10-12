<?php
class Getpwd_phone_model extends CI_Model
{
    private $table = 'getpwd_phone';
    
    public function insert($postData=array())
    {
        $data = array(
            'phone'    => $postData['phone'],
            'code'     => md5($postData['code']),
            'addtime'  => date('Y-m-d H:i:s'),
            'failtime' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
            'flag'     => 0,
        );
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    
    public function update($postData=array())
    {
        $data = array(
            'code'     => md5($postData['code']),
            'addtime'  => date('Y-m-d H:i:s'),
            'failtime' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
            'flag'     => 0,
        );
        $this->db->where('phone', $postData['phone']);
        return $this->db->update($this->table, $data);
    }

    public function validatePhone($postData=array(), $failtime=false)
    {
        $this->db->where('phone', $postData['phone']);
        if (!empty($postData['verify'])) {
            $this->db->where('code', md5($postData['verify']));
        }
        if ($failtime) { //validate js验证时和注册需要验证是否过期
            $this->db->where('addtime <', date('Y-m-d H:i:s'));
            $this->db->where('failtime >=', date('Y-m-d H:i:s'));
        }
        return $this->db->get($this->table);
    }
}