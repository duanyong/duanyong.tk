<?php
/**
 * 对用户操作有关的函数，如注册，删除，更新资料等
 *
 * XXX 文件依赖 XXX
 *	1、ROOT_DIR . "devinc.all.php"
 *
 * */


/**
 * &user = array(
 *	"sex"	    => true,
 *	"mobile"    => "15888888888",
 *	"password"  => "duanyong",
 * )
 *
 * */
function a_user_reg(&$user=false) {
    if (a_bad_array($user)) {
	return a_log();
    }

    // 表单的数据项
    $data = array(
	"sex"	    => "",
	"mobile"    => "",
	"password"  => "",
    );

    //检查必填项(手机号码、密码)和可选项
    if (a_bad_mobile($user["mobile"], $data["mobile"])) {
	// 手机号码格式错误

	return false;
    }


    if (a_bad_string($user["password"], $data["password"])) {
	// 密码错误

	return false;
    }


    // 检查性别
    if (a_bad_0id($user["sex"], $data["sex"])) {
	// 没有邮箱地址

	return false;
    }


    //用户请求时间
    $data["ctime"] = a_action_timestamp();

    //用户请求IP
    $data["regip"] = a_action_ip();


    //插入数据
    if (false === a_db("user:insert", $data)
	|| a_bad_id($data["uid"])
    ) {
	return false;
    }

    //插入成功
    return $data;
}
