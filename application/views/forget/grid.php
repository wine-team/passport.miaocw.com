<?php $this->load->view('layout/header');?>
<div class="lgbg">
	<div class="w" style="padding-top:120px;">
		<form action="user.php" method="post" name="getPassword" onsubmit="return  submitPwdInfo();">
			<table border="0" align="center" class="lh35">
			      <tr>
			            <td colspan="2" align="center" class="red"><h2 class="yahei f18">请输入您的注册邮箱或注册手机号找回密码</h2><p class="lh35">&nbsp;</p></td>
			      </tr>
			      <tr>
			            <td  align="right" valign="middle">邮箱/手机号：&nbsp;&nbsp;</td>
			            <td  class="f14 pb10"><input name="account" id="account" type="text" size="30" class="ipt left" onclick="this.value='';" /></td>
			      </tr>
			      <tr>
			            <td align="right">验证码：&nbsp;&nbsp;</td>
			            <td class="f14 pb10">
			            	<input name="captcha" id="captcha" type="text" maxlength="4" size="25" class="ipt left" onclick="this.value='';"/>
			            </td>
			      </tr>
			      <tr>
			            <td align="right"></td>
			            <td class="f14 pb10">
			              <img src="captcha.php?get_password=1" alt="captcha" style="vertical-align: middle;cursor: pointer;" onClick="this.src='captcha.php?get_password=1&'+Math.random()" />
			            </td>
			      </tr>
			      <tr>
			            <td></td>
			            <td>
				            <input type="hidden" name="act" value="get_password" />
				            <input type="submit" style="width:100px;" class="redb left" value="确认提交" name="submit"/>
				            <a style="width:80px;" class="huib left ml10" href="/user.php">返回上一页</a>
			            </td>
			      </tr>
			</table>
		</form>
	</div>
</div>
<?php $this->load->view('layout/footer');?>
