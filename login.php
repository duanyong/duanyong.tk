<?php
/**
 * 提供用户注册的程序
 *
 *  /db/devdb.user.php
 *
 * */

require_once(__DIR__ . "/devinc.all.php");
require_once(ROOT_DIR . "/db/devdb.user.php");
require_once(ROOT_DIR . "/user/devapi.user.php");


$arg	= array();

$user	= array();


if (a_bad_username($_POST["username"], $username)) {
    //没有输入用户名
    exit(a_action_page("请输入登录账号"));
}


if (a_bad_string($_POST["password"], $password)) {
    //没有输入密码
    exit(a_action_page("请输入登录密码"));
}



// 取值完毕



//检查数据库中的值是否存在
if (false === ( $user = a_user_by_username($username) )) {
    // 数据库错误
    exit(a_server_error());
}


if ($user["password"] !== md5($password)) {
    //密码错误
    exit(a_action_page("用户名或者密码错误，请重新输入"));
}


//密码和用户正确，
$arg["err"] = 0;



//设置cookie
if (empty($_POST["rem"])) {
    $exp = 0;

} else {
    $exp = 30;
}


$exp = $exp * 86400 * 30 + time();

setCookie("uid", $user["uid"], $exp);
setCookie("key", $user["password"], $exp);



//更新登录信息
a_user_update_login($user);


exit(a_action_redirect("/me", "登陆成功，稍后将会转到首页。", 3));
