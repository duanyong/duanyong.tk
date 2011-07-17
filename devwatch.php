<?php
////////////////////////////////////////////////////////////////////////////////
// 监控css和js文件的变化并生成对应的文件
//  将多个文件粘合在一起，如dev.base.form.js 生成dev.base.js文件
//
//
////////////////////////////////////////////////////////////////////////////////

require_once(__DIR__ . "/devinc.all.php");

$pid_file = "/tmp/devwatch.pid";

define("JS_DIR", ROOT_DIR . "/js/");
define("CSS_DIR", ROOT_DIR . "/css/");


//文件的依赖关系列表
//  array(
//	"/var/www/duanyong/js/base.js" => array(
//	    "/var/www/duanyong/index.tpl",
//	    "/var/www/duanyong/reg.tpl",
//	    "/var/www/duanyong/login.tpl",
//	),
//	"/var/www/duanyong/css/base.css" => array(
//	    "/var/www/duanyong/reg.tpl",
//	    "/var/www/duanyong/login.tpl",
//	    "/var/www/duanyong/index.tpl",
//	),
//  )
//
//	数组的键为被依赖的文件，值为数组，表示依赖的文件列表
//	当被依赖文件发生改变后，会生成对应的js、css、tpl文件
//	然后再去通知依赖的文件已发生改变，通知后有下面几种情况
//	    被通知是tpl，且不为静态文件（devwatch:xxxx）不做任何处理
//	    被通知是tpl，且是一静态文件（devwatch:xxxx），再次生成对应类型的文件
//	    //TODO: 以下未实现，逻辑还未清楚
//	    被通知是css，且不为静态文件（devwatch:css），不做任何处理
//	    被通知是css，且是一静态文件（devwatch:css），重新生成css文件
//	    被通知是js，且不为静态文件（devwatch:js），不做任何处理
//	    被通知是js，且是一静态文件（devwatch:js），重新生成js文件
//
$depends = array();


function a_devwatch_init() {
    if (is_file($pid_file)
    ) {
	return a_log("daemon is running for /tmp/devwatch.pid");
    }

    global $depends;

    $files = array();

    a_devwatch_eachfile(ROOT_DIR, $files);

    foreach ($files as $file) {
	if (a_devwatch_filter($file)) {
	     a_devwatch_depend($file, $depends);
	}
    }

    var_dump($depends);
}



//不遍历的目录
$no_each_dir = array();
$no_each_dir[] = ROOT_DIR . "/img";
$no_each_dir[] = ROOT_DIR . "/dev";


//不遍历的文件
$no_each_file = array(
    "php",
    "html",
    "shtml",
);

//遍历目录下非隐藏的所有的文件
function a_devwatch_eachfile($dir, &$files) {
    if (!is_dir($dir)
	|| !( $handle = @opendir($dir) )
    ) {
	return a_log();
    }

    //检查目录是否在不需要遍历
    global $no_each_dir;
    global $no_each_file;

    if (!( $info = pathinfo($dir))
	|| in_array($info["dirname"] . "/" . $info["filename"], $no_each_dir)
    ) {
	return ;
    }

    //得到每个文件
    while (( $file = readdir($handle) )) {
	if (strpos($file, ".") === 0) {
	    //以.开始的隐藏咯过，包括.和..
	    continue;
	}

	if (is_dir($file)) {
	    a_devwatch_eachfile($dir . "/" . $file, $files);

	} else {
	    $info = pathinfo($file);

	    if (!isset($info["extension"])
		|| !in_array($info["extension"], $no_each_file)
	    ) {
		$files[] = $dir . "/" . $file;
	    }
	}
    }

    @closedir($dir);
}



//排除不需要监听的文件
//  1、隐藏文件或者隐藏目录
//  2、不以dev开头的js和css文件
function a_devwatch_filter($file) {
    $prefix	= null;
    $postfix	= null;
    $basename	= null;

    if (!is_file($file)
	|| !( $basename = basename($file) )
	|| !( $postfix	= substr($basename, strrpos($basename, ".") + 1, strlen($basename)) )
	|| !( $prefix	= substr($basename, 0, strpos($basename, ".")) )

    ) {
	return false;
    }

    //file name: dev.xx.js|css or file end line has {devwatch:xxxx}
    if ($prefix !== "dev"
	&& $postfix !== "tpl"
	&& a_devwatch_static($file) === false
    ) {
	return false;
    }

    return true;
}


//检查文件是否以{devwatch:xxxx}结尾
function a_devwatch_static($file) {
    if (! ( $filesize = filesize($file) )
	|| $filesize > 3 * 1024 * 1204
    ) {
	return a_log("file to big.");
    }


    if (( $handle = @fopen($file, "r") )) {
	//移动到{devwatch:}前
	$pos    = 0;
	$index  = false;
	$chars;

	do {
	    $pos += 256;

	    if ($pos > $filesize) {
		$pos = $filesize;
	    }

	    if (fseek($handle, $pos * -1, SEEK_END) !== 0) {
		//设置文件指针出错
		$index = false;

		break;
	    }

	    //在字针处取最到文件末尾
	    $chars = fread($handle, $pos);
	    $index = strrpos($chars, "\n", -2);

	    if ($index === false
		&& $pos === $filesize
	    ) {
		//整行都没有找到换行符说明整个文件都没有换行

		break;
	    }

	} while ($index === false);


	@fclose($handle);

	//取到{}之间的内容，是否符合{devwatch: xxxx}的格式 
	if ( $index === false
	    || !( $chars = substr($chars, $index) )
	    || !( preg_match("/\{devwatch\:[ |\S]+\}/", $chars) )
	) {

	    //不是静态文件
	    return false;
	}

	//符合devwatch字符串匹配
	return true;
    }

    //文件打开出错
    return false;
}


//分析文件的依赖关系
//  文件形成依赖关系有下面的语句产生
//	1、{js name="xxxx, yyyy"}	依赖js文件
//	2、{css name="xxxx, yyyy"}	依赖css文件
//	3、{include file="/xxxx.tpl"}	依赖xxxx.tpl文件
function a_devwatch_depend($file, &$depends) {
    if (!is_array($depends)
	|| !is_file($file)
	|| !is_readable($file)
	|| !( $handle = @fopen($file, "r") )
    ) {
	return a_log("not file or depend not array object");
    }

    echo "depend:" . $file . "\n";

    $regs   = null;
    $line   = null;
    $token  = null;

    //得到每个文件
    while (!feof($handle)) {
	if (( $line = fgets($handle) )) {

	    //当前行是否有依赖语句
	    if (preg_match("/\{js name\=\"(\S+[,| |\S+]*)\"\}/", $line, $regs)) {
		//是{js name="xxxx, yyyy, zzzz"}
		a_devwatch_depend_js($regs[1], $file, $depends);

	    } else if (preg_match("/\{css name\=\"(\S+[,| |\S+]*)\"\}/", $line, $regs)) {
		//是{css name="xxxx, yyyy, zzzz"}
		a_devwatch_depend_css($regs[1], $file, $depends);

	    } else if (preg_match("/\{include file\=\"[ ]*(\S+\.tpl)\"\}/", $line, $regs)) {
		//是{include file="" $date=20091011}
		a_devwatch_depend_include($regs[1], $file, $depends);
	    }
	}
    }

    @closedir($dir);
}


//分析{js name="xxxx, yyyy, zzzz"}，并形成依赖关系
function a_devwatch_depend_js($keys, &$file, &$depends) {
    if (a_bad_string($keys)) {
	return a_log();
    }

    $keys = str_replace(" ", "", $keys);
    $keys = explode(",", $keys);

    foreach ($keys as $key) {
	$key = JS_DIR . $key . ".js";

	if (!isset($depends[$key])) {
	    $depends[$key] = array();
	}

	$depends[$key][] = $file;
    }
}

//分析{css name="xxxx, yyyy, zzzz"}，并形成依赖关系
function a_devwatch_depend_css($keys, &$file, &$depends) {
    if (a_bad_string($keys)) {
	return a_log();
    }

    $keys = str_replace(" ", "", $keys);
    $keys = explode(",", $keys);

    foreach ($keys as $key) {
	$key = CSS_DIR . $key . ".css";

	if (!isset($depends[$key])) {
	    $depends[$key] = array();
	}

	$depends[$key][] = $file;
    }
}


//分析{include file="/xxxx.tpl"}，并形成依赖关系
function a_devwatch_depend_include($inc, &$file, &$depends) {
    if (a_bad_string($inc)) {
	return a_log();
    }

    $inc = str_replace(" ", "", $inc);

    if (strpos($inc, "/") !== 0) {
	//处理{include file="diary.tpl"}
	//找到diary.tpl的上级目录，然后与ROOT_DIR合并
	$info	= pathinfo($file);
	$info	= str_replace(ROOT_DIR, "", $info["dirname"]);

	$inc	= $info . "/" . $inc;
    }


    $inc = ROOT_DIR . $inc;

    if (!isset($depends[$inc])) {
	$depends[$inc] = array();
    }

    $depends[$inc][] = $file;
}



//在ROOT_DIR/js目录生成需要的js文件
function a_watch_general_js($js) {
    if (a_bad_string($js)) {
	return a_log();
    }

    $all    = scandir(JS_DIR);
    $regx   = "/^dev\.{$js}[.|\S]*\.js$/";
    $path   = JS_DIR . $js . ".js";

    $files  = array();
    $first  = JS_DIR . "dev." . $js . ".js";

    foreach ($all as $key => $file) {
	if (strpos($file, ".") === 0
	    || is_dir($file)
	    || !preg_match($regx, $file)
	    || $first === $file
	) {
	    //隐藏文件不管
	    continue;
	}

	$files[$key] = JS_DIR . $file;
    }

    //dev.xxxx.js类似的根文件永远处于第一位
    $files[0] = $first;


    //清除以前可能存在的文件
    file_put_contents($path, "//{$js}.js\n//The Love, The Lover, Thangs.\n//General at " . date("Y-m-d H:i:s"));

    foreach ($files as $file) {
	if (!is_readable($file)) {
	    continue;
	}

	file_put_contents($path, "\n\n\n" . file_get_contents($file), FILE_APPEND);
    }
}


//在ROOT_DIR/css目录生成需要的css文件
function a_watch_general_css($css) {
    if (a_bad_string($css)) {
	return a_log();
    }


    $all    = scandir(CSS_DIR);
    $regx   = "/^dev\.{$css}[.|\S]*\.css/";
    $path   = CSS_DIR . $css . ".css";

    $files  = array();
    $first  = CSS_DIR . "dev." . $css . ".css";

    foreach ($all as $key => $file) {
	if (strpos($file, ".") === 0
	    || is_dir($file)
	    || !preg_match($regx, $file)
	    || $first === $file
	) {
	    //隐藏文件不管
	    continue;
	}

	$files[$key] = CSS_DIR . $file;
    }

    //dev.xxxx.css类似的根文件永远处于第一位
    $files[0] = $first;


    //清除以前可能存在的文件
    file_put_contents($path, "/* {$css}.css */\n/* The Love, The Lover, Thangs. */\n/* General at " . date("Y-m-d H:i:s") . " */");

    foreach ($files as $file) {
	if (!is_readable($file)) {
	    continue;
	}

	file_put_contents($path, "\n\n\n" . file_get_contents($file), FILE_APPEND);
    }
}


// 生成守护进程
function a_daemonize() {
    global $pid_file;

    // 查看执行的结果
    $pid = pcntl_fork();

    if ($pid < 0 ) {
	// 执行失败
	return false;

    } else if ($pid) {

	// 在父进程中，一秒后醒过来
	usleep(1);

	// 退出父进程
	exit(0);
    }


    // 得到程序的ID
    if (( $sid = posix_setsid() ) < 1) {
	// 获取执行环境失败
	return false;
    }

    file_put_contents($pid_file, $pid);
    file_put_contents("/tmp/log","sleep: {$pid}, {$sid}\n", FILE_APPEND);

    usleep(1);

    // 关闭各终端
    if (defined('STDIN')) {
	fclose(STDIN);
    }

    if (defined('STDOUT')){
	fclose(STDOUT);
    }

    if (defined('STDERR')) {
	fclose(STDERR);
    }
}


if (false === a_daemonize())  {
    // 守护进程失败，删除pid
    global $pid_file;

    @unlink($pid_file);
}


register_shutdown_function("f_daemon_stop");

//a_devwatch_init();
