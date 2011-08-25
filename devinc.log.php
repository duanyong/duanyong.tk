<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.log.php
//	日志文件的输出格式为
//	2011-01-01 01:01:01 NOTICE [/user/devapi.user.php:18] wrong arg
//
//
//      E_USER_NOTICE	- 默认。用户生成的 run-time 通知。脚本发现了可能的错误，也有可能在脚本运行正常时发生。
//      E_USER_WARNING	- 非致命的用户生成的 run-time 警告。脚本执行不被中断。
//	E_USER_ERROR	- 致命的用户生成的 run-time 错误。错误无法恢复。脚本执行被中断。
//
//
//	a_log($log)
//	    输出日志
//
//	a_warn($log)
//	    输出错误
//
//	a_error($log)
//	    中断执行
//
//	a_log_on()
//	    开启日志输出
//
//	a_log_off()
//	    关闭日志输出
//
//
////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////

error_reporting(E_ALL);

ini_set('display_errors', 'Off');


//日志文件
$log_file   = "/var/log/nginx/duanyong.tk.log";

//是否开启日志输出（默认开启）
$log_on	    = true;

//是否在cli模式下运行
$log_is_cli = php_sapi_name() === "cli";

//日志等级
$log_leves  = array(
    E_USER_NOTICE   => 'NOTICE',
    E_USER_WARNING  => 'WARNNG',
    E_USER_ERROR    => 'ERROOR',
);


//
////////////////////////////////////////////////////////////////////////////////


//E_USER_NOTICE	- 默认。用户生成的 run-time 通知。脚本发现了可能的错误，也有可能在脚本运行正常时发生。
function a_log($log=false) {
    if ($log === false) {
	//函数的参数调用

	return trigger_error("wrong arg", E_USER_WARNING) && false;
    }


    if (is_array($log)) {
	$log = var_export($log, true);
    }

    return trigger_error($log, E_USER_NOTICE) && false;
}



//E_USER_WARNING - 非致命的用户生成的 run-time 警告。脚本执行不被中断。
function a_warn($log=false) {
    return trigger_error($log, E_USER_WARNING) && false;
}



//E_USER_ERROR - 致命的用户生成的 run-time 错误。错误无法恢复。脚本执行被中断。
function a_error($log) {
    trigger_error($log, E_USER_ERROR) && false;

    //中断脚本执行，返回PHP E_ERROR错误
    exit(E_ERROR);
}


//开启日志输出
function a_log_on() {
    global $log_on;

    return $log_on = true;
}


//关闭日志输出
function a_log_off() {
    global $log_on;

    return !( $log_on = false );
}


//追加日志到文件中。如果是cli模式，输出到标准输出
function a_log_append(&$msg) {
    global $log_file, $log_is_cli, $log_on;

    if ($log_is_cli) {
	fwrite(STDOUT, "\n" . $msg);
    }

    if ($log_on) {
	error_log("\n" . $msg, 3, $log_file);
    }
}


//自定义日志输出函数
function a_log_hander(&$leve, &$msg) {
    //对句暴露的日志函数
    static $names = null;

    if (!$names) {
	$names = array(
	    'a_log',
	    'a_warn',
	    'a_error',
	);
    }



    //因为日志按项目的相对路径输出。如/home/duanyong/workspace/duanyong/login.php 应输出为/login.php
    //debug_backtrace中file属性为绝对路径，需要将绝对路径换成项目的相对路径。
    static $pdir = null;

    if (!$pdir) {
	if (is_link(ROOT_DIR)) {
	    //获取真正的系统路径
	    $pdir = readlink(ROOT_DIR);

	} else {
	    //出错
	    $pdir = ROOT_DIR;
	}
    }

    global $log_leves;
    if (!isset($log_leves[$leve])) {
	return a_log('This [' . $leve . '] unavail log leves');
    }


    //2011-01-01 01:01:01 NOTICE [/user/devapi.user.php:18] warn arg
    $log = date('Y-m-d h:i:s') .' ' . $log_leves[$leve];


    $traces = debug_backtrace();

    //提取错误信息，从数组中提取函数名为a_log, a_warn, a_error得到文件的名和出错行数进行输出
    foreach ($traces as &$trace) {
	if (isset($trace["function"])
	    && ( $name = $trace["function"] )
	    && in_array($name, $names)
	) {
	    //打印track中的语句

	    $log .= ' [' .  str_replace($pdir, "", $trace['file']) . ':' . $trace['line'] . ']';

	    break;
	}

	unset($track);
    }

    $log .= ' ' . $msg;

    //追加到日志文件中
    a_log_append($log);

    return true;
}


//采用自定义的日志输出函数
set_error_handler("a_log_hander");
