<?php
/**
 * 用户主界面（匿名用户也可以使用）
 *
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/user/devapi.user.php");


if (!( $user = user_autologin() )) {
    //用户未登录
    $user = array();

    //设置用户标识
    user_token();
}

$data           = array();
$data['user']   = $user;

s_action_page($data);

