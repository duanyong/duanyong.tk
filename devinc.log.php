<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.log.php
//	日志文件，输出格式为
//	[2011-01-01 01:01:01] [notice] user/devapi.user.php:18 Invalid argument supplied for foreach()
//
//
//	a_log(&$log)
//	    输出日志
//
//	a_warn(&$log)
//	    输出日志（这个错误很严重）
//
//
////////////////////////////////////////////////////////////////////////////////


error_reporting(E_ALL);
ini_set('display_errors','On');


function a_log($log=false) {
    if ($log === false) {
	// 函数的参数调用
	//TODO***
	return trigger_error("wrong arg...") && false;
    }

    return trigger_error($log) && false;
}


function a_warn($log=false) {
    echo $log;

    return false;
}
