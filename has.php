<?php
/**
 * 提供用户注册的程序
 *
 *  /db/devdb.user.php
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/dev/devinc.password.php");


$ret = array();

if (!s_bad_get('username', $username)) {
    $data['username'] = s_db_one("select `id` from `%s_user` where `username`='{$username}'");
}


if (!s_bad_get('nickname', $nickname)) {
    $data['nickname'] = s_db_one("select `id` from `%s_user` where `nickname`='{$nickname}'");
}


$data['error'] = 0;

s_action_json($data);
