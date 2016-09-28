<?php
class User_coupon_set_model extends CI_Model
{
    private $table = 'user_coupon_set';

    public function findById($coupon_set_id)
    {
        $this->db->where('coupon_set_id', $coupon_set_id);
        return $this->db->get($this->table);
    }
    
    /**
     * 
     * @param unknown $coupon_set_id
     */
    public function setCouponNum($coupon_set_id,$num) {
    	
    	$this->db->set('number','number-'.$num,false);
        $this->db->where('coupon_set_id',$coupon_set_id);
        return $this->db->update($this->table);
    }
}