<?php
/**
 * 对留言操作有关的函数，如注册，删除，更新资料等
 *
 * XXX 文件依赖 XXX
 *
 * */

function words_list_by_sender($nickname, $page=1, $size=20) {
    if (s_bad_string($nickname)
        || s_bad_id($page)
        || s_bad_id($size)
        || !( $user = user_by_nickname($nickname) )
    ) {
        return false;
    }

    $mkey = "words_list_by_sender#nickname={$nickname}&page={$page}&size={$size}";

    if (( $list = s_memcache($mkey) )) {
        return $list;
    }

    //返回列表
    $pos = ( $page - 1 ) * $size;

    if (!( $list = s_db_list("select * from `%s_words` where `sid`={$user['id']} order by `time` desc limit {$pos}, {$size}") )) {
        return false;
    }

    //添加到memcache中，存储300秒
    s_memcache($mkey, $list, 300);

    return $list;
}


function words_list_by_receiver($nickname, $page=1, $size=20) {
    if (s_bad_string($nickname)
        || s_bad_id($page)
        || s_bad_id($size)
        || !( $user = user_by_nickname($nickname) )
    ) {
        return false;
    }

    $mkey = "words_list_by_receiver#nickname={$nickname}&page={$page}&size={$size}";

    if (( $list = s_memcache($mkey) )) {
        return $list;
    }

    //返回列表
    $pos = ( $page - 1 ) * $size;

    if (!( $list = s_db_list("select * from `%s_words` where `tid`={$user['id']} order by `time` desc limit {$pos}, {$size}") )) {
        return false;
    }

    //添加到memcache中，存储300秒
    s_memcache($mkey, $list, 300);

    return $list;
}
