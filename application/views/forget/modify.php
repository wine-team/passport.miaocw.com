<?php $this->load->view('layout/header');?>

<div class="add-names">
 <!--    <div class="add-fonter clearfix">
        <ul>
            <li class="fonter secend"><i>1.</i>填写账户名</li>
            <li class="fonter tist"><i>2.</i>确认验证信息</li>
            <li class="fonter active"><i>3.</i>修改密码</li>
            <li><i class="normal">√</i>完成</li>
        </ul>
    </div> -->
    <div class="add-body">
       <div class="add-progress">
            <ul class="clearfix">
                <li class="fonter secend">
                    <hr>
                    <i>1</i>
                    <p>填写账户名</p>
                </li>
                <li class="fonter tist">
                    <hr>
                    <i>2</i>
                    <p>确认验证信息</p>
                </li>
                <li class="fonter active">
                    <hr>
                    <i>3</i>
                    <p>修改密码</p>
                </li>
                <li class="fonter">
                    <hr>
                    <i>√</i>
                    <p>完成</p>
                </li>
             </ul>
        </div>
        <form class="forget-modify-password forget-form-type">
              <div class="add-group clearfix">
                  <label class="add-label">新的密码：</label>
                  <input type="hidden" name="username" value="<?php echo $username ?>">
                  <input type="password" name="password" id="password" minlength="6" class="add-control required">
                  <span class="error"></span>
              </div>
              <div class="add-group clearfix">
                  <label class="add-label">确认密码：</label>
                  <input type="password" name="confirm_password" minlength="6" class="add-control required">
                  <span class="error"></span>
              </div>
              <div class="add-group">
                  <button type="submit" class="btn btn-default">修改密码</button>
            </div>
        </form>
    </div>
</div>
<?php $this->load->view('layout/footer');?>