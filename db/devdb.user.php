<?php
////////////////////////////////////////////////////////////////////////////////
// devdb.user.php
//	数据表user对应的操作函数
//
//
//
////////////////////////////////////////////////////////////////////////////////

function a_user_by_email($email) {
    if (a_bad_email($email)) {
	return a_log();
    }

    return a_db_sql("select * from user where `email`='{$email}' limit 1;");
}

