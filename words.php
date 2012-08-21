<?php
/**
 * 
 *
 *
 * */

require_once('dev/devinc.all.php');


//检查是否第一次
if (!( $tear = a_cookie('__tear') )) {
    //创建新的并记录到用户的COOKIE中
    $tear = a_action_uuid();

    //永不过期
    a_cookie('__tear', $tear, 10);
}


if (a_bad_post('token', $token)) {
    //没有记录标识，发送到公开页面
    $token = '';
}

if (a_bad_post('words', $words)) {
    //记录空白
    $words = '';
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
