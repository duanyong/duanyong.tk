<?php
////////////////////////////////////////////////////////////////////////////////
// devdb.user.php
//	数据表user对应的操作函数
//
//
//
////////////////////////////////////////////////////////////////////////////////

function a_user_by_username($username) {
    if (a_bad_username($username)) {
	    return a_log();
    }

    return a_db_query("select * from `user` where `username`='{$username}' limit 1;");
}
