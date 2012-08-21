<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.cookie.php
//	cookie操作的相关函数
//
//
//	a_cookie($key, &$value=fase, $day=0)
//	    向客户端设置cookie, $day开之后失效。如果不设置，者浏览器关闭后就失败
//
//	a_cookie($name)
//	    接收smarty模板，将其渲染出来
//
//
////////////////////////////////////////////////////////////////////////////////



function a_cookie($key, &$value=false, $day=0) {
    if (a_bad_string($key)) {
        return false;
    }

    if ($value !== false) {
        a_cookie_set($key, $value, $day);

        return ;
    }

    return a_cookie_get($key);
}


function a_cookie_set($key, $value, $day=0) {
    if (a_bad_string($key)
        || a_bad_0string($value)

        || a_bad_0id($day)
    ) {
        return false;
    }

    $exp = $day * 86400 + a_action_time();

    setcookie($key, htmlspecialchars($value), $exp);
}


function a_cookie_get($key) {
    if (a_bad_string($key)) {
        return a_log_arg();
    }

    return isset($_COOKIE[$key]) ? htmlspecialchars_decode($_COOKIE[$key]) : false;
}
