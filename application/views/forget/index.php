<?php $this->load->view('layout/header');?>
<div class="lgbg">
	<div class="w">
		<form method="post" name="getPassword" class="forget-form">
			<table border="0" align="center" class="lh35">
			      <tr>
			            <td colspan="2" align="center" class="red"><h2 class="yahei f18">请输入您的注册邮箱或注册手机号找回密码</h2><p class="lh35">&nbsp;</p></td>
			      </tr>
			      <tr>
			            <td  align="right" valign="middle">邮箱/手机号：&nbsp;&nbsp;</td>
			            <td  class="f14 pb10"><input name="username" id="account" type="text" size="25" class="ipt left"/></td>
			      </tr>
			      <tr>
			            <td align="right">验证码：&nbsp;&nbsp;</td>
			            <td class="f14 pb10">
			            	<input name="captcha" id="captcha" type="text" maxlength="4" size="25" class="ipt left" />
			            </td>
			      </tr>
			      <tr>
			            <td align="right"></td>
			            <td class="f14 pb10 captcha">
			              <?php echo $captcha['image'];?>
			            </td>
			      </tr>
			      <tr>
			            <td></td>
			            <td>
				            <input type="submit" style="width:100px;" class="redb left" value="确认提交" name="submit"/>
				            <a style="width:80px;" class="huib left ml10" href="javascript:history.back();">返回上一页</a>
			            </td>
			      </tr>
			</table>
		</form>
	</div>
</div>
<?php $this->load->view('layout/footer');?>