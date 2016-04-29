<?php $this->load->view('pc/layout/header');?>

<div class="add-names">
<!--     <div class="add-fonter clearfix">
        <ul>
            <li class="fonter secend"><i>1.</i>填写账户名</li>
            <li class="fonter secend"><i>2.</i>确认验证信息</li>
            <li class="fonter tist"><i>3.</i>修改密码</li>
            <li class="last"><i class="normal">√</i>完成</li>
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
                <li class="fonter secend">
                    <hr>
                    <i>2</i>
                    <p>确认验证信息</p>
                </li>
                <li class="fonter tist">
                    <hr>
                    <i>3</i>
                    <p>修改密码</p>
                </li>
                <li class="fonter active">
                    <hr>
                    <i>√</i>
                    <p>完成</p>
                </li>
             </ul>
        </div>
        <div class="aly-ccx">
            <div class="ccx-li clearfix">
                <img src="passport/pc/images/tp4.png">
                <p>您的密码已修改成功！请重新登录</p>
            </div>
            <div class="rutuer"><span id="jump-to-home">10</span>秒后跳转<a href="<?php echo $this->config->passport_url;?>">登录页</a></div>
        </div>
    </div>
</div>
<?php $this->load->view('pc/layout/footer');?>
<script type="text/javascript">
function countDown(secs, surl){
    var jumpTo = document.getElementById('jump-to-home');
    jumpTo.innerHTML=secs;
    if (--secs > 0) {
        setTimeout("countDown("+secs+",'"+surl+"')", 1000);
    } else {
        location.href=surl;
    }
}
countDown(10, '<?php echo $this->config->passport_url;?>');
</script> 