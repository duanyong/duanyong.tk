<?php
/**
 * 提供用户写新日记
 *
 * */

require_once(__DIR__ . "/../devinc.all.php");
require_once(ROOT_DIR . "/diary/devapi.diary.php");


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


if (a_bad_0string($_POST["date"], $date)) {
    // 提交的数据有问题，不应该产生这样的情况的
    $arg["err"]["date"] = "写个日期吧";
}

if (a_bad_0string($_POST["gether"], $gether)) {
    // 提交的数据有问题，不应该产生这样的情况的
    $arg["err"]["gether"] = "今天的天气怎么样?";
}



//取值完毕

if (!a_bad_array($arg["err"])) {
    //有错误发生，返回错误

    return a_action_done();
}



//准备存储新用户
$diary = array(
    "date"	=> $date,
    "gether"	=> $gether,
    "content"	=> $content,
);


if (false === (a_diary_add($diary) )) {
    //数据存储发生错误，提示用户

    //报告错误
    $arg["err"]["db"] = "对不起，服务器和她女朋友吵架了，能否帮助安抚下，谢啦。";

    exit(a_action_done());
}


//一切正常，可以注册
$arg["err"] = 0;

exit(a_action_done());

