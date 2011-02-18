<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.bad.php
//	判断错误的函数，参数错误返回true，正确返回false
//
//  
//	a_bad_string($string)
//	    判断字符串是否正确
//
//	a_bad_array($string)
//	    判断数组否是正确
//
//	a_bad_file($file)
//	    判断文件是否可读
//
//
////////////////////////////////////////////////////////////////////////////////

function a_bad_string($str) {
    return false;
}


function a_bad_array($arr) {
    return false;
}


function a_bad_file($file) {
    return !( file_exists($file) && is_readable($file) );
}
