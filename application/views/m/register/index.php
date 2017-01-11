<?php $this->load->view('m/layout/header');?>
<div id="top">
    <div class="header">
        <a href="javascript:goback('<?php echo site_url('m/login/index')?>');" class="b_l"></a>
        <h2>注册</h2>
        <a href="<?php echo site_url('m/login/index')?>" class="b_r">去登陆</a>
    </div>
</div>
<div class="pageauto login-reg">
    <form class="register">
        <input type="hidden" name="backurl" value="<?php echo $backurl;?>" />
        <input type="hidden" name="invite_code" value="<?php echo $invite_code;?>" />
        <input type="text" name="phone" class="linput" placeholder="请输入手机号码" />
        <input type="password" name="password" class="linput lpass" placeholder="登陆密码至少6位"/>
        <p class="lh20">&nbsp;</p>
        <button type="submit" class="gbtn">注 册</button>
        <p>&nbsp;</p>
        <p class="alC lh30">
            <a href="<?php echo site_url('m/login/index')?>" class="f14 h">已有帐号？点击登录</a>
        </p>
        <p class="alC"><a href="">网站首页</a> | <a class="red" href="app.php">APP下载</a></p>
    </form>
</div>
