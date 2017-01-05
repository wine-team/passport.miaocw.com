<?php
class Welcome extends MW_Controller
{
    public function _init()
    {
        $this->load->library('user_agent');
    }
    
    public function index()
    {
        if ($this->agent->is_mobile()) {
            redirect($this->config->passport_url.'m/login/index.html');
        } else {
            redirect($this->config->passport_url.'pc/login/index.html');
        }
    }
}