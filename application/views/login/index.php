<?php $this->load->view('layout/header');?>
<div class="lgbg">
	<div class="lboby yahei">
		<div class="lha" id="lha">
			<h2 class="on">登陆</h2>
			<h2>注册<em class="h_rt"><i class="r_dop"></i>送10元</em></h2>
	    </div>
		<div class="lzone" id="lzone">
			<div class="lfmo">
				<form class="loginform" id="loginform" method="post" name="formLogin">
					<p>帐号（邮箱/手机号）：</p>
					<input type="text" class="lpt u_zh" autocomplete="off" name="user_name">
					<p class="clear">输入密码：</p>
					<input type="password" autocomplete="off" class="lpt u_mm" name="password">
					<input type="hidden" name="back_url" value="<?php echo isset($backurl) ? $backurl:'';?>"/>
					<input type="submit" class="lgbtn" value="登录" />
					<div class="over mt10">
						<label class="left gray"><input type="checkbox" value="1" name="remember" checked id="remember"/> 下次自动登陆 </label>
						<a href="<?php echo site_url('forget/index');?>" class="right gray">忘记密码</a>
				    </div>
				</form>
				<div class="hid" id="lok">
					<h2 class="yahei f18">登录成功<b id="miao">3</b>秒后自动返回上一页<p>&nbsp;</p></h2>
					<p class="f12">
						<a href="javascript:history.back();" class="xhb">立即返回</a>
						<a href="<?php echo $this->config->ucenter_url;?>" class="ml10 xhb">进入会员中心</a>
					</p>
				</div>
			</div>
			<div class="lfmo hid">
				<form class="loginform" id="regform">
					<div class="rel prc">
						<span class="red tts error-infor hid">已被注册</span>
						<a href="javascript:;" class="yxr blue register-type" flag="1">邮箱注册</a>
						<p id="zlab">手机注册：</p>
						<input type="text"  class="lpt u_zh type-name" name="username" autocomplete="off" class="user-name required" />
					    <input type="hidden" name="type" value="1"/>
					</div>
					<p class="clear">输入密码：</p>
					<input type="password"  autocomplete="off" class="lpt u_mm required" name="password" id="password"/>
					<p class="clear">确认密码：</p>
					<input type="password"  autocomplete="off" class="lpt u_mm required" name="confirm_password"/>
					<input type="hidden" name="back_url" value="<?php echo isset($backurl) ? $backurl:'';?>" />
					<div class="clear"></div>
					<p class="alR lh20 mb10">
					    <a href="<?php echo $this->config->help_url;?>" class="gray" target="_blank">服务条款</a>
					</p>
					<input type="submit" class="lgbtn" value="立即注册" name="submit"/>
				</form>
				<div class="hid lh35" id="rok">
					<h2 class="yahei f18">注册成功</h2>
					<p class="mt10">已赠送<b class="red">10</b>元优惠券，订单满100元即可使用</p>
					<p class="f12">
						<a href="javascript:window.location.href=document.referrer;" class="xhb">立即返回</a>
						<a href="<?php echo $this->config->ucenter_url;?>" class="ml10 xhb">进入会员中心</a>
					</p>
				</div>
			</div>
		</div>
		<div class="login_f">
			<em class="c9">使用合作网站账号登录:</em>
				<a title="使用QQ登录" class="pl10 pr10" href="javascript:;">QQ号登陆</a>
			<em class="vline">|</em>
			<a title="使用新浪微博登录" class="pr10" href="javascript:;">微博帐号登陆</a>
			<p><a href="javascript:window.history.go(-1);" class="c9">返回上一页</a></p>
		</div>
	</div>
</div>
<?php $this->load->view('layout/footer');?>
