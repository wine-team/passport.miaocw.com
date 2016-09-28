<?php
class User_coupon_get_model extends CI_Model
{
    private $table = 'user_coupon_get';

    public function insert($postData=array())
    {
        $data = array(
            'coupon_set_id' => $postData['coupon_set_id'],
            'coupon_name'   => $postData['coupon_name'],
            'uid'           => $postData['uid'],
            'scope'         => $postData['scope'],
            'related_id'    => $postData['related_id'],
            'amount'        => $postData['amount'],
            'condition'     => !empty($postData['condition']) ? $postData['condition'] : 0,
            'note'          => !empty($postData['note']) ? $postData['note'] : '',
            'start_time'    => $postData['start_time'],
            'end_time'      => $postData['end_time'],
            'status'        => $postData['status'],
            'created_at'    => date('Y-m-d H:i:s'),
        );
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
}