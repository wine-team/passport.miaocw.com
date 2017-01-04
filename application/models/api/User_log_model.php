<?php
class User_log_model extends CI_Model
{
    private $table = 'user_log';
    
    public function insert($uid, $ip_from, $operate_type=1, $status=1)
    {
        $data = array(
            'uid'          => $uid,
            'log_time'     => date('Y-m-d H:i:s'),
            'ip_from'      => $ip_from,
            'operate_type' => $operate_type,
            'status'       => $status
        );
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
}