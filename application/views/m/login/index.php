<?php $this->load->view('m/layout/header');?>
<div id="top">
    <div class="header">
        <a href="javascript:goback();" class="b_l"></a>
        <h2>登录</h2>
        <a href="<?php echo site_url('m/register')?>" class="b_r">去注册</a>
    </div>
</div>
<div class="pageauto login-reg" id="lbefore">
    <form action="<?php echo site_url('m/loginPost')?>" id="login" class="login">
        <input type="text" name="username" class="linput" id="username" value="" placeholder="邮箱/手机" />
        <input type="password" name="password" id="password" class="linput lpass" value=""  placeholder="登陆密码"/>
        <a href="<?php echo site_url('m/register')?>" class="h left">免费注册（送10元优惠券）</a>
        <a href="<?php echo site_url('m/forget')?>" style="color:#09F" class="right">忘记密码?</a>
        <div class="clear"></div>
        <button type="submit" class="gbtn">登 录</button>
        <p>&nbsp;</p>
        <p class="alC lh30">
            <a href="/connect/login.php?platform=qq" class="lgqq hid">QQ登录</a>
            <a href="/connect/login.php?platform=weibo" class="lgweibo hid">微博登录</a>
        </p>
        <p class="alC"><a href="">网站首页</a> |
            <a class="red" href="app.php">APP下载</a>
        </p>
    </form>
</div>
<?php $this->load->view('m/layout/footer');?>