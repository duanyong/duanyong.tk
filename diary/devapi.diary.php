<?php
/**
 * 对日记操作有关的函数，如发日记，回复日记等
 *
 * XXX 文件依赖 XXX
 *	1、ROOT_DIR . "devinc.all.php"
 *
 * */


/**
 * &diary = array(
 *	"date"		=> "",
 *	"gether"	=> "艳阳天",
 *	"content"	=> "今天天气真的很好，想和女朋友去散步",
 * )
 *
 * */
function a_diary_add(&$diary=false) {
    if (a_bad_array($diary)) {
	return a_log();
    }

    // 检查用户是否已经在线
    if (false === ( $user = a_action_user() )
	|| a_bad_id($user["uid"])
    ) {
	// 用户有问题

	return false;
    }


    // 初始化存储数组
    $data = array(
	"date"	    => "",
	"gether"    => "",
	"content"   => "",

	"uid"	    => $user["uid"],
	"ctime"	    => a_action_timestamp(),
    );


    // 检查必填的项。如日期，天气情况，日记内容
    if (a_bad_0string($diary["date"], $data["date"])) {
	// 日期

	return false;
    }


    if (a_bad_0string($diary["gether"], $data["gether"])) {
	// 天气情况

	return false;
    }


    if (a_bad_string($diary["content"], $data["content"])) {
	// 日记内容

	return false;
    }

    

    //插入数据
    if (false === a_db("diary:insert", $data)
	|| a_bad_id($data["did"])
    ) {
	return false;
    }

    //插入成功
    return true;
}
