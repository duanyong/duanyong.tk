<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.mysql.php
//	数据库mysql相关的操作
//	    主要功能是提供符合mysql的连接池，断开以及如何执行sql语句的接口
//
//	
//	a_mysql_conn()
//	    将数据合并到模板中，供模板输出
//
//
////////////////////////////////////////////////////////////////////////////////



// 把数据按列表返回
function a_db_list($sql) {
    if (a_bad_string($sql)
	|| false === a_db_test()
    ) {
	return a_log();
    }


    if ( false === ( $ret = a_db_sql($sql) )) {
	return a_warn();
    }

    // echo mysql_client_encoding();

    // echo var_dump(var_export($ret, true));
    echo mysql_info();
}


 // 连接到数据库  
function a_db_conn() {
    global $config;

    return mysql_connect($config["farm"], $config["username"], $config["password"]); 
}


// 测试并选中数据库
function a_db_test() {
    global $config;

    if (false === ( $conn = a_db_conn() )
	|| false === ( mysql_select_db($config["database"], $conn) )
    ) {
	return a_warn();
    }

    return true;
}


// 执行sql语句
function a_db_sql($sql) {
    if (a_bad_string($sql)) {
	return a_log();
    }

    global $config;

    if (false === ( $conn = a_db_conn() )) {
	// 数据库错误

	return a_warn();
    }

    echo $sql;
    // 取得数据，不用关闭连接。让连接池来管理是否关闭连接

    return mysql_query($sql, $conn);
}


