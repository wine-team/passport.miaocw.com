<?php
class Welcome extends MW_Controller
{
    public function index()
    {
        redirect($this->config->passport_url.'pc/login/index.html');
    }
}