<?php
////////////////////////////////////////////////////////////////////////////////
// 监控css和js文件的变化并生成对应的文件
//  将多个文件粘合在一起，如dev.base.form.js 生成dev.base.js文件
//
//
////////////////////////////////////////////////////////////////////////////////

require_once(__DIR__ . "/devinc.all.php");
require_once(__DIR__ . "/devinc.tracker.php");

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


function a_devwatch_init(&$depends) {
    if (is_file("/var/tmp/devwatch.pid")) {
	return a_log("daemon is running for /tmp/devwatch.pid");
    }

    a_devwatch_depend_files($depends);
}


//返回目录下所有具有依赖关系的文件(dev.*.js, dev.*.css, tpl)
function a_devwatch_depend_files(&$depends) {
    //得到所有的tpl文件
    $js  = glob(JS_DIR . "dev.*.js");
    $css = glob(CSS_DIR . "dev.*.css");
    $tpl = a_devwatch_depend_tpl(ROOT_DIR);


    //分析所有可能产生依赖的文件，确立其依赖关系
    foreach (array_merge($js, $css, $tpl) as $file) {
	if (pathinfo($file, PATHINFO_EXTENSION) !== "tpl"
	    && !( $chars = file_get_contents($file) )
	    && !( preg_match("/\{\*devwatch\:[ |\S]+\*\}/", $chars) )
	) {
	    //非tpl文件且文件中没有{devwatch:xxxx}指令不需要分析其依赖关系
	    continue;
	}

	a_devwatch_depend($file, $depends);
    }
}


//返回项目可能产生依赖关系的tpl文件
function a_devwatch_depend_tpl($dir) {
    if (!is_dir($dir)) {
	return a_log();
    }

    $ret = array();

    //不需要分析依赖关系的目录
    static $ignore_dir = array();
    $ignore_dir[] = ROOT_DIR . "/img";
    $ignore_dir[] = ROOT_DIR . "/dev";

    //取得所有的tpl文件
    foreach (glob($dir . "/*") as $file) {
	if (is_dir($file)
	    && !in_array($file, $ignore_dir)
	) {
	    $ret = array_merge($ret, a_devwatch_depend_tpl($file));

	} else if (pathinfo($file, PATHINFO_EXTENSION) === "tpl") {
	    //非忽略的文件类型
	    $ret[] = $file;
	}
    }

    return $ret;
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

    $text = "//{$js}.js\n//The Love, The Lover, Thangs.\n//General at " . date("Y-m-d H:i:s") . "\n\n\n";
    $path = JS_DIR . $js . ".js";
    $base = JS_DIR . "dev.{$js}.js";

    if (is_readable($base)) {
	//确保dev.xxxx.js类似的根文件永远处于第一位
	$text .= file_get_contents($base);
    }

    //写入到base.js中
    file_put_contents($path, $text);

    //得到所有的dev.base.js dev.base.xxxx.js
    $all = glob(JS_DIR . "dev.{$js}*.js");

    foreach ($all as $file) {
	if (!is_readable($file)
	    || $file === $base
	) {
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

    $text = "/* {$css}.css */\n/* The Love, The Lover, Thangs. */\n/* General at " . date("Y-m-d H:i:s") . " */\n\n\n";
    $path = CSS_DIR . $css . ".css";
    $base = CSS_DIR . "dev.{$css}.css";

    if (is_readable($base)) {
	//确保dev.xxxx.css类似的根文件永远处于第一位
	$text .= file_get_contents($base);
    }

    //写入到base.css中
    file_put_contents($path, $text);

    //得到所有的dev.base.css dev.base.xxxx.css
    $all = glob(CSS_DIR . "dev.{$css}*.css");

    foreach ($all as $file) {
	if (!is_readable($file)
	    || $file === $base
	) {
	    continue;
	}

	file_put_contents($path, "\n\n\n" . file_get_contents($file), FILE_APPEND);
    }
}



function a_devwatch_callback($event) {

}


$depends = array();

a_devwatch_init($depends);

a_tracker_add(array_keys($depends), "a_devwatch_callback");
