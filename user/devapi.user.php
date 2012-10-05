<?php
/**
 * 对用户操作有关的函数，如注册，删除，更新资料等
 *
 * XXX 文件依赖 XXX
 *	1、ROOT_DIR . "devinc.all.php"
 *
 * */

function user_by_id($id) {
    if (s_bad_id($id)) {
        return false;
    }

    $mkey = "user_by_id#" . $id;

    if (( $user = s_memcache($mkey) )) {
        return $user;
    }

    //用户未注册
    if (!( $user = s_db_row("select * from `%s_user` where `id`={$id}") )) {
        return false;
    }

    //添加到memcache中，存储30秒
    s_memcache($mkey, $user, 30);

    return $user;
}


function user_by_username($username) {
    if (s_bad_string($username)) {
        return false;
    }

    $mkey = "user_by_username#" . $username;

    if (( $user = s_memcache($mkey) )) {
        return $user;
    }

    //用户未注册
    if (!( $user = s_db_row("select * from `%s_user` where `username`='{$username}'") )) {
        return false;
    }

    //添加到memcache中，存储30秒
    s_memcache($mkey, $user, 30);

    return $user;
}


//创建新用户
function user_create($username, $password, $nickname) {
    if (user_by_username($username)) {
        return false;
    }

    $time = s_action_time();
    $data['ftime']	    = date('Y-m-d H:i:s', $time);
    $data['username']   = $username;
    $data['nickname']   = $nickname;
    $data['password']   = user_encrypt($username, $password);

    // 插入数据
    return s_db('%s_user:insert', $data);
}


//更新用户的登录信息
function user_autologin($update=false) {
    if (!( $user = s_cookie_desue() )) {
        return false;
    }

    if (!$update) {
        $user['nickname'] = $user['nn'];
    }

    return $user;
}


//更新用户的登录信息
function user_login($username, $password, $remember) {
    if (!( $user = user_by_username($username) )
        || ( $user['password'] !== user_encrypt($username, $password) )
    ) {
        return false;
    }

    unset($user['password']);

    //登录成功，更新cookie时间....

    $exp = $remember ? 7 * 86400 : false;

    //更新COOKIE信息
    s_cookie('SUP', s_cookie_sup($user, $exp));
    s_cookie('SUE', s_cookie_sue($user, $exp));

    return $user;
}


//返回用户密码的加密字符串
function user_encrypt($username, $password) {
    if (s_bad_string($username)
        || s_bad_string($password)
        || !( $pki = s_cookie_pki() )

    ) {
        return false;
    }


    //混淆用户名和密码
    $lon = md5($username . $pki) . md5($pki . $password);
    $sht = '~!@#$%^&*()_+[]\|{}";:,./?><';      //手动添加一些特殊字符，增加md5字符集范围


    //以较长的字符串做基准，反序交叉合并字符串
    $new = "";
    $len = mb_strlen($lon);
    $mod = mb_strlen($sht);

    do {
        $new .= mb_substr($lon, $len, 1);

        if (( $tmp = mb_substr($sht, $len, 1) )) {
            //短字符串在pos下标处有值才合并
            $new .= $tmp;
        } else {
            $new .= mb_substr($lon, $len % $mod, 1);
        }

    } while (( -- $len ) >= 0);


    return md5($new);
}


