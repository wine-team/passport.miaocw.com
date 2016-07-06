<?php
class Advert_model extends CI_Model
{
    private $table = 'advert';
    public function findBySourceState($source_state)
    {
        $this->db->where('source_state', $source_state);
        return $this->db->get($this->table);
    }
}