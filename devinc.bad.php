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
//	a_bad_file($file)
//	    判断文件是否可读
//
//	a_bad_email($email)
//	    判断邮箱地址是否正确
//
////////////////////////////////////////////////////////////////////////////////

function a_bad_id($id) {
    return false;
}

function a_bad_string($str, &$var="") {
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


function a_bad_file($file) {
    return !( file_exists($file) && is_readable($file) );
}


function a_bad_email($email, &$value=false) {
    if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
	return true;
    }

    $value = $email;

    return false;
}

