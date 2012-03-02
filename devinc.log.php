<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.log.php
//	日志文件的输出格式为
//	2011-01-01 [duanyong:127.0.0.1] ACTION START: /diary/add
//	    01:01:01.290086 [trace] [sql] select * from diary order by did desc limit 10 [/devinc.mysql.php:14]
//	    01:01:01.290087 [trace] [arg] argument is null [/devinc.mysql.php:15]
//	    01:01:01.290090 [error] [sql] argument is null [/devinc.mysql.php:15]
//
///	2011-01-01 [fengyu:127.0.0.1] ACTION START: /user/add
//	    01:01:02.290086 [trace] [sql] select * from diary order by did desc limit 10 [/devinc.mysql.php:14]
//	    01:01:02.290087 [trace] [arg] argument is null [/devinc.mysql.php:15]
//	    01:01:02.290090 [error] [sql] argument is null [/devinc.mysql.php:15]
//
//
//      E_USER_NOTICE	- 默认。用户生成的 run-time 通知。脚本发现了可能的错误，也有可能在脚本运行正常时发生。
//      E_USER_WARNING	- 非致命的用户生成的 run-time 警告。脚本执行不被中断。
//	    E_USER_ERROR	- 致命的用户生成的 run-time 错误。错误无法恢复。脚本执行被中断。
//
//
//	a_log($log)
//	    输出日志
//
//	a_log_arg($log)
//
//	a_log_sql($log)
//
//	a_log_action($log)
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


//日志文件
$log_file   = "/var/log/nginx/php.log";

//是否开启日志输出（默认开启）
$log_on	    = true;

//是否在cli模式下运行
$log_is_cli = php_sapi_name() === "cli";


//
////////////////////////////////////////////////////////////////////////////////


error_reporting(E_ALL);

ini_set('display_errors', 'Off');


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

//E_USER_NOTICE	- 默认。用户生成的 run-time 通知。脚本发现了可能的错误，也有可能在脚本运行正常时发生。
function a_log($log=false, $level=E_USER_NOTICE) {
    $log = a_log_argument_tostring($log);

    return trigger_error($log, $level) && false;
}


function a_log_action() {
    return trigger_error(null, E_USER_DEPRECATED) && false;
}


function a_log_arg() {
    return trigger_error("warning arg", E_USER_WARNING) && false;
}

function a_log_sql() {
    return trigger_error("warning sql", E_USER_WARNING) && false;
}

function a_error($log) {
    trigger_error($log, E_ERROR) && false;

    //中断脚本执行，返回PHP E_ERROR错误
    exit(E_ERROR);
}


function a_log_argument_tostring(&$arg) {
    if (false === $arg) {
        $arg = "empty log";

    } else if (is_array($arg)) {
        $arg = str_replace("\n", "", var_export($arg));

    } else if (is_bool($arg)) {

        $arg = $arg ? "bool(true)" : "bool(false)";
    }

    return $arg;
}



//自定义日志输出函数
function a_log_hander(&$no, &$log, &$file, &$line, &$context) {
    //因为日志按项目的相对路径输出。如/home/duanyong/workspace/duanyong/login.php 应输出为/login.php
    //debug_backtrace中file属性为绝对路径，需要将绝对路径换成项目的相对路径。

    static $realdir;

    if (!$realdir) {
        if (is_link(ROOT_DIR)) {
            //获取真正的系统路径
            $realdir = readlink(ROOT_DIR);

        } else {
            $realdir = ROOT_DIR;
        }
    }


    $str    = "\n";
    $time   = $_SERVER['REQUEST_TIME'];
    $file   = str_replace($realdir, "", $file);

    //$debug  = a_log_debug_message();


    if ($no === E_USER_DEPRECATED) {
        //2011-01-01 [duanyong:127.0.0.1] ACTION START /diary/add
        $username = isset($_COOKIE["username"]) ? $_COOKIE["username"] : "-";
        $str .= "\n" . date("Y-m-d", $time)                                                                // 2011-01-01
            . " [" . $username . ":" . $_SERVER["REMOTE_ADDR"] . "] ACTION START "                  // [duanyong:127.0.0.1]
            . str_replace(".php", "", $file);                                                       // /diary/add
            //. str_replace(".php", "", $debug["file"]);                                              // /diary/add

    } else {
        //    01:01:01.290086 [trace] [sql] select * from diary order by did desc limit 10 [/devinc.mysql.php:14]
        $desc = null;

        switch ($no) {
        case E_USER_ERROR:
            $desc = "error";
            break;

        case E_USER_WARNING:
            $desc = "warning";
            break;

        case E_USER_NOTICE:
            $desc = "notice";
            break;

        default:
            $desc = "notice";
        }


        // 01:01:01.290086
        $mc = microtime();
        $t  = explode(" ", $mc);
        $t  = substr($t[0], 1, strlen($t[0]));

        $str .= "\t" . date("h:i:s", $time) . $t                // 01:01:01.290086
            . " [" . $desc . "]"                                // [warning]
            . " " . a_log_argument_tostring($log)               // bool(true)
            . " [" . $file . "]:" . $line;                      // [/devinc.mysql.php:14]
            //. " [" . $debug["file"] . "]:" . $debug["line"];    // [/devinc.mysql.php:14]
    }


    //追加到日志文件中
    global $log_file, $log_is_cli, $log_on;

    if ($log_is_cli) {
        $stdout = fopen("php://stdout", "w");
        fwrite($stdout, $str . "\n");
        fclose($stdout);
    }

    if ($log_on) {
        if (!is_writable($log_file)) {
            $stdout = fopen("php://stdout", "w");
            fwrite($stdout, "log file not writable, please check it.\n");
            fclose($stdout);

            return false;
        }

        error_log($str, 3, $log_file);
    }

    return true;
}


function a_log_debug_message() {
    static $log_direction;

    if (!$log_direction) {
        if (is_link(ROOT_DIR)) {
            //获取真正的系统路径
            $log_direction = readlink(ROOT_DIR);

        } else {
            $log_direction = ROOT_DIR;
        }


        static $functions;

        if (!$functions) {
            $functions = array(
                "a_log",
                "a_log_arg",
                "a_log_sql",
                "a_log_action",
            );
        }

        $trace  = array();
        $traces = debug_backtrace();

        //提取错误信息
        foreach ($traces as &$t) {
            if (isset($t["function"])
                && ( $f = $t["function"] )
                && in_array($f, $functions)
            ) {
                $trace["line"]      = $t["line"];
                $trace["file"]      = str_replace($log_direction, "", $t["file"]);
                $trace["function"]  = $f;

                unset($t);
                break;
            }

            unset($t);
        }

        unset($traces);

        return $trace;
    }
}


//采用自定义的日志输出函数
set_error_handler("a_log_hander", E_ALL);
