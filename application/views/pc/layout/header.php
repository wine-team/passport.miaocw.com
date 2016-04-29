<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="renderer" content="webkit">
<meta name="title"  content="鑫余集团"/>
<meta name="keywords" content="鑫余集团" />
<meta name="description"  content="鑫余集团" />
<title>鑫余集团</title> 
<base href="<?php echo $this->config->skins_url;?>"/>
<link type="image/x-icon" rel="shortcut icon" href="passport/pc/images/logo02.png" />
<?php css('passport/pc', 'bootstrap.min');?>
<?php css('passport/pc', 'reset', '20160415');?>
<?php css('passport/pc', 'style', '20160415');?>

<?php js('passport/pc', 'jquery-1.10.2');?>
<?php js('passport/pc', 'bootstrap.min');?>
<?php js('passport/pc', 'jquery.validate.min');?>
<?php js('passport/pc', 'jquery.validate.messages_zh');?>
 <!--[if lt IE 10]>
<?php js('passport/pc', 'placeholder');?>
<![endif]-->
<?php js('passport/pc', 'index', '20160415');?>
</head>
<body>
<div class="login-header">
    <div class="header clearfix">
        <a href="<?php echo $this->config->main_base_url?>" class="welcome-left">
            鑫余集团
        </a>
        <span class="welcome-login">
            <?php 
                $action = $this->router->fetch_class();
                echo $action == 'login' ? '欢迎登录' : ( $action == 'register' ? '欢迎注册' : '忘记密码' );
		    ?>
		</span>
		<?php if ($action == 'login' || $action == 'register') :?>
		<span class="service">24小时客服热线：888-8888-888</span>
		<?php else :?>
		<span class="service">
		    <a href="<?php echo site_url('pc/register')?>">注册</a> <span class="cutoff-line">|</span> <a href="<?php echo site_url('pc/login')?>">登录</a>
		</span>
		<?php endif;?>
	</div>
</div>
