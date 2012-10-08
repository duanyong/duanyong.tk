<?php
/**
 * 用户发布想说的话
 *
 *  /db/devdb.user.php
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/user/devapi.user.php");


if (s_bad_post('nickname', $nickname)) {
    //没有记录标识，发送到公开页面
    $wrong['nickname'] = '';
}


if (s_bad_post('message', $message)) {
    //记录空白
    $message = "我很想你。。。";
}


//非注册用户非token不予发布说说
if (false === ( $user = user_login_by_cookie() )
    && false === ( $user = user_login_by_token() )
) {
    //用户不存在（机器人访问）
    $wrong['notice'] = '发布失败';
}


//需要传送的用户不存在，创建一个新用户
if (!( $people = user_by_nickname($nickname) )
    && !( $people = user_create_by_nickname($nickname) )
) {
    $wrong['notice'] = '发布失败';
}


if (isset($wrong)) {
    return s_action_page(array(
        'error'     => 10000,
        'message'   => $message,
        'nickname'  => $nickname,
        'wrong'     => $wrong,
    ), 'main.tpl');
}



$time = s_action_time();

$data = array();
$data['sid']    = $user['id'];
$data['tid']    = $people['id'];
$data['words']  = $message;

$data['ip']     = s_action_ip();
$data['time']   = $time;


if (false === ( $id = s_db('%s_words:insert', $data) )) {
    $wrong['notice'] = '发布失败';

    return s_action_page(array(
        'error'     => 10001,
        'message'   => $message,
        'nickname'  => $nickname,
        'wrong'     => $wrong,
    ), 'main.tpl');
}


return s_action_redirect("list.php?nickname={$user['nickname']}");
