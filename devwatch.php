<?php
////////////////////////////////////////////////////////////////////////////////
// 监控css和js文件的变化并生成对应的文件
//  将多个文件粘合在一起，如dev.base.form.js 生成dev.base.js文件
//
//  依赖: 基础文件
//
//
//
//
////////////////////////////////////////////////////////////////////////////////

require_once(__DIR__ . "/devinc.all.php");
require_once(__DIR__ . "/devinc.tracker.php");

define("JS_DIR", ROOT_DIR . "/js/");
define("CSS_DIR", ROOT_DIR . "/css/");


//文件的依赖关系列表
//  分析tpl和含有{*devwatch: xxxx*}指令的文件
//	tpl => 文件虽然没有{*devwatch: xxxx*}但还是需要分析依赖关系。
//	因为在依赖关系链条中，有可能最后一个tpl中有{*devwatch: xxxx*}，如果不存储其依赖关系，文件更新却反应不到关系链条顶部，
//	造成文件更新却无法处理的问题。
//
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


$js_regx  = "/\{js name\=\"(\S+[,| |\S+]*)\"\}/";
$css_regx = "/\{css name\=\"(\S+[,| |\S+]*)\"\}/";
$inc_regx = "/\{include file\=\"[ ]*(\S+\.tpl)\"\}/";
$dev_regx = "/\{\*devwatch\:([ |\S]+)\*\}/";


function a_devwatch_init() {
    if (is_file("/var/tmp/devwatch.pid")) {
	return a_log("daemon is running for /tmp/devwatch.pid");
    }

    //得到所有的tpl文件
    $js  = glob(JS_DIR . "dev.*.js");
    $css = glob(CSS_DIR . "dev.*.css");
    $tpl = a_devwatch_exhibit_tpl(ROOT_DIR);

    //所有的文件
    $files = array_merge($js, $css, $tpl);

    //将文件切片分析其依赖关系
    a_devwatch_slice_files($files);
}


//将所有文件切片分析确定文件与文件的依赖关系
//  只分析tpl和非tpl中包含{*devwatch: xxxx*}指令的文件
function a_devwatch_slice_files(&$files) {
    if (a_bad_array($files)) {
	return a_log();
    }

    global $dev_regx, $depends;

    //分析所有可能产生依赖的文件，确立其依赖关系
    foreach ($files as $file) {
	if (!is_file($file)
	    || (
		( pathinfo($file, PATHINFO_EXTENSION) !== "tpl" )
		&& !( $chars = file_get_contents($file) )
		&& !( preg_match($dev_regx, $chars) )
	    )
	) {
	    //非文件、非tpl文件且文件中没有{devwatch:xxxx}指令不需要分析其依赖关系
	    continue;
	}

	a_devwatch_search_relation($file, $depends);
    }
}



//分析单个文件的依赖关系
//  文件形成依赖关系有下面的语句产生
//	1、{js name="xxxx, yyyy"}	依赖js文件
//	2、{css name="xxxx, yyyy"}	依赖css文件
//	3、{include file="/xxxx.tpl"}	依赖xxxx.tpl文件
function a_devwatch_search_relation($file, &$depends) {
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

    global $js_regx, $css_regx, $inc_regx;

    //得到每个文件
    while (!feof($handle)) {
	if (( $line = fgets($handle) )) {

	    //当前行是否有依赖语句
	    if (preg_match($js_regx, $line, $regs)) {
		//是{js name="xxxx, yyyy, zzzz"}
		a_devwatch_depend_js($regs[1], $file, $depends);

	    } else if (preg_match($css_regx, $line, $regs)) {
		//是{css name="xxxx, yyyy, zzzz"}
		a_devwatch_depend_css($regs[1], $file, $depends);

	    } else if (preg_match($inc_regx, $line, $regs)) {
		//是{include file="" $date=20091011}
		a_devwatch_depend_include($regs[1], $file, $depends);
	    }
	}
    }

    @closedir($dir);
}


//依赖js，分析{js name="xxxx, yyyy, zzzz"}语句，并形成依赖关系
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


//依赖css，分析css{css name="xxxx, yyyy, zzzz"}语句，并形成依赖关系
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


//依赖include，分析{include file="/xxxx.tpl"}语句，并形成依赖关系
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
//$js => base
function a_watch_general_js($js) {
    if (a_bad_string($js)) {
	return a_log();
    }

    $path = JS_DIR . $js . ".js";
    $base = JS_DIR . "dev.{$js}.js";
    $text = "//{$js}.js\n//The Love, The Lover, Thangs.\n//General at " . date("Y-m-d H:i:s") . "\n\n\n";

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

    a_log('general done: ' . str_replace(ROOT_DIR, '', $path));
}


//在ROOT_DIR/css目录生成需要的css文件
function a_watch_general_css($css) {
    if (a_bad_string($css)) {
	return a_log();
    }

    $path = CSS_DIR . $css . ".css";
    $base = CSS_DIR . "dev.{$css}.css";
    $text = "/* {$css}.css */\n/* The Love, The Lover, Thangs. */\n/* General at " . date("Y-m-d H:i:s") . " */\n\n\n";

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


    a_log('general done: ' . str_replace(ROOT_DIR, '', $path));
}


//生成{*devwatch: xxxx*}的文件（用smarty生成四种文件格式：js, css, html, shtml）
//
//  注意：  xxxx不能以dev开头，不心tpl结尾
//	    文件名默认可以不指定，采用tpl的文件名替换
//		{*devwatch: js*}
//		{*devwatch: data.js*}
//
function a_watch_general_tpl($tpl) {
    global $dev_regx;

    if (!is_file($tpl)
	|| !is_readable($tpl)
	|| !( $content = file_get_contents($tpl) )
	|| !preg_match($dev_regx, $content, $match)
	|| !( $name = $match[1] )
	|| !( $name = str_replace(" ", "", $name) )

	//生成的文件不以dev开头，不以tpl结尾
	|| preg_match("/^dev\S+|\S*tpl$|^dev\S*tpl$/", $name)

    ) {
	return false;
    }

    // {*devwatch: js*}
    // {*devwatch: css*}
    // {*devwatch: html*}
    // {*devwatch: shtml*}
    // {*devwatch: index.js*}
    // {*devwatch: index.css*}
    // {*devwatch: index.html*}
    // {*devwatch: index.shtml*}

    $pos	= 0;
    $info	= pathinfo($tpl);
    $filename	= null;

    if (false === ( $pos = strrpos($name, ".") )) {
	//类似{*devwatch: js*}
	//没有文件名只有后缀，采用tpl的文件名
	$filename = $info["dirname"] . "/" . $info["filename"] . "." . $name;

    } else {
	//类似{*devwatch: index.js*}
	//取$tpl的路径，与指定输入的文件名称拼接，得到路径
	$filename = $info["dirname"] . "/" . $name;
    }

    if (false === ( $content = a_smarty_tpl($tpl) )) {
	//tpl报错，不进行文件写入
	return false;
    }

    try {
	//写入/var/www/duanyong/js/xxxx.js
	file_put_contents($filename, $content);

	a_log('general done: ' . str_replace(ROOT_DIR, '', $filename));

    } catch (Exception $e) {
	return a_log("cann't writable to path: {$filename}, please check it.");
    }
}


//返回项目可能产生依赖关系的tpl文件
function a_devwatch_exhibit_tpl($dir) {
    if (!is_dir($dir)) {
	return a_log();
    }

    $ret = array();

    //不需要分析依赖关系的目录
    static $ignore_dir = null;

    if (!$ignore_dir) {
	$ignore_dir[] = ROOT_DIR . "/img";
	$ignore_dir[] = ROOT_DIR . "/dev";
    }

    $ext = null;

    //取得所有的tpl文件
    foreach (glob($dir . "/*") as $file) {
	if (!( $ext = pathinfo($file, PATHINFO_EXTENSION) )
	    || in_array($ext, array("php", "html", "html", "gif", "png", "swp"))
	) {
	    continue;
	}

	if (is_dir($file)
	    && !in_array($file, $ignore_dir)
	) {
	    $ret = array_merge($ret, a_devwatch_exhibit_tpl($file));

	} else {
	    //非忽略的文件类型
	    $ret[] = $file;
	}
    }

    return $ret;
}


//得到项目下所有的目录
function a_devwatch_exhibit_directory($dir) {
    if (!is_dir($dir)) {
	return array();
    }

    //得到目录下所有的子目录
    $dirs = glob($dir . "/*", GLOB_ONLYDIR);

    //还需要遍历子目录
    foreach ($dirs as $file) {
	$dirs = array_merge($dirs, a_devwatch_exhibit_directory($file));
    }

    $dirs[] = $dir;

    return array_unique($dirs);
}



//监听项目目录，如有文件发生变化立即处理
function a_devwatch_tracker() {
    $dirs = a_devwatch_exhibit_directory(ROOT_DIR);

    //排除不必监视的目录，如dev/img等
    static $ignore;;

    if (!$ignore) {
	$ignores[] = ROOT_DIR . "/img";
	$ignores[] = ROOT_DIR . "/dev";
    }


    a_tracker_add($dirs, "a_devwatch_callback");
}



function a_devwatch_callback(&$events) {
    //目录的创建、更名、删除操作会触发回调
    //文件的创建，修改，删除操作会触发回调
    global $dev_regx, $depends;

    static $ignore;

    if (!$ignore) {
	$ignore = array(
	    "php",
	    "html",
	    "shtml",
	    "jpg",
	    "png",

	    "swp",
	);
    }


    $changes = array();

    foreach (array_keys($events) as $file) {
	if (a_bad_array($events[$file], $event)
	    || a_bad_id($event["wd"], $wd)
	) {
	    continue;
	}

	//判定是目录还是文件
	if (!$event["is_dir"]) {
	    //文件
	    if (!( $path = $event["source"] . "/" . $file )
		|| !is_readable($path)
		|| !( $info = pathinfo($path) )
		|| !$info["extension"]
		|| in_array($info["extension"], $ignore)
	    ) {
		continue;
	    }

	    //将更改的文件累积起来，对单个文件处理完毕后重新分析其信赖关系
	    $changes[]	= $path;
	    //获取文件内容，如有{*devwatch: xxxx*}指令需要重新生成文件
	    $content	= file_get_contents($path);


	    //发生更新的文件有下面几种情况需要处理
	    //	    1、含有{*devwatch: xxxx*}指令，先xxxx类型的文件
	    //	    2、更新文件是dev.xxxx.js或者dev.xxxx.yyyy.css文件
	    //	处理完后查看依赖表是否有文件依赖更新文件。如有，更新依赖文件
	    //
	    if (preg_match($dev_regx, $content, $match)) {
		// {*devwatch: js*}
		// {*devwatch: css*}
		// {*devwatch: html*}
		// {*devwatch: shtml*}
		a_watch_general_tpl($path);


	    } else if (substr($info["filename"], 0, 4) === "dev.") {
		//以dev.开头的文件


		//处理dev.xxxx.zzzz.js / dev.xxxx.css ，取xxxx文件名
		$token = explode(".", $info["filename"]);
		$token = $token[1];


		if ($info["extension"] === "js") {
		    //生成dev.base.js文件生成base.js

		    a_watch_general_js($token);

		    continue;

		} else if ($info["extension"] === "css") {
		    //生成dev.base.css文件生成base.css

		    a_watch_general_css($token);

		    continue;
		}


	    } else {
		//处理完 {*devwatch: xxxx*} 和 {js name="base"}、{css name=""}，暂不需要处理其它类型
	    }
 

	    //查看文件在 XXX 旧的 XXX 依赖关系列表中是否存在 
	    //	发生变化的文件在依赖链条上的底端，需要将上面所有的文件都检查，只要遇到{*devwatch: xxxx*}的文件都需要重新生成，
	    //	另外要注意有可能依赖链条中有交叉情况（如foot.tpl和header.tpl依赖其它文件），所有在此需要标记已处理过的
	    //	文件就不需要再次处理（因为都是同一文件引起变化）。如下图所示：
	    //
	    //		----------------------------------
	    //		|   index.tpl {*devwatch: html*} |
	    //		----------------------------------
	    //			|		---------------------
	    //			|_____依赖______|	foot.tpl    |
	    //					---------------------
	    //						|		--------------------------------
	    //						|_____依赖______| check.tpl {*devwatch: html*} |
	    //								--------------------------------
	    //
	    //	check.tpl发生变化，需要将index.tpl文件也更新，所以需要将依赖链条上所有的文件都检查一次，遇到{*devwatch: xxxx*}都更新
	    //
	    //
	    //
	    $dones = array();

	    if (isset($depends[$path])) {
		//依赖关系是一张二维表，所有只要两次循环就可以拿到全部的依赖关系
		$dps = $depends[$path];

		foreach ($dps as $k => $d) {
		    if (!is_readable($d)
			|| !( $content = file_get_contents($d) )

			//已做过处理
			|| isset($dones[$d])
		    ) {
			continue;
		    }


		    //有{*devwatch: xxxx*}指令，重新生成
		    if (preg_match($dev_regx, $content, $match)) {
			a_watch_general_tpl($d);

			$dones[$path]  = true;
		    }
		}

		$dps = array_unique($dps);
	    }
	}
    }
}



function a_devwatch_go() {
    //关系初始化
    a_devwatch_init();

    //开始跟踪文件变化
    a_devwatch_tracker();
}



$depends = array();

a_log_off();

a_devwatch_go();
