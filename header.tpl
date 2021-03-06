{if !isset($user)}
    {$user=user_login_by_cookie()}
{/if}
    <div id="header">
        <div class="nav-logo">
            <img src="{$smarty.const.HTTP_HOST}/img/logo.png" />
            <a target="_blank" href="about.html">关于Beta</a>
        </div>

        <div class="nav-box nav-fun" style="display: {if empty($user)}none{/if};">
            <a target="_blank" href="{$smarty.const.HTTP_HOST}/words/mine.php">累积留言{$user.sum|default:0}次</a>
            <a href="{$smarty.const.HTTP_HOST}/logout.php">退出</a>
        </div>

        <div class="nav-box nav-fun" style="display: {if !empty($user)}none{/if};">
            <a href="{$smarty.const.HTTP_HOST}/reg.php?back={$back}" onclick="s_ga_click('main_reg');">注册</a>
            <a href="{$smarty.const.HTTP_HOST}/login.php?back={$back}" onclick="s_ga_click('main_login');">我要登录</a>
        </div>

        <div class="nav-box nav-wel">
            <h2>欢迎{$user.nickname|default:'你'}回来</h2>
        </div>
    </div>
