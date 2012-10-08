<?php
/**
 * 对用户操作有关的函数，如注册，删除，更新资料等
 *
 * XXX 文件依赖 XXX
 *	1、ROOT_DIR . "devinc.all.php"
 *
 * */
define('USER_TYPE_TOKEN',       1 << 0);
define('USER_TYPE_NICKNAME',    1 << 1);
define('USER_TYPE_REG',         1 << 2);



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

    //添加到memcache中，存储300秒
    s_memcache($mkey, $user, 300);

    return $user;
}


function user_by_username($username) {
    if (s_bad_string($username)) {
        return false;
    }

    $mkey = "uid_by_username#" . $username;

    if (!( $uid = s_memcache($mkey) )) {
        //用户未注册
        if (!( $uid = s_db_one("select `id` from `%s_user` where `username`='{$username}'") )) {
            return false;
        }

        //添加到memcache中，存储300秒
        s_memcache($mkey, $uid, 300);
    }

    return user_by_id($uid);
}


function user_by_nickname($nickname) {
    if (s_bad_string($nickname)) {
        return false;
    }

    $mkey = "uid_by_nickname#" . $nickname;

    if (!( $uid = s_memcache($mkey) )) {
        //用户未注册
        if (!( $uid = s_db_one("select `id` from `%s_user` where `nickname`='{$nickname}'") )) {
            return false;
        }

        //添加到memcache中，存储300秒
        s_memcache($mkey, $uid, 300);
    }

    return user_by_id($uid);
}

function user_by_token($token) {
    if (s_bad_string($token)) {
        return false;
    }

    $mkey = "uid_by_token#" . $token;

    if (!( $uid = s_memcache($mkey) )) {
        if (!( $uid = s_db_one("select `id` from `%s_user` where `token`='{$token}'") )) {
            //没有此token对应的用户信息
            return false;
        }

        //添加到memcache中，存储300秒
        s_memcache($mkey, $uid, 300);
    }

    return user_by_id($uid);
}


function user_token_from_cookie($setup=false) {
    //查看是用户是否有tooken值
    if (!( $token = s_cookie('TOKEN') )
        && $setup === true
    ) {
        //产生64位随机字符
        $token  = s_string_random(64, true);

        //存储30年
        s_cookie('TOKEN', $token, 1 << 30);
    }

    return $token;
}



//通过token方式创建新用户
function user_create_by_token($token) {
    if (s_bad_string($token)) {
        return false;
    }

    //插入数据库
    $username = $token;
    $nickname = md5($token);

    $data = array();
    $data['token']      = $token;
    $data['username']   = $username;
    $data['nickname']   = $nickname;
    $data['password']   = user_encrypt($username, $username);

    $data['status']     = USER_TYPE_TOKEN;

    $time   = s_action_time();
    $data['tokentime']	= date('Y-m-d H:i:s', $time);
    $data['time']       = $time;

    return s_db('%s_user', s_db('%s_user:insert', $data));
}


//通过nickname方式创建新用户
function user_create_by_nickname($nickname) {
    if (s_bad_string($nickname)) {
        return false;
    }

    //没有指定用户名，随机产生一个
    $username = s_string_random(64, true);

    $data = array();
    $data['token']      = user_token_from_cookie(true);
    $data['username']   = $username;
    $data['nickname']   = $nickname;
    $data['password']   = user_encrypt($username, $username);
    $data['status']     = USER_TYPE_NICKNAME;

    $time = s_action_time();
    $data['nicktime']	= date('Y-m-d H:i:s', $time);
    $data['time']       = $time;

    return s_db('%s_user', s_db('%s_user:insert', $data));
}



//通过注册方式创建新用户
function user_create_by_reg($username, $password, $nickname, $token=false) {
    if (s_bad_string($username)
        || s_bad_string($nickname)
        || s_bad_string($password)
    ) {
        return false;
    }

    //用户已经发表过说说，但未注册
    //获取用户，修改用户名和密码
    if ($token
        && ( $user = user_by_token($token) )
        && ( $user['status'] <= USER_TYPE_TOKEN )
    ) {
        $data['username']   = $username;
        $data['nickname']   = $nickname;
        $data['password']   = user_encrypt($username, $password);
        $data['status']     = USER_TYPE_REG;

        $time = s_action_time();
        $data['regtime']    = date('Y-m-d H:i:s', $time);
        $data['time']       = $time;


        //更新用户的资料
        s_db('%s_user:update', $user, $data);

        return s_db('%s_user', $user['id']);

    } else {
        //token对应的用户已经存在，只需要更新用户名和密码
        $data['token']      = $token ? $token : user_token_from_cookie(true);
        $data['username']   = $username;
        $data['nickname']   = $nickname;
        $data['password']   = user_encrypt($username, $password);
        $data['status']     = USER_TYPE_REG;

        $time = s_action_time();
        $data['regtime']    = date('Y-m-d H:i:s', $time);
        $data['time']       = $time;

        return s_db('%s_user', s_db('%s_user:insert', $data));
    }
}

//更新用户的登录信息
function user_login_by_cookie($update=false) {
    if (!( $cookie = s_cookie_desue() )
        || !( $user = s_db('%s_user', $cookie['uid']) )
    ) {
        return false;
    }

    return $user;
}


//更新用户的登录信息
function user_login_by_token($update=false) {
    if (!( $user = user_by_token(s_cookie('TOKEN') ))
        || $user['token'] != USER_TYPE_TOKEN
    ) {
        return false;
    }

    return $user;
}


//更新用户的登录信息
function user_login($username, $password, $remember=false) {
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


//更新用户的登退出信息
function user_logout($user) {
    if (!( $user = user_by_id($user['uid']) )) {
        return false;
    }

    //更新COOKIE信息
    s_cookie('SUP', '', -1);
    s_cookie('SUE', '', -1);
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


