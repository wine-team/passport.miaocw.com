<?php
class Mall_cart_goods_model extends CI_Model{
	
	private $table = 'mall_cart_goods';
	
	 /**
	 * 获取购物车内容
	 * @param unknown $uid
	 */
	public function getCartGoodsByUid($uid){
		
		$this->db->where('uid',$uid);
		$this->db->order_by('creat_at','desc');
		return $this->db->get($this->table);
	}
		
}