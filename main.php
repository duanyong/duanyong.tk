<?php
/**
 * 用户主界面（匿名用户也可以使用）
 *
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/user/devapi.user.php");


if (!( $user = user_login_by_cookie() )) {
    //用户未登录
    $user = array();

    //设置用户标识
    if(!user_token_from_cookie()) {
        //用户之前没有来访过
        user_create_by_token(user_token_from_cookie(true));
    }
}

$data           = array();
$data['user']   = $user;

s_action_page($data);

