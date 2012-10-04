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
    <link rel="stylesheet" href="css/reset.css" type="text/css">
    <link rel="stylesheet" href="css/common.css" type="text/css">
</head>
<body bgcolor="#FFF">
<div id="warpper">
    {include file="header.tpl" user=$user}

    <div id="main">
        <form id="words_form" action="words.php" method="post">
            <label for="token">写给</label><input id="token" type="text" name="token" /><br />
            <label for="content">想说的话</label><textarea id="content" type="text" name="words"></textarea>
            <input id="form_submit" type="submit" value="传送" /><div id="words_indicator"></div>
        </form>
    </div>

    {include file="footer.tpl"}
</div>


<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/jquery.pop.js"></script>
<script src="js/jquery.form.js"></script>
<script src="js/jquery.cookie.js"></script>
<script src="js/main.js"></script>

<script src="js/jquery.ga.js"></script>
<!-- script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script -->
<script type="text/javascript">
    s_ga_init('UA-34246201-1');
</script>
</body>
</html>
