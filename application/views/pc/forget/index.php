<?php $this->load->view('pc/layout/header');?>
<div class="add-names">
    <div class="add-body">
        <div class="add-progress">
            <ul class="clearfix">
                <li class="fonter active">
                    <hr>
                    <i>1</i>
                    <p>填写账户名</p>
                </li>
                <li class="fonter">
                    <hr>
                    <i>2</i>
                    <p>确认验证信息</p>
                </li>
                <li class="fonter">
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
        <form class="forget-form-account forget-form-type" method="post">
            <div class="add-group clearfix">
                <label class="add-label">账户名：</label>
                <input type="text" name="username" class="add-control required" placeholder="请填写登录账户名">
                <span class="error"></span>
            </div>
            <div class="add-group clearfix">
                <label class="add-label">验证码：</label>
                <input type="text" name="captcha" class="add-control yest required">
                <span class="error"></span>
                <a id="ajaxJsonCaptcha" class="supplyLand-img" href="javascript:;">
                   <?php echo $captcha['image'];?> 
                </a>
            </div>
            <div class="add-group">
                <button type="submit" class="btn btn-default">下一步</button>
            </div>
        </form>
    </div>
</div>
<?php $this->load->view('pc/layout/footer');?>