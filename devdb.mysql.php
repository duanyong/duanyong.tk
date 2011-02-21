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


// 数据操作
function a_db($table, $v1, $v2=false) {
    if (a_bad_string($table)) {
	return a_log();
    }


    ////////////////////////////////////////////////////////////////////////////////
    // a_db("user", uid)
    // a_db("user:insert", array("uid" => 1, "name" => "张三"))
    // a_db("user:update", array("uid" => 1, "name" => "张三"), array("name" => "duanyong"))
    // a_db("user:delete", uid)
    
    // 对table分拆，得出表名和需要操作的类型
    $pos    = strrpos($table, ":");
    $action = $pos ? substr($table, $pos+1) : false;
    $table  = $pos ? substr($table, 0, $pos) : $table;

    $ret = false;

    if ($action === false) {
	// 按主键返回数据

	if (a_bad_id($v1)) {
	    return l_log();
	}

	$ret = a_db_select($table, $v1);

    } else if ($action === "insert") {
	// 插入数据
	$ret = a_db_insert($table, $v1, $v2);

    } else if ($action === "update") {
	// 更新
	$ret = a_db_update($table, $v1, $v2);

    } else if ($action === "delete") {
	// 删除
    }


    return $ret;
}


// 返回主键对应的数据
function a_db_select($table, $id) {
    if (a_bad_string($table)
	|| a_bad_id($id)
    ) {
	return a_log();
    }

    $pid = substr($table, 0, 1) . "id";
    $sql = "select * from {$table} where {$pid} = {$id}\G";
    echo $sql;

    // 得到一个资源连接后取得对应的数据
    if ( false === ( $ret = a_db_sql($sql) )
	|| false === ( $row = mysql_fetch_row($ret) )
    ) {
	return a_log();
    }

    // 释放资源
    mysql_free_result($ret);

    return $row;
}


// 返回插入数据的其中包括了表主键
function a_db_insert($table, $data) {
    if (a_bad_string($table)
	|| a_bad_array($data)
    ) {
	return a_log();
    }

    $pid = substr($table, 0, 1) . "id";

    if (isset($data[$pid])) {
	// 错误,插入的数据有主键

	return a_log("your data has primary key, plase check id.");
    }


    // 除去重复的值
    $data = array_unique($data);

    // 将$data中的字段折出来(`name`, `age`, `sex`, `account`)
    $arr = array();
    $sql = "insert into `{$table}`";

    foreach (array_keys($data) as $key) {
	$arr[] = $key;
    }

    // (`name`, `age`, `sex`, `accuont`)
    $sql .= ' (`' . implode('`, `', $arr) . '`)';


    // 将$data中的数据组合起来（'duanyong', 12, true, 2456）
    $arr = array();

    foreach (array_values($data) as $value) {
	$arr[] = is_string($value) ? '"' . $value . '"' : $value;
    }

    // ('zhangsan', 22, true, 99)
    $sql .= ' value (' . implode(', ', $arr) . ')';



    if(false === a_db_sql($sql)
	|| false === ($id = mysql_insert_id() )
    ) {
	// 插入失败

	return l_log();
    }

    return a_db_select($table, $id);
}


// 更新数据，其中$v1是原始数据，$v2是需更新的字段，其中不能包括主键
function a_db_update($table, $v1, $v2) {
    if (a_bad_string($table)
	|| a_bad_array($v1)
	|| a_bad_array($v2)
    ) {
	return a_log();
    }

    // 分析$table，得到表主键
    $pid    = "";
    $names  = explode("_", $table);
    foreach ($names as $key) {
	if (a_bad_string($key)) {
	    continue;
	}

	// 把每个单词的首字母拼凑起来组合成主键
	$pid .= substr($key, 0, 1);
    }

    if (empty($pid)) {
	return a_log();
    }

    $pid .= "id";


    $values = array();

    // 防止有重复的值
    $v2 = array_unique($v2);

    // 对$v1和$v2数据归类
    foreach ($v2 as $key => $value) {
	if ($v1[$key] == $v2[$key]) {
	    continue;
	}

	$values[] = "`{$key}`=" . ( is_string($value) ? '"' . $value . '"' : $value );
    }


    $sql = "update `{$table}` set " . implode(", ", $values) . " where {$pid} = {$v1[$pid]}";

    if (false === a_db_sql($sql)) {
	return l_log();
    }

    return a_db_select($table, $v1[$pid]);
}


// 把数据按列表返回
function a_db_list($sql) {
    if (a_bad_string($sql)) {
	return a_log();
    }

    if ( false === ( $ret = a_db_sql($sql) )) {
	return a_warn();
    }


    // 得到资源后，取得对应的数据
    $rows = array();
    while ($row = mysql_fetch_assoc($ret)) {
	$rows[] = $row;
    }

    // 释放资源文件
    mysql_free_result($ret);

    return $rows;
}


 // 采用长连接来连接数据库  
function a_db_conn() {
    global $config;

    if (a_bad_array($config["farm"], $farm)) {
	return f_warn();
    }

    //TODO 优先选择状态好的数据库

    return mysql_pconnect($farm[0], $config["username"], $config["password"]); 
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

    if (false === mysql_select_db($config["database"], $conn)) {
	// 没有数据库

	return f_warn();
    }

    // 设置存取的编码格式。此处用utf8格式
    if (false === mysql_query("SET NAMES 'UTF8'", $conn)) {
	// 设置编码格式有问题

	return f_warn();
    }

    return mysql_query($sql, $conn);
}


