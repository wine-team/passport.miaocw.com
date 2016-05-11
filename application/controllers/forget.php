<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Forget extends MJ_Controller
{
    public function _init()
    {
       $this->load->library('encrypt');
    }
    
     /**
     *忘记密码
     */
    public function grid()
    {
        if ($this->frontUser) { //如果已经登录，就跳转到首页。
            $this->redirect($this->config->main_base_url);
        }
        $captcha = $this->getCaptcha(18, 100, 36,4);
        $data['captcha'] = $captcha;
        $this->load->view('forget/grid', $data);
    }
    
    /**
     * 验证码
     */
    public function ajaxJsonCaptcha()
    {
        $captcha = $this->getCaptcha(18, 130, 36, 4);
        echo json_encode($captcha);exit;
    }
    
    /**
     * 验证账户 和 发送短信
     */
    public function validateUser()
    {
        $username = $this->input->post('username');
        if ($this->validateParam($username)) {
            $this->jsonMessage('用户名必填');
        }
        if (strtolower($this->input->post('captcha')) != strtolower(get_cookie('captcha'))) {
            $this->jsonMessage('验证码错误');
        }
        $result = $this->user->validateName($username);
        if ($result->num_rows() <= 0) {
            $this->jsonMessage('用户名不存在');
        }
        $encodename = $this->encrypt->encode($username);
        $this->jsonMessage('', base_url('forget/confirm').'?keycode='.urlencode($encodename));
    }
    
     /**
     * 验证码确认页面
     */
    public function confirm(){

    	$encodename = $this->input->get('keycode');
    	$decodename = $this->encrypt->decode($encodename);
    	$data['username'] = $decodename;
    	$this->load->view('forget/confirm',$data);
    }
    
     /**
      * 更改密码
     */
    public function changePassword(){

    	$data[] = array();
    	$this->load->view('forget/changePassword',$data);
    }
    
    
    
   
}
