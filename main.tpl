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
    <link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body bgcolor="#FFF">
<div id="warpper">
    {include file="header.tpl" user=$user}

    <div id="main" class="layout-2">
        <div id="main_content" class="left">
            <form id="words_form" class="form-2" action="words.php" method="post">
                <fieldset>
                    <div class="item">
                        <label for="words_token">写给你</label>
                        <input id="words_token" class="text" type="text" name="token" data-text="可以输入Ta的邮箱或手机号" tabindex="1" maxlength="25" value="{$token}" />
                        <br /><span id="token_wrong" class="monition" style="display: none;"></span>
                    </div>
                    <div class="item">
                        <label for="words_message">我想说</label>
                        <textarea id="words_message" class="mutitext" name="wrods" data-text="我现在很多话想对你说，却无从起口" tabindex="2">{$words}</textarea>
                        <br /><span id="password_wrong" class="monition" style="display: none;"></span>
                    </div>
                    <div class="item" style="display: none;">
                        <label for="words_pic">给你看张相片</label>
                        <input id="wrods_pic" class="text" type="text" name="pic" data-text="好名字会让所有人都记得你" tabindex="" maxlength="18"  />
                        <br /><span id="nickname_wrong" class="monition" style="display: none;"></span>
                    </div>
                    <div class="item">
                        <input id="words_submit" class="submit" type="submit" value="投 递" tabindex="3" />
                    </div>
                </fieldset>

                <input type="hidden" id="words_now" name="now" value="" />
            </form>
        </div>

        <div id="main_slider" class="right">
            <div id="main_gap">
            </div>
            <div id="main_exam">
            </div>
        </div>
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
