<?php
/**
 * 提供用户注册的程序
 *
 *  /db/devdb.user.php
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/user/devapi.user.php");

if (( $user = user_autologin() )) {
    //用户已登录
    $data['user'] = $user;

    return s_action_redirect('/main.php');
}

if (s_bad_post('username', $username)) {
    //填写账号
    return s_action_page(array(
        'error'     => 10000,
    ), 'login.tpl');
}

if (s_bad_post('password', $password)) {
    //未填写密码
    $wrong['password'] = "您的密码不正确，请重新输入";

    return s_action_page(array(
        'error'     => 10000,
        'username'  => $username,
        'wrong'     => $wrong,
    ), 'login.tpl');
}

if (s_bad_post('remember', $remember)) {
    $remember = false;
}



if (!user_by_username($username)) {
    //用户不存在
    $wrong['username'] = "您使用的账号不存在，请重新登录";

    return s_action_page(array(
        'error'     => 10000,
        'username'  => $username,
        'password'  => $password,
        'wrong'     => $wrong,
    ), 'login.tpl');
}

if (!( $user = user_login($username, $password, $remember) )) {
    //用户名或密码不正确
    $wrong['password'] = "您的密码不正确，请重新输入";

    return s_action_page(array(
        'error'     => 10000,
        'username'  => $username,
        'password'  => $password,
        'wrong'     => $wrong,
    ), 'login.tpl');
}

$data['user'] = $user;

s_action_redirect('/main.php');
