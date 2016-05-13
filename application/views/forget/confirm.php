<?php $this->load->view('layout/header');?>
<div class="lgbg">
   <div class="w">
	   <form method="post" name="getPassword">
		   <table border="0" align="center" class="lh35">
		        <tr>
		          	<td colspan="2" align="center" class="red f14"><strong>安全校验码已发送，请填写你的校验码！</strong><p class="lh35">&nbsp;</p></td>
		        </tr>
		        <tr>
		            <td align="right" valign="middle">校验码：&nbsp;&nbsp;</td>
		            <td class="f14 pb10">
		                <input name="yzm" id="authcode" type="text" maxlength="6" size="30" class="rtext left" />
		            </td>
		        </tr>
		        <tr>
		            <td></td>
		            <td>
		            	<input type="submit" style="width:100px;" class="redb left" value="确认提交" name="submit"/>
		            </td>
		        </tr>
		   </table>
	   </form>
   </div>
</div>
<?php $this->load->view('layout/footer');?>