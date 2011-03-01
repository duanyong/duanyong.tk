<?php
/**
 * 提供用户注册的程序
 *
 *  /db/devdb.user.php
 *  /user/devapi.user.php
 *
 * */

require_once(__DIR__ . "/devinc.all.php");
require_once(ROOT_DIR . "/db/devdb.user.php");
require_once(ROOT_DIR . "/user/devapi.user.php");


// 登录逻辑的必要值检查（邮箱、密码）
if (a_bad_ajax()) {
    //非正常提交，直接返回403

    return false;
}


$arg = array();

if (a_bad_mobile($_POST["mobile"], $mobile)) {
    $arg["mobile"] = "邮箱做为登录账号，必须填写。";
}

if (a_bad_string($_POST["password"], $password)) {
    $arg["password"] = "密码是保护账号的基本手段，必须填写。";
}

if (a_bad_0id($_POST["sex"], $sex)) {
    $arg["sex"] = "请选择性别。";
}


// 取值完毕

//检查数据库中的值是否存在
if (false === ( $user = a_user_by_mobile($mobile) )) {
    // 数据库错误
    // 已经有用户注册过了

    $arg["from"] = "对不起，服务器和她女朋友吵架了，能否帮助安抚下，谢啦。";
}

if (!a_bad_array($user)) {
    // 用户存在，检查是否是过期的用户

    $arg["mobile"] = "您提供的手机号码有问题，请联系管理员。";
}


if (!empty($arg)) {
    // 有错误发生，返回错误

    exit(a_action_done());
}



// 准备存储新用户
$user = array(
    "sex"	=> $sex === 1 ? 1 : 0,
    "mobile"	=> $mobile,
    "password"	=> md5($password),
);


if (false === (a_user_reg($user) )) {
    //数据存储发生错误，提示用户

    //报告错误
    $arg["db"] = "对不起，服务器和她女朋友吵架了，能否帮助安抚下，谢啦。";

    exit(a_action_done());
}


//一切正常，可以注册
$arg["err"] = 0;

exit(a_action_done());
