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


function a_log(&$log="") {
    echo $log;

    return false;
}


function a_warn(&$log="") {
    echo $log;

    return false;
}


