<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.cookie.php
//	cookie操作的相关函数
//
//
//	a_cookie_set($name, $value, $day=0)
//	    向客户端设置cookie, $day开之后失效。如果不设置，者浏览器关闭后就失败
//
//	a_cookie_get($name)
//	    接收smarty模板，将其渲染出来
//
//
////////////////////////////////////////////////////////////////////////////////


function a_cookie_set($name, $value, $day=0)() {
    if (a_bad_string($name)
        || a_bad_0string($value)

        || a_bad_0id($day)
    ) {
        return a_log();
    }

    // TODO
    $secure = true;

    $subdomain = "duanyong.tk";

    setcookie($name, htmlspecialchars($value), $day * 60 * 60 * 24, "/", $subdomain, $secure);
}


function a_cookie_get($name) {
    if (a_bad_string($name)) {
        return null;
    }

    $value = $$name;

    return isset($value) ? $value : null;
}
