<?php
/**
 * 提供用户注册的程序
 *
 *  /db/devdb.user.php
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/user/devapi.user.php");

$data = array();

if (( $user = user_autologin() )) {
    //用户已登录
    $data['user'] = $user;

    return s_action_redirect('/main.php');
}

s_action_page($data);
