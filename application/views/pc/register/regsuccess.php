<?php $this->load->view('pc/layout/header');?>
<div class="success-register">
    <div class="success-text">
        <p class="suc-your">恭喜您，您已经成功注册妙处网会员！</p>
        <p class="suc-zhang">您在妙处网的登录账号为<i><?php echo $username;?></i>请妥善保管您的账号资料。</p>
        <div class="success-button clearfix">
            <span>现在您可以：</span>
            <a href="<?php echo $this->config->main_base_url;?>" class="btn btn-success">立即购物</a>
            <a href="<?php echo $this->config->ucenter_url.'customer/personal.html';?>" class="btn btn-primary">完成资料</a>
        </div>
    </div>
    <div class="success-text-footer">
        <div class="text-footer clearfix">
            <img src="passport/pc/images/weixin.png">
            <p>扫一扫，关注妙处网微信公众号</p>
        </div>
    </div>
</div>
<?php $this->load->view('pc/layout/footer');?>