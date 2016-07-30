<?php $this->load->view('layout/header');?>
<div class="register detailmain">
    <div class="apartfrom clearfix">
        <div class="apartfrom-left">
            <div class="title">
                <div class="apartfrom-left-center clearfix">
                    <p>会员注册</p>
                    <span class="apartfrom-triangle"></span>
                </div>
            </div>
            <div class="apartfrom-left-test">
                <form class="register-form-validate" action="<?php echo site_url('register/doRegister')?>" method="post">
                    <table class="table">
                        <tr>
                            <td class="apartfrom-cellular">手机号码：</td>
                            <td class="apartfrom-position-hao clearfix">
                                <?php if ($this->input->get('backurl')) :?>
                                <input type="hidden" name="backurl" value="<?php echo $this->input->get('backurl');?>">
                                <?php endif;?>
                               	<input type="text" name="mobile_phone" class="required" placeholder="请输入您的手机号" autocomplete="off"/>
                                <i class="iconfont">&#xe605;</i>
                            </td>
                        </tr>
                        <tr>
                            <td class="apartfrom-cellular">设置密码：</td>
                            <td class="apartfrom-position-hao clearfix">
                                <input type="password" name="password" id="password" class="required" placeholder="6-20个大小写英文字母、符号或者字母" autocomplete="off"/>
                                <i class="iconfont">&#xe609;</i>
                            </td>
                        </tr>
                        <tr>
                            <td class="apartfrom-cellular">确认密码：</td>
                            <td class="apartfrom-position-hao clearfix">
                                <input type="password" name="confirm_password" minlength="6" class="required" placeholder="请再次输入密码" autocomplete="off"/>
                                <i class="iconfont">&#xe609;</i>
                            </td>
                        </tr>
                        <tr>
                            <td class="apartfrom-cellular ">验证码：</td>
                            <td class="clearfix forget-form-account">
                                <input type="text" name="captcha" class="apartfrom-verify d-captcha required" placeholder="请输入验证码"  autocomplete="off"/>
                                <a href="javascript:;" id="ajaxJsonCaptcha" ><?php echo $captcha['image']; ?></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="apartfrom-cellular">手机验证码：</td>
                            <td class="apartfrom-error clearfix">
                                <input type="text" name="verify" class="apartfrom-verify required" autocomplete="off"/>
                                <button type="button" class="apartfrom-text btnsend">获取动态密码</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="apartfrom-cellular">推荐人：</td>
                            <td class="apartfrom-position-hao">
                                <input type="text" name="parent_id" value="<?php echo $parent_id?>" <?php if (!empty($parent_id)) :?>readonly="readonly"<?php endif;?> placeholder="填写推荐人" autocomplete="off"/>
                            </td>
                        </tr>
                        <tr class="apartfrom-negotiate">
                            <td class="apartfrom-cellular"></td>
                            <td class="apartfrom-fise">
                                <input type="checkbox" name="is_check" checked="checked" class="required">
                                <p>我已阅读并接受<a href="javascript:;">《妙处网站服务协议》</a></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="apartfrom-cellular"></td>
                            <td><button type="submit" class="apartfrom-submit">立即注册</button></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <div class="apartfrom-right">
            <div class="apartfrom-rapid">
                <p>快速注册</p>
                <div class="apartfrom-regter clearfix">
                    <a href="javascript:;"><i class="iconfont">&#xe601;</i></a>
                    <a href="javascript:;"><i class="iconfont">&#xe606;</i></a>
                    <a href="javascript:;"><i class="iconfont zfb">&#xe608;</i></a>
                </div>
                <p>已有账号，<a href="<?php echo site_url('login');?>">立即登录</a></p>
            </div>
            <div class="apartfrom-img">
                <img src="passport/images/zhuc.jpg">
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout/footer');?>
