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
$dev_regx = "/\{\*devwatch\:[ |\S]+\*\}/";


function a_devwatch_init(&$depends) {
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
    a_devwatch_slice_files($files, $depends);
}


//将所有文件切片分析确定文件与文件的依赖关系
//  只分析tpl和非tpl中包含{*devwatch: xxxx*}指令的文件
function a_devwatch_slice_files(&$files, &$depends) {
    if (a_bad_array($files)
	|| a_bad_array($depends)
    ) {
	return a_log();
    }

    global $dev_regx;

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
}


//生成{*devwatch: xxxx*}的文件（用smarty生成四种文件格式：js, css, html, shtml）
//
//  注意：  xxxx不能以dev开头，不心.tpl结尾
//	    文件名默认可以不指定，采用tpl的文件名替换
//  {*devwatch: js*}
//  {*devwatch: data.js*}
//
function a_watch_general_tpl($tpl) {
    global $dev_regx;

    if (!is_file($tpl)
	|| !is_readable($tpl)
	|| !( $content = file_get_contents($tpl) )
	|| !preg_match($dev_regx, $content, $match)
	|| !( $name = $match[1] )
	|| !( $name = str_replace(" ", "", $name) )

	//不以dev开头，不以tpl结尾
	|| !preg_match("/^dev\S+|\S*tpl$|^dev\S*tpl$/", $name)

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


    $info = pathinfo($tpl);

    if (false === strrpos($name, ".")) {
	//{*devwatch: js*}
	//没有文件名只有后缀，采用tpl的文件名

	//tpl文件名有可能是dev.base.form.js或者dev.base.js或者base.tpl或者devwatch.tpl
	//
	if (preg_match("^[dev\.]+(\S)")) {}
	$filename str_replace("dev.", "", $info["filename"]);

	//如果


	$name = $info["filename"] . $name;
    }


	|| !( $info = $info["dirname"] . "/" . $info["filename"] . "." . $match[1] )


    //得到文件真正的路径{*devwatch: data.js*}，生成tpl所在目录/data.js
    $info = $info["dirname"] . "/" . $info["filename"] . "." . $match[1];

    if (!is_writeable($info)) {
	return a_log("cann't writable to path: {$info}, please check it.");
    }


    try {
	//写入/var/www/duanyong/js/xxxx.js
	file_put_contents($info, f_smarty($tpl));

    } catch (Exception $e) {
	return a_log($e->getMessage());
    }
}



//用smarty生成其它文件（如:/js/citydata.js）
//TODO:
function a_watch_general_other($tpl) {
    if (!file_exists($tpl)
	|| !is_readable($tpl)
    ) {
	return a_log();
    }


    $content = null;

    try {
	$content = a_smarty($tpl);

    } catch (Exception $e) {
	a_log($e->getMessage());
    }

    //是否需要压缩

    ///file_put_contents($tpl, $content);
}


//返回项目可能产生依赖关系的tpl文件
function a_devwatch_exhibit_tpl($dir) {
    if (!is_dir($dir)) {
	return a_log();
    }

    $ret = array();

    //不需要分析依赖关系的目录
    static $ignore_dir = array();
    $ignore_dir[] = ROOT_DIR . "/img";
    $ignore_dir[] = ROOT_DIR . "/dev";

    //取得所有的tpl文件
    foreach (glob($dir . "/*.tpl") as $file) {
	if (is_dir($file)
	    && !in_array($file, $ignore_dir)
	) {
	    $ret = array_merge($ret, a_devwatch_depend_tpl($file));

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
    static $ignore = array();

    $ignores[] = ROOT_DIR . "/img";
    $ignores[] = ROOT_DIR . "/dev";


    a_tracker_add($dirs, "a_devwatch_callback");
}



function a_devwatch_callback($events) {
    //目录的创建、更名、删除操作会触发回调
    //文件的创建，修改，删除操作会触发回调

    global $dev_regx, $depneds;

    static $ignore = array("php", "html", "shtml", "jpg", "png");


    $changes = array();

    foreach (array_keys($events) as $file) {
	if (f_bad_array($events[$file], $event)
	    || f_bad_id($event["wd"], $wd)
	) {
	    continue;
	}

	//判定是目录还是文件
	if (!$event["is_dir"]) {
	    //文件
	    if (!is_readable( $path = $event["source"] . "/" . $file )
		|| !( $info = pathinfo($path) )
		|| in_array($info["extension"], $ignore)
	    ) {
		continue;
	    }

	    //将更改的文件累积起来，对单个文件处理完毕后重新分析其信赖关系
	    $changes[] = $path;


	    //获取文件内容，如有{*devwatch: xxxx*}指令需要重新生成文件
	    $content = file_get_contents($path);



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

		$file = $info["dirname"] . "/" . $info["filename"] . "." . $match[1];

		if (!is_writeable($file)) {
		    a_log("cann't writable to path: {$file}, please check it.");

		    continue;
		}

		//写入/var/www/duanyong/js/xxxx.js
		file_put_contents($path, f_smarty($file));



	    } else if (strpos($info["filename"], "dev.") === 0) {
		//处理dev.xxxx.js / dev.xxxx.css 
		$token = substr($info["filename"], strpos($info["filename"], "."));

		$token .= $info["extension"];

		if ($info["extension"] === "js") {
		    //生成dev.base.js文件生成base.js

		    a_watch_general_js($token);
		} else if ($info["extension"] === "css") {
		    //生成dev.base.css文件生成base.css

		    a_watch_general_css($token);
		}



	    } else {
		//处理完 {*devwatch: xxxx*} 和 {js name="base"}、{css name=""}，暂不需要处理其它类型
	    }
 

	    //查看文件在 XXX 旧的 XXX 依赖关系列表中是否有信赖关系。
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

	    if (isset($depends[$file])) {
		//依赖关系是一张二维表，所有只要两次循环就可以拿到全部的依赖关系
		$dps = $depends[$file];

		foreach ($dps as $d) {
		    if (!is_readable($d)
			|| !( $content = file_get_contents($d) )
		    ) {
			continue;
		    }

		    if (preg_match($dev_regx, $content, $match)) {

		    }

		    

		    if (isset($depends[$d])) {
			foreach ($depends[$d])) {}

			$dps = array_merge($dps, $depends[$d]);

		    }
		}

		$dps = array_unique($dps);

	    }
	}
    }
}



function a_devwatch_xxxx() {
}



function a_devwatch_tracker_file($file) {}



$depends = array();
a_devwatch_tracker();


//a_devwatch_init($depends);

		/*
		switch (true) {
		case (IN_CREATE & $mask):
		case (IN_MODIFY & $mask):

		    break;

		case (IN_DELETE & $mask):
		    //有删除操作，如果是目录的话，需要删除
		    if (is_dir($name)) {
			//目录是末尾是否有/，
			if (strrpos($name, "/") !== mb_strlen($name)-1) {
			    //没有，需要手动添加
			    $name .= "/";
			}

			//被删除的目录是否也是目录
			if (is_dir($name . $file)) {
			    //删除监听事件和当前被删除的监听目录

			    unset($watching[$name]);
			    unset($descriptor[$wd]);

			    @inotify_add_watch($fd, $wd);
			}
		    }

		    break;

		default:
		    //其它操作直接略过，不需要回调函数
		    continue;
		}
		 */

