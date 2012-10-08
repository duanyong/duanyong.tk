<?php
/**
 *  列出用户的消息所有消息
 *
 *
 * */

require_once("../dev/devapp.config.php");
require_once(ROOT_DIR . "/user/devapi.user.php");
require_once(ROOT_DIR . "/words/devapi.words.php");


if (false === ( $user = user_login_by_cookie() )
    && false === ( $user = user_login_by_token() )
) {
    //用户不存在（机器人访问）
    $wrong['notice'] = '发布失败';
}

if (s_bad_get('page', $page, 'int')) {
    //默认第一页
    $page = 1;
}


if (s_bad_get('size', $size, 'int')) {
    //默认每页20条
    $size = 20;
}


if (!( $list = words_list_by_sender($user['nickname'], $page, $size) )) {
    //未获取查找的用户
    $wrong['notice'] = '未找到用户的留言，请重新查找';

    return s_action_page(array(
        'error'     => 10000,
        'nickname'  => $nickname,
        'wrong'     => $wrong,
    ), 'search.tpl');
}



s_action_page(array(
    'error'     => 0,
    'list'      => $list,
));
