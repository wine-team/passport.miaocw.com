<?php $this->load->view('m/layout/header');?>
<div class="pageauto login-reg">
    <div class="lh30 pd10">
        <img src="m/images/ok.png" width="23" height="23" class="left mt5 pr10">
        <h2 class="f18 left">恭喜，注册成功!</h2>
        <div class="clear"></div>
        <p>已赠8元优惠劵（<a href="bonus.php" class="U">查看</a>），购满100元即可使用哟</p>
        <a href="hot.html" class="red">去看看热销宝贝 >></a>
        <p class="lh16">&nbsp;</p>
        <div class="lh25 f14 hid">
            <p>请牢记您的登录名和密码</p>
            <p class="red">您的登录名：<b id="u_name"></b>,登录密码：<b id="u_pass"></b></p>
        </div>
        <div style="background-color:#fff;border:1px solid #ddd;border-radius:4px;padding:10px 20px;">
            <p>关注微信：quwang520</p><p>购物更私密快捷</p>
            <p>了解更多情趣资讯</p>
        </div>
        <p class="lh16">&nbsp;</p>
        <a href="javascript:goback();" class="red U pr10">返回继续购物</a>
        <a href="user.php" class="U red pl10">进入会员中心</a>
    </div>
</div>
<?php $this->load->view('m/layout/footer');?>