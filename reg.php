<?php
/**
 * 提供用户注册的程序
 *
 *  /db/devdb.user.php
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/dev/devinc.password.php");


if (s_bad_post('username', $username)) {
    //没有填写账户
    return s_action_error('请填写您的手机或者邮箱做为登录账号', 10001);
}

if (s_bad_post('password', $password)) {
    //没有填写密码
    return s_action_error('请填写你的登录密码', 10002);
}


if (s_bad_post('nickname', $nickname)) {
    //没有填写密码
    return s_action_error('请填写你的用户昵称', 10003);
}



if (s_db_one("select `id` from `%s_user` where `username`='{$username}' or `nickname`='{$nickname}'")) {
    //用户名已被注册
    return s_action_error('您的用户名或昵称已被注册', 10004);
}


$time = s_action_time();
$data['ftime']	    = date('Y-m-d H:i:s', $time);
$data['username']   = $username;
$data['nickname']   = $nickname;
$data['password']   = s_sso_encrypt($username, $password);



// 插入数据
if (false === ( $uid =  s_db('%s_user:insert', $data) )) {
    //注册失败
    return s_action_error('注册失败', 10005);
}

$user = array(
    'uid'       => $uid,
    'nickname'  => $nickname,
    'username'  => $username,
);


s_action_json(array(
    'error' => 0,
    'sue'   => s_sso_encrypt($user, $time),
    'sup'   => s_sso_chain($user, $time),
));
