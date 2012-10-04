<?php
/**
 * 提供用户注册的程序
 *
 *  
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/user/devapi.user.php");


if (s_bad_post('username', $username)) {
    //没有填写账户
    $wrong['username'] = '请填写您的手机或者邮箱做为登录账号';
}

if (s_bad_post('password', $password)) {
    //没有填写密码
    $wrong['username'] = '请填写你的登录密码';
}

if (s_bad_post('nickname', $nickname)) {
    //没有填写昵称
    $wrong['username'] = '请填写你的用户昵称';
}


if (isset($wrong)
    || !user_create($username, $password, $nickname)
) {

    $wrong['username'] = "您的账号已被占用，是否需要找回密码";

    return s_action_page(array(
        'error'     => 10000,
        'username'  => $username,
        'password'  => $password,
        'nickname'  => $nickname,
        'wrong'     => $wrong,
    ), 'reg.tpl');
}


//模拟用户登录
user_login($username, $password, false);


//页面跳转到用户首页
s_action_redirect('main.php');
