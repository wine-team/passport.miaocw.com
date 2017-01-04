<?php
class User_bind_model extends CI_Model
{
    private $table = 'user_bind';
    
     /**
     * 获取绑定数据
     * @param unknown $param
     * @param string $f
     */
    public function getResultByRes($param,$f='*'){
    	
    	$this->db->select($f);
    	$this->db->from($this->table);
    	if (!empty($param['other_id'])){
    	   $this->db->where('other_id',$param['other_id']);
    	}
    	if (!empty($param['type'])){ // 支付类型
    		$this->db->where('type',$param['type']);
    	}
    	return $this->db->get();
    }
    
     /**
     * 插入授权来源
     * @param unknown $other_id
     * @param unknown $user_id
     * @param unknown $type
     */
    public function insert($other_id,$user_id,$type) {
    	
    	$data = array(
    		'other_id' => $other_id,
    		'user_id' => $user_id,
    		'type' => $type,
    		'creat_at' => date('Y-m-d H:i:s')
    	);
    	$this->db->insert($this->table,$data);
    	return $this->db->insert_id();
    }
}