<?php
/**
 * 对用户操作有关的函数，如注册，删除，更新资料等
 *
 * XXX 文件依赖 XXX
 *	1、ROOT_DIR . "devinc.all.php"
 *
 * */


function a_user_reg(&$user=false) {
    if (a_bad_array($user)) {
	return a_log();
    }

    $data = array();

    //检查必填项(用户名、密码)和可选项
    if (a_bad_email($user["email"], $data["email"])) {
	// 邮箱地址错误

	return false;
    }

    if (a_bad_string($user["password"], $data["password"])) {
	// 密码错误

	return false;
    }


    //用户请求时间
    $data["ctime"] = a_action_time();

    //用户请求IP
    $data["regip"] = a_action_ip();


    //插入数据
    return a_db("user:insert", $data);
}
