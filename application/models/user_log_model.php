<?php
class User_log_model extends CI_Model
{
    private $table = 'user_log';
    
    public function insert($params)
    {
        return $this->db->insert($this->table, $params);
    }
}