<?php $this->load->view('m/layout/header');?>
<div id="top">
    <div class="header">
        <a href="javascript:goback();" class="b_l"></a>
        <h2>注册</h2>
        <a href="<?php echo site_url('m/login/index')?>" class="b_r">去登陆</a>
    </div>
</div>
<div class="pageauto login-reg">
    <form action="reg.php" method="post" class="regist" id="regist">
        <input type="text" name="username" class="linput" id="username" value="" placeholder="手机号码或者邮箱" />
        <input type="password" name="password" id="password" class="linput lpass" value=""  placeholder="登陆密码至少5位"/>
        <p class="lh20">&nbsp;</p>
        <button type="submit" class="gbtn">注 册</button>
        <p class="alC">
            <a href="<?php echo site_url('m/login/index')?>" class="f14 h">已有帐号？点击登录</a>
        </p>
        <p class="alC"><a href="">网站首页</a> | <a class="red" href="app.php">APP下载</a></p>
    </form>
</div>
<?php $this->load->view('m/layout/footer');?>
