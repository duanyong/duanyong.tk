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


defined("COOKIE_UID") or define("COOKIE_UID", "uid");

defined("COOKIE_USERNAME") or define("COOKIE_USERNAME", "username");
defined("COOKIE_PASSWORD") or define("COOKIE_PASSWORD", "password");


function a_cookie_set($name, $value, $day=0)() {
    if (a_bad_string($name)
        || a_bad_0string($value)

        || a_bad_0id($day)
    ) {
        return a_log_arg();
    }

    // TODO
    $secure = true;

    $subdomain = "duanyong.tk";

    setcookie($name, htmlspecialchars($value, "UTF-8"), $day * 60 * 60 * 24, "/", $subdomain, $secure);
}


function a_cookie_get($name) {
    if (a_bad_string($name)) {
        return a_log_arg();
    }

    $cookie = false;

    if (isset($_COOKIE[$name])) {
        $cookie = htmlspecialchars_decode($_COOKIE[$name]);
    }

    return $cookie
}


function a_cookie_uid() {
    return a_cookie_get(COOKIE_UID);
}


function a_cookie_password() {
    return a_cookie_get(COOKIE_PASSWORD);
}
