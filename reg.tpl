<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>爱语记</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <meta content="您手上的心情布袋" name="description"/> 

    {css name="common, reg"}
</head>
<body>
    {* 导入header *}
    {include file="header.tpl"}

    <div id="reg-slide" class="reg-box">
        asda
    </div>

    <div id="reg-action" class="reg-box">
        <span>注册新账号：</span><br />
        <form action="/reg.php" method="post">
	        账号：<input type="text" name="username" value="手机/邮箱" maxlength="25" tabindex="1" /><br/>
	        密码：<input type="password" name="password" value="" maxlength="25" tabindex="2" /><br/>
	        安全设置：<input type="text" name="securt" value="" maxlength="25" tabindex="3" /><br/>

	        <input type="submit" value="提交" tabindex="3" /><br/>
        </form>
    </div>


    {* 导入footer*}
    {include file="footer.tpl"}
</body>
</html>

{*devwatch: html*}
