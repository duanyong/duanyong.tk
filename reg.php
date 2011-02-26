<?php
/**
 * 提供用户注册的程序
 *
 * */

require_once(__DIR__ . "/devinc.all.php");
require_once(ROOT_DIR . "/db/devdb.user.php");
require_once(ROOT_DIR . "/user/devapi.user.php");


///
// 登录逻辑的必要值检查（邮箱、密码）
if (a_bad_ajax()) {
    //非正常提交，直接返回403

    return false;
}

global $arg;

if (a_bad_id($_POST["email"], $email)) {
    $arg["err"]["email"] = "邮箱做为登录账号，必须填写。";
}

if (a_bad_string($_POST["password"], $password)) {
    $arg["err"]["password"] = "密码是保护账号的基本手段，必须填写。";
}

if (a_bad_mobile($_POST["mobile"], $mobile)) {
    $arg["err"]["password"] = "请填写您的手机号码。";
}


//取值完毕

//检查数据库中的值是否存在
if (false !== ( $user = a_user_by_email($email) )) {
    //已经有用户注册过了

    $arg["err"]["email"] = "您输入的用户名已经被注册。";
}

if (!a_bad_array($arg["err"])) {
    //有错误发生，返回错误

    return a_action_done();
}



//准备存储新用户
$user = array(
    "email"	=> $email,
    "mobile"	=> $mobile,
    "password"	=> md5($password),
);

echo var_export($arg, true);

if (false === (a_user_reg($user) )) {
    //数据存储发生错误，提示用户

    //报告错误
    $arg["err"]["db"] = "对不起，服务器和她女朋友吵架了，能否帮助安抚下，谢啦。";

    exit(a_action_done());
}


//一切正常，可以注册
$arg["err"] = 0;

echo var_export($arg, true);

exit(a_action_done());
