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
    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <link rel="stylesheet" href="/css/words/mine.css" type="text/css">
</head>
<body bgcolor="#FFF">
<div id="warpper">
    {include file="../header.tpl" user=$user}

    <div id="main" class="layout-2">
        <div id="mine_slider" class="left">
            <h1 class="h2">
                我的发件箱
            </h1>
            <div class="word-list">
                <div class="title">
                    <span class="nickname">送往</span>
                    <span class="content">话题</span>
                    <span class="datetime">时间</span>
                </div>

{foreach $list as $item}
                    {$receive=user_by_id($item['tid'])}
                <div class="item">
                    <span class="nickname"><a href="/search.php?nickname={$receive.nickname}">{$receive.nickname}</a></span>
                    <span class="content">{$item.words}</span>
                    <span class="datetime">{date('m月d日 h时:m分',$item.time)}</span>
                </div>
{foreachelse}
                <div class="null">
                您还没给任何朋友发送留言哦
                </div>
{/foreach}
            </div>
        </div>

        <div id="mine_container" class="right container">
        </div>
    </div>

    {include file="../footer.tpl"}
</div>


<script src="/js/jquery-1.7.2.min.js"></script>
<script src="/js/jquery.pop.js"></script>
<script src="/js/jquery.form.js"></script>
<script src="/js/jquery.cookie.js"></script>
<script src="/js/login.js"></script>

<script src="/js/jquery.ga.js"></script>
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
