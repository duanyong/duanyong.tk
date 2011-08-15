<?php
/**
 * 对用户操作有关的函数，如注册，删除，更新资料等
 *
 * XXX 文件依赖 XXX
 *	1、ROOT_DIR . "devinc.all.php"
 *
 * */




//更新用户的登录信息
function a_user_update_login(&$user) {
    if (f_bad_id($user["uid"], $uid)) {
	return a_log();
    }


}

