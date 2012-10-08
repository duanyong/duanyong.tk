<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta content="initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0, user-scalable=yes, width=device-width" name="viewport">
    <meta name="Keywords" content="爱语记,爱情的甜语密语" />
    <meta name="Description" content="留住那些美好的支字片语,给未来的自己" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <title>爱语记</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="css/common.css" type="text/css">
    <link rel="stylesheet" href="css/reg.css" type="text/css">
</head>
<body bgcolor="#FFF">
<div id="warpper">
{include file="header.tpl"}

    <div id="main" class="layout-2">
        <div id="reg_slider" class="left">
            <h3 class="h3">爱语记</h3>
            <div id="reg-feature-item">
                <a class="title">思念是一呼一吸</a>
                <div class="desc">不管睡前还是醒后，这都是有你和我的梦</div>
                <div class="icon icon-voice"></div>
            </div>
            <div id="reg-feature-item">
                <div class="icon icon-bottle"></div>
                <a class="title">将你说的话传递给她</a>
                <div class="desc">距离无法停止我的思念，终有一天你会在我眼前</div>
            </div>
            <!--
            <div id="reg-feature-item">
                <a class="title">同是天涯论落人</a>
                <div class="desc">上辈子我们有相遇未见，今世再续前缘</div>
                <div class="icon icon-localtion"></div>
            </div>
            //-->
        </div>

        <div id="reg_container" class="right container">
        <!--
            <div class="ad-box">
            </div>
        //-->
            <form id="reg_form" class="form-1" action="register.php" method="post">
                <fieldset>
                    <div class="item">
                        <label for="username">账 号</label>
                        <input id="username" class="text" type="text" name="username" data-text="请输入您的邮箱或手机号" tabindex="1" maxlength="25" value="{$username}" />
                        <br /><span id="username_wrong" class="monition" style="display: none;"></span>
                    </div>
                    <div class="item">
                        <label for="password">密 码</label>
                        <input id="password" class="text" type="password" name="password" data-text="请输入您的登录密码" tabindex="2" maxlength="32" value="{$password}" />
                        <br /><span id="password_wrong" class="monition" style="display: none;"></span>
                    </div>
                    <div class="item">
                        <label for="nickname">昵 称</label>
                        <input id="nickname" class="text" type="text" name="nickname" data-text="好名字会让所有人都记得你" tabindex="3" maxlength="18" value="{$nickname}" />
                        <br /><span id="nickname_wrong" class="monition" style="display: none;"></span>
                    </div>
                    <div class="item">
                        <input id="reg_submit" class="submit" type="submit" value="注 册" tabindex="4" />
                    </div>
                </fieldset>
            </form>

            <div style="display: none;">
                联合登录：新浪微博、QQ、MSN
            </div>
        </div>
    </div>

{include file="footer.tpl"}
</div>


<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/jquery.pop.js"></script>
<script src="js/jquery.form.js"></script>
<script src="js/jquery.cookie.js"></script>
<script src="js/reg.js"></script>

<script src="js/jquery.ga.js"></script>
<!-- script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script -->
<script type="text/javascript">
{if $wrong}
    {foreach $wrong as $key=>$msg}
        $('#{$key}').wrong('{$msg}');
    {/foreach}
{/if}

    s_ga_init('UA-34246201-1');
</script>
</body>
</html>
