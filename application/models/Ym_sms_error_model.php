<?php
class Ym_sms_error_model extends CI_Model
{
    private $table = 'ym_sms_error';

    public function insertYmSmsError($telephone, $content, $error_id=0, $sms_type=1)
    {
        $data = array(
            'mobile'   => $telephone,
            'content'  => $content,
            'error_id' => $error_id,
            'sms_type' => $sms_type,
            'sendtime' => date('Y-m-d H:i:s', time()),
        );
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
}
