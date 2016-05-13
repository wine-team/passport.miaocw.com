<?php $this->load->view('layout/header');?>
<div class="lgbg">
   <div class="w">
	    <form method="post" name="getPassword">
	        <table border="0" align="center" class="lh35">
		          <tr>
		              <td colspan="2" align="center" class="red f14"><strong>请输入你的新密码</strong><p class="lh35">&nbsp;</p></td>
		          </tr>
		          <tr>
		              <td align="right" valign="middle">新密码：&nbsp;&nbsp;</td>
		              <td class="f14 pb10">
		                <input name="new_password" id="new_password" type="password" size="30" class="rtext left"/>
		              </td>
		          </tr>
	              <tr>
		              <td align="right" valign="middle">确认密码：&nbsp;&nbsp;</td>
		              <td class="f14 pb10">
		                <input name="confirm_password" id="confirm_password" type="password" size="30" class="rtext left" />
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
