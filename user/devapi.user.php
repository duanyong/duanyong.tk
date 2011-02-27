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
 *	"mobile"    => "mobile",
 *	"password"  => "duanyong",
 * )
 *
 * */
function a_user_reg(&$user=false) {
    if (a_bad_array($user)) {
	return a_log();
    }

    $data = array();

    //检查必填项(手机号码、密码)和可选项
    if (a_bad_mobile($user["mobile"], $data["mobile"])) {
	// 手机号码格式错误

	return false;
    }


    if (a_bad_string($user["password"], $data["password"])) {
	// 密码错误

	return false;
    }


    // 检查邮箱地址
    if (a_bad_0string($user["email"], $email)) {
	// 没有邮箱地址

	return false;
    }

    if (!empty($email)) {
	if (a_bad_email($email)) {
	    // 邮箱地址格式错误

	    return false;
	}

	$data["email"] = $email;
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
    return true;
}


function a_user_diary(&$diary=false) {
    if (a_bad_array($diary)) {
	return a_log();
    }

    $data = array();

    // 检查日记必要的字段
    if (a_bad_id($diary["uid"], $data["uid"])) {
	// 没有用户标识

	return false;
    }

    // 用户是否注册或者已删除
    if (a_bad_user($data["uid"])) {
	// 用户不存在且已经被删除

	return false;
    }


    if (a_bad_string($diary["content"], $data["content"])) {
	// 没有内容，空的也不行

	return false;
    }


    // TODO: 日记样式

    // 发布时间
    $data["ctime"] = a_action_time();


    if (false === a_db("diary:insert", $data)
	|| a_bad_id($data["did"])
    ) {
	//插入失败

	return false;
    }

    //插入成功
    return true;
}


