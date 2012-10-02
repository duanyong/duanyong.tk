<?php
/**
 *  列出所有消息
 *
 *
 * */

require_once('dev/devinc.all.php');


if (a_bad_get('public', $public, '0int')) {
    //默认公开
    $public = 1;
}


if (a_bad_get('page', $page, 'int')) {
    //默认第一页
    $page = 1;
}


if (a_bad_get('size', $size, 'int')) {
    //默认每页50条
    $size = 50;
}


$pos = ( $page - 1 ) * $size;
$sql = "select `id`, `token`, `words`, `key`, `public` "
    . "from `%s_words` "
    . "where `public`={$public} group by `token` order by `time` desc limit {$pos}, {$size}";

a_action_json(array(
    'error'     => 0,
    'list'      => a_db_list($sql),
));

