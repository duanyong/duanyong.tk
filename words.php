<?php
/**
 * 用户发布想说的话
 *
 *  /db/devdb.user.php
 *
 * */

require_once("dev/devapp.config.php");
require_once(ROOT_DIR . "/user/devapi.user.php");

$wrong = array();

if (s_bad_post('nickname', $nickname)) {
    //没有记录标识，发送到公开页面
    $wrong['nickname'] = '';
}


if (s_bad_post('words', $words)) {
    //记录空白
    $wrong['wrods'] = '';
}


//检查用户是否是已注册
if (!( $user = user_autologin() )
    || !( $token = user_token(false) )
) {
    //用户不存在（机器人访问）
    $wrong['message'] = '发布失败';
}

$data = array();

if (!empty($wrong)) {
    //有错误，需要页面提示
    $data['wrong'] = $wrong;

    return s_action_page($data);
}


//检查昵称对应的用户是否存在
if (!( $people = user_by_nickname($nickname) )) {
    //创建一个昵称对应的用户

}


$time = a_action_time();

$data = array();
$data['tear']  = $tear;

$data['token'] = $token;
$data['words'] = $words;
$data['ip']    = a_action_ip();
$data['time']  = $time;
$data['ftime'] = date('Y-m-d H:i:s', $time);


if (false === ( $id = a_db('%s_words:insert', $data) )) {
    return a_action_error('南飞', 501);
}


//更新此token对应的汇总数据
a_action_json(array(
    'error'     => 0,
    'id'        => $id,
));
