<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {if not empty($url)}<meta http-equiv="refresh"{if not empty($stay)} content="{$stay}"{/if} url="{$url}" />{/if}
    <title>爱语记</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <meta content="您手上的心情布袋" name="description"/> 

    {css name="base"}
</head>
<body>
    {* 导入header *}
    {include file="header.tpl"}

    {if isset($err) and $err eq "servererr"}
    <div style="">
	{* 服务器发生严重出错 *}
	服务器发生严重错误，请稍后再试。我们会尽快，给您带来的不便请谅解。
    </div>

    {elseif isset($err) and isset($msg)}
    <div>
	{$msg}<a href="{if isset($url)}{$url}{elseif}/{/if}">点击此处返回</a>
    </div>
    {/if}


    {* 导入footer*}
    {include file="footer.tpl"}
</body>
</html>
