<?php $this->load->view('pc/layout/header');?>

<div class="bz-login">
	<div class="login-img">
       <img src="<?php echo $login_bg?>"/>
	</div>
	<div class="login-con">
		<div class="login">
			<form class="normal-login login-form-validate" action="<?php echo base_url('pc/login/loginPost');?>" method="post" >
			    <input type="hidden" name="backurl" value="<?php echo $backurl;?>">
			    <input class="d-quick" type="hidden" name="act" value="1"/>
				<ul>
					<li class="title">会员</li>
					<li>
						<div class="remind">
							<i class="iconfont">&#xe600;</i>
							<p>公共场所不建议自动登录，以防账号丢失</p>
						</div>
					</li>
					<li class="clearfix">
						<div class="init-login">
							<input type="text" name="username" id="username" class="input-text required"  placeholder="会员名/手机号">
							<i class="iconfont photo-icon">&#xe603;</i>
						</div>
					</li>
					<li>
						<div class="login-pwd">
							<input type="password" name="password" id="password" class="input-text required"  placeholder="密码">
							<i class="iconfont photo-icon">&#xe602;</i>
						</div>
					</li>
					<li>
						<div class="remember-pwd clearfix">
							<label>
								<input type="checkbox" name="auto_login" checked/>自动登录
							</label>
							<a href="<?php echo site_url('pc/forget')?>" class="forget">忘记密码?</a>
						</div>
					</li>
					<li>
						<div class="erro-code forget-form-account" style="<?php echo $err_count >= 3 ? 'display:block;' : 'display:none;'?>">
							<input type="text" name="captcha" placeholder="验证码" class="d-captcha"/>
							<a href="javascript:;" id="ajaxJsonCaptcha" ><?php echo $captcha['image']; ?></a>
						</div>
					</li>
					<li><button type="submit" class="login-submit d-login">登 录</button></li>
					<li><div class="bz-register">还没有账号，<a href="<?php echo site_url('pc/register')?>">立即注册</a></div></li>
				</ul>
				<div class="hd">
					<img src="passport/pc/images/fast-login.png" />
				</div>
			</form>
			<form class="quick-login" action="">
			    <input type="hidden" name="backurl" value="<?php echo $backurl;?>">
			    <input class="e-quick" type="hidden" name="act" value="2"/>
				<ul>
					<li class="title">手机动态密码登录</li>
					<li>
						<div class="remind">
							<i class="iconfont">&#xe600;</i>
							<p>公共场所不建议自动登录，以防账号丢失</p>
						</div>
					</li>
					<li class="clearfix">
						<div class="init-login">
							<input class="input-text required" id="mobile_phone" name="mobile_phone" type="text" placeholder="手机" />
							<i class="iconfont photo-icon">&#xe602;</i>
						</div>
					</li>
					<li>
					   <div class="fast-login">
							<div class="input-box">
								<input class="input-text e-captcha required" id="captcha" name="captcha" type="text" placeholder="验证码" />
							</div>
							<a href="javascript:;" class="ajaxJsonCaptcha" ><?php echo $captcha['image']; ?></a>
						</div>
						<div class="fast-code clearfix">
							<div class="pull-left code-w">
								<input class="input-text required " id="verify" type="text" name="verify" placeholder="请输入动态密码" />
							</div>
							<button type="button" class="getpwd btnsend pull-left">获取动态密码</button>
						</div>
					</li>
					<li>
						<div class="remember-pwd clearfix">
							<label>
								<input type="checkbox" checked/>自动登录
							</label>
							<a href="<?php echo site_url('pc/forget')?>">忘记密码?</a>
						</div>
					</li>
					<li><button type="submit" class="login-submit e-login">登 录</button></li>
					<li><div class="bz-register">还没有账号，<a href="<?php echo site_url('pc/register')?>">立即注册</a></div></li>
				</ul>
				<div class="hd">
					<img src="passport/pc/images/members-login.png" />
				</div>
			</form>
		</div>
	</div>
</div>
<?php $this->load->view('pc/layout/footer');?>
