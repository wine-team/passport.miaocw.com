<?php
class User_invite_code_model extends CI_Model
{
    private $table = 'user_invite_code';

    /**
     * 邀请码
     * @param $inviteCode
     * @return mixed
     */
    public function validateInviteCode($inviteCode)
    {
        $this->db->where('invite_code', $inviteCode);
        return $this->db->get($this->table);
    }

    public function insert($params=array())
    {
        $data = array(
            'uid'         => $params['uid'],
            'create_time' => date('Y-m-d H:i:s')
        );
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
}