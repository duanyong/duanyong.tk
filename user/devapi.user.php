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
    if (a_bad_id($user["uid"], $uid)) {
        return a_log();
    }

}


//查看浏览器cookie中取得uid，不检查是否与密码匹配
function a_user_logined($uid=false, $redirect="/login") {
    if ($uid === false) {
        //get uid from cookie
        if (false === ($uid = a_cookie_uid()) ) {
            return false;
        }
    }

    $logined = false;

    if (!a_bad_id($uid)
        && ( $user = a_db("user", $uid) )
        && a_cookie_password() === md5($user["password"] . $user["last_login"])
    ) {
        $logined = true;
    }


    return $logined ? $user : false;
}
