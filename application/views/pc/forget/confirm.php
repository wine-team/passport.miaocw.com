<?php $this->load->view('pc/layout/header');?>

<div class="add-names">
<!--     <div class="add-fonter clearfix">
        <ul>
            <li class="fonter tist"><i>1.</i>填写账户名</li>
            <li class="fonter active"><i>2.</i>确认验证信息</li>
            <li class="fonter"><i>3.</i>修改密码</li>
            <li><i class="normal">√</i>完成</li>
        </ul>
    </div> -->
    <div class="add-body">
        <div class="add-progress">
            <ul class="clearfix">
                <li class="fonter tist">
                    <hr>
                    <i>1</i>
                    <p>填写账户名</p>
                </li>
                <li class="fonter active">
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
        <div class="tab-content">
            <div class="tab-pane active" id="mobile">
                <form class="forget-form-type forget-form-mobile">
                    <div class="add-group clearfix">
                        <label class="add-label">手机号：</label>
                        <div class="input-li">
                            <input type="hidden" name="username" value="<?php echo $user_name?>">
                            <input type="text" name="phone" value="<?php echo $phone;?>" class="add-control input-li" readonly="readonly">
                        </div>
                        <button type="button" class="btn btn-success btnsend" data-attr="<?php echo $encode_phone;?>">发送验证码</button>
                    </div>
                    <div class="add-group clearfix">
                        <label class="add-label">验证码：</label>
                        <input type="text" name="verify" class="add-control yest required">
                        <span class="error"></span>
                    </div>
                    <div class="add-group">
                        <button type="submit" class="btn btn-default">下一步</button>
                    </div>
                </form>
            </div>
            <!-- 
            <div class="tab-pane" id="email">
                 <form class="forget-form-type">
                     <div class="add-group clearfix">
                         <label class="add-label">邮箱：</label>
                         <div class="input-li">
                             <input type="text" name="email" value="812****8@qq.com" class="add-control input-li email" readonly="readonly">
                         </div>
                         <button type="button" class="btn btn-success btnsend">发送验证码</button>
                     </div>
                     <div class="add-group clearfix">
                        <label class="add-label">验证码：</label>
                        <input type="text" class="add-control yest">
                     </div>
                     <div class="add-group">
                         <button type="submit" class="btn btn-default">下一步</button>
                    </div>
                </form>
            </div>
             -->
        </div>
    </div>
</div>
<?php $this->load->view('pc/layout/footer');?>