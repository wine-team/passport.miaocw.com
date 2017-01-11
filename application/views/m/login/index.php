<?php $this->load->view('m/layout/header');?>
<div id="top">
    <div class="header">
        <a href="javascript:goback('<?php echo site_url('m/register')?>');" class="b_l"></a>
        <h2>登录</h2>
        <a href="<?php echo site_url('m/register')?>" class="b_r">去注册</a>
    </div>
</div>
<div class="pageauto login-reg" id="lbefore">
    <form class="login">
        <input type="hidden" name="act" value="1" />
        <input type="hidden" name="backurl" value="<?php echo $backurl;?>" />
        <input type="text" name="username" id="username" class="linput" placeholder="手机/邮箱" />
        <input type="password" name="password" id="password" class="linput lpass" placeholder="登陆密码"/>
        <a href="<?php echo site_url('m/register')?>" class="h left">免费注册（送8元优惠券）</a>
        <a href="<?php echo site_url('m/forget')?>" style="color:#09F" class="right">忘记密码?</a>
        <div class="clear"></div>
        <button type="submit" class="gbtn">登 录</button>
        <p>&nbsp;</p>
        <p class="alC lh30">
            <a href="/connect/login.php?platform=qq" class="lgqq hid">QQ登录</a>
            <a href="/connect/login.php?platform=weibo" class="lgweibo hid">微博登录</a>
        </p>
        <p class="alC">
            <a href="<?php echo $this->config->main_base_url; ?>">网站首页</a> | <a class="red" href="app.php">APP下载</a>
        </p>
    </form>
</div>