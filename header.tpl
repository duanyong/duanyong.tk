    <div id="header">
        <div class="nav-logo">
            <a title="爱语记">
                <img src="img/logo.png" />
                <a target="_blank" href="about.html">关于我</a>
            </a>
        </div>


        <div class="nav-box nav-fun" style="display: {if empty($user)}none{/if};">
            <a target="_blank" data-label="查看总数" href="/mine?type=played">累积收听<span id="rec_played">5206</span>首</a>
            <a href="/logout.php">退出</a>
        </div>

        <div class="nav-box nav-fun" style="display: {if !empty($user)}none{/if};">
            <a href="/reg.php?back={$back}" onclick="s_ga_click('main_reg');">注册</a>
            <a href="/login.php?back={$back}" onclick="s_ga_click('main_login');">我要登录</a>
        </div>

        <div class="nav-box nav-wel">
            <h2>欢迎{$user.nickname|default:'你'}回来</h1>
        </div>
    </div>
