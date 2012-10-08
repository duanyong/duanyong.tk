<?php
/**
 * 提供用户注册的程序
 *
 *  /db/devdb.user.php
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/user/devapi.user.php");

if (( $user = user_login_by_cookie() )) {
    user_logout($user);
}

$data['user'] = array();

s_action_page($data, 'logout.tpl');
