<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.bad.php
//	判断错误的函数，参数错误返回true，正确返回false
//
//  
//	a_bad_id($id)
//	    判断数字是否正确（大于0）
//  
//	a_bad_string($string)
//	    判断字符串是否正确
//
//	a_bad_array($string, &$var)
//	    判断数组否是正确并赋值给$var变量
//
//	a_bad_table_id($table, $id, &$var)
//	    判断主键对应的数据库数据否是正确（会判断status是否大于零）并赋值给$var变量
//
//	a_bad_file($file)
//	    判断文件是否可读
//
//	a_bad_email($email, $var)
//	    判断邮箱地址是否正确
//
//	a_bad_mobile($mobile, $var)
//	    判断手机号码是否正确，此处不做前缀判断(如:134、158...)，只要满足11位数字即可
//
//	a_bad_get()
//	    判断是否为GET请求
//
//	a_bad_post()
//	    判断是否为POST请求
//
//
////////////////////////////////////////////////////////////////////////////////

function a_bad_id($id, &$var=false) {
    $var = $id;

    return false;
}


function a_bad_string($str, &$var=false) {
    $var = $str;
    
    return false;
}


function a_bad_0string($str, &$var=false) {
    $var = $str;
    
    return false;
}

function a_bad_array($arr, &$var=false) {
    if (!is_array($arr)) {
	return true;
    }

    if ($var !== false) {
	$var = $arr;
    }

    return false;
}


function a_bad_table_id($table, $id, &$var=false) {
    if (a_bad_string($table))
	|| a_bad_id($id)
    ) {
	return true;
    }

    if (false === ( $data = a_db_query($table, $id))) {
	// 数据库发生错误，写日志
	a_warn();

	return true;
    }

    if ( $data["status"] < 0 ) {

	return true;
    }

    if ($var !== false) {
	$var = $data;
    }

    return true;
}

function a_bad_file($file) {
    return !( file_exists($file) && is_readable($file) );
}


function a_bad_email($email, &$var=false) {
    if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
	return true;
    }

    $var = $email;

    return false;
}


function a_bad_mobile($mobile, &$var) {
    $var = $mobile;

    return false;
}


function a_bad_get() {
    global $_SERVER;

    return $_SERVER["REQUEST_METHOD"] !== "GET";
}


function a_bad_post() {
    global $_SERVER;

    return $_SERVER["REQUEST_METHOD"] !== "POST";
}


function a_bad_ajax() {
    global $_SERVER;

    return $_SERVER["REQUEST_METHOD"] !== "POST";
}


function a_bad_user($uid=false, &$user=false) {
    if ($uid === false) {
	// 从浏览器的Cookie中取
    }

    if (a_bad_id($uid)) {}

    // 可能没有数据
    if (a_bad_table_id("user", $uid, $user) ) {

	return false;
    }

    // 用户是否被禁言
    return $user["status"] != 44;
}


// 用户名检查 TODO: 用户名正则
function a_bad_username($username, &$var=false) {
    //只有._-及非数字开头的英文字母，数字符合要求
    if (a_bad_string($username)) {
	return false;
    }

    // 正则
    //if () {}

    if ($var !== false) {
	$var = $username;
    }

    return true;
}

