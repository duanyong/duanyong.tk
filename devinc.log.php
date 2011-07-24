<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.log.php
//	日志文件，输出格式为
//	[2011-01-01 01:01:01] [notice] user/devapi.user.php:18 Invalid argument supplied for foreach()
//
//
//      E_USER_NOTICE	- 默认。用户生成的 run-time 通知。脚本发现了可能的错误，也有可能在脚本运行正常时发生。
//      E_USER_WARNING	- 非致命的用户生成的 run-time 警告。脚本执行不被中断。
//	E_USER_ERROR	- 致命的用户生成的 run-time 错误。错误无法恢复。脚本执行被中断。
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
ini_set('display_errors', 'Off');



//日志类型
$LEVES = array(
    E_USER_NOTICE   => 'NOTICE'	,
    E_USER_WARNING  => 'WARNNING',
    E_USER_ERROR    => 'ERROR',
);



//打印: []
//2010/12/07 16:00:26 INFO [/login.php]:97 wrong arg

function a_log($log=false) {
    if ($log === false) {
	// 函数的参数调用

	return trigger_error("wrong arg", E_USER_NOTICE) && false;
    }


    if (is_array($log)) {
	$log = var_export($log, true);
    }

    return trigger_error($log, E_USER_NOTICE) && false;
}


function a_warn($log=false) {
    echo $log;

    return false;
}


function a_err(&$log) {
    return trigger_error($log, E_USER_NOTICE) && false;
}


//自定义错误处理函数
//  输入到站点运行时的错误日志/var/nginx/duanyong.running.log
function a_log_hander(&$leve, &$msg) {
    if (isset($leves[$leve])) {

    }

    //提取错误信息，从数组中手提式到
    $tracker = debug_backtrace();

    var_dump($tracker);
}


set_error_handler("a_log_hander");
