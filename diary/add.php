<?php
/**
 * 提供用户发布日记
 *
 * */

require_once(__DIR__ . "/devinc.all.php");
require_once(ROOT_DIR . "/user/devapi.diary.php");


// 登录逻辑的必要值检查（日记内容）
if (a_bad_ajax()) {
    //非正常提交，直接返回403

    return false;
}

global $arg;

if (a_bad_user(false, $user)) {
    $arg["err"]["form"] = "您可能没有登陆，请登陆后再写日记";
}


if (a_bad_mobile($_POST["content"], $content)) {
    $arg["err"]["content"] = "请写点字儿再提交吧";
}


if (a_bad_0string($_POST["email"], $email)) {
    $arg["err"]["email"] = "没有邮箱地址。";
}


//取值完毕

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

