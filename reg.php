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


if (a_bad_username($_POST["username"], $user["username"])) {
    exit(a_action_page("请填写您的手机或者邮箱做为登录账号"));
}

if (a_bad_string($_POST["password"], $user["password"])) {
    exit(a_action_page("请填写你的登录密码"));
}




// 取值完毕


//检查数据库中的值是否存在
if (a_user_by_username($user["username"])) {
    exit(a_action_page("对不起，此账号已被注册，请重新输入新的账号"));
}


$user["regip"]	    = a_action_ip();
$user["ctime"]	    = a_action_time();
$user["password"]   = md5($user["password"]);


// 插入数据
if (false === a_db("user:insert", $user)) {
    //注册失败
    exit(a_server_error());
}



//一切正常，可以注册
$arg["err"] = 0;


exit(a_action_redirect("/index.shtml", "注册成功，稍后回到首页", 1));
