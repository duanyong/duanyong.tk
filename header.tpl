    <div id="header">
        <div id="logo">
            <a title="爱语记">
                <img src="img/logo.png" />
                <a target="_blank" href="about.html">关于我</a>
            </a>
        </div>

        <div id="user" style="display: {if empty($user)}none{/if};">
            <label>{$user.username}</label>
            <div id="u_cnt">
                <a target="_blank" data-label="查看总数" href="/mine?type=played">累积收听<span id="rec_played">5206</span>首</a>
                <a target="_blank" data-label="记录总数" href="/mine?type=played">累积收听<span id="rec_played">5206</span>首</a>
                <a target="_blank" data-label="关闭总数" href="/mine?type=played">累积收听<span id="rec_played">5206</span>首</a>
            </div>
        </div>

        <div id="anony" style="display: {if !empty($user)}none{/if};">
            <a href="/login.php?back={$back}" onclick="s_ga_click('main_login');">登录</a>
            <a href="/reg.php?back={$back}" onclick="s_ga_click('main_reg');">现在注册</a>
        </div>
    </div>
