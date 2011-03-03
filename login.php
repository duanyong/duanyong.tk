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


// 登录逻辑的必要值检查（邮箱、密码）
if (a_bad_ajax()) {
    //非正常提交，直接返回403

    return false;
}

$arg = array();

$user = array(
    "rember"	=> "",
    "mobile"	=> "",
    "password"	=> "",
);




if (a_bad_mobile($_POST["mobile"], $user["mobile"])) {
    $arg["err"]["mobile"] = "邮箱做为登录账号，必须填写。";
}

if (a_bad_string($_POST["password"], $user["password"])) {
    $arg["err"]["password"] = "密码是保护账号的基本手段，必须填写。";
}

if (isset($_POST["rember"])) {
    $user["rember"] = $_POST["rember"] === "1" ? 1 : 0;
}


// 取值完毕


//检查数据库中的值是否存在
if (!( $data = a_user_by_mobile($user["mobile"]) )) {
    // 数据库错误
    $arg["err"]["mobile"] = "您提供的手机号码有问题，请联系管理员。";
}


if (isset($arg["err"])) {
    // 有错误发生，返回错误

    exit(a_action_done());
}


if (md5($user["password"]) !== $data["password"]) {
    $arg["err"] = "输入的密码有误，请重新输入";
}


//一切正常，可以注册
$arg["err"] = 0;


exit(a_action_done());
