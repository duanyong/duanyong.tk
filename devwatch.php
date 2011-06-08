<?php
////////////////////////////////////////////////////////////////////////////////
// 监控css和js文件的变化并生成对应的文件
//  将多个文件粘合在一起，如dev.base.form.js 生成dev.base.js文件
//
//
////////////////////////////////////////////////////////////////////////////////

require_once(__DIR__ . "/devinc.all.php");


$tpls	= array();
$jses	= array();
$csses	= array();

$pid_file = "/tmp/devwatch.pid";


////////////////////////////////////////////////////////////////////////////////
// devwatch init
//
////////////////////////////////////////////////////////////////////////////////
function a_watch_init() {
    //分析ROOT_DIR目录中的所有js，css，tpl文件相互之间的关系

    $tpls = a_watch_get_files(ROOT_DIR, "tpl");
}


//分析目录下的文件（tpl）
function a_devwatch_get_files($dir, $type) {
    if (!is_dir($dir)
	|| false === ( $hd = @opendir($dir) )

	|| a_bad_string($type)
    ) {
	return a_log();
    }


    $files = array();

    while (false !== ( $name = readdir($hd) )) {
	if ($name === '.'
	    || $name === '..'
	) {
	    continue;

	} 

	//是文件的情况
	if (is_file($name)) {
	    //隐藏文件和非type文件不返回
	    if (0 === ( $pos = strrpos($name, '.') )
		|| $type !== mb_substr($name, $pos + 1)
	    ) {
		continue;
	    }

	    $files[] = $dir . '/' . $name;

	} else if (is_dir($name)) {
	    //是目录，递归
	    $files = array_merge($files, a_devwatch_get_files($dir . '//' . $name, $type));
	}
    }

    @closedir($dir);

    return $files;
}



//分析tpl文件中的依赖关系的数据
function a_devwatch_depend($files) {
    if (a_bad_array($files)) {
	return a_log();
    }

    global $depends;

    foreach ($files as $name) {

	if (!is_file($name)
	    || false === ( $hd = @fopen($name, 'r') )
	) {
	    continue;
	}

	while (false !== ( $line = fgets($hd) )) {
	    //是否以{开始和}结尾
	    if (false === ( $line = str_replace(array("\n", "\r"), '', $line) )
		|| '{' !== substr($line, 0, 1)
		|| '}' !== substr($line, strlen($line) -1, 1)

		|| false === strpos($line, '=')

		//取{js file="layout, login"}中的js依赖文件类型
		|| false === ( $type = substr($line , 1, strpos($line, ' ') -1) )

		//取{js file="layout, login"}中的layout, login会依赖文件名
		|| false === ( $pos1 = strpos($line, '"') )
		|| false === ( $pos2 = strrpos($line, '"') )
		|| false === ( $change = substr($line, $pos1+1, $pos2 - $pos1 -1) )
	    ) {
		continue;
	    }

	    //去除"layout, login"中的空格
	    $change = str_replace(' ', '', $change);

	    if (empty($change)) {
		//没有可变化的文件
		continue;
	    }

	    if ($type === 'js') {
		//dev.base.js
		$pre	= 'dev.';
		$post	= '.js';

	    } else if ($type === 'css') {
		//dev.layout.js
		$pre	= 'dev.';
		$post	= '.css';

	    } else {
		$pre	= '';
		$post	= '';
	    }


	    //当前行为js，换成dev.layout.js 和 dev.login.js
	    $change = explode(',', $change);

	    //建立依赖关系depends['dev.layout.js'][] = "t.tpl";
	    foreach ($change as &$c) {
		$depends[$pre . $c. $post][] = $name;

		unset($c);
	    }
	}
    }
}




////////////////////////////////////////////////////////////////////////////////
// 生成js和css文件
//
////////////////////////////////////////////////////////////////////////////////
function a_watch_general($type) {
    if (a_bad_string($type)
	|| (
	    $type !== "js"
	    && $type !== "css" )
	|| !( $dir = ROOT_DIR . "/{$type}/" )
	|| !is_dir($dir)
	|| !(  $handle = opendir($dir) )
    ) {
	return a_log();
    }

    // TODO:
    // 测试目的目录是否可写

    $files  = array();

    while (false !== ( $file = readdir($handle) )) {
	// 如果文件不以dev.xx.js这样的格式，不管
	if (!( $file = explode(".", $file) )
	    || $file[0] !== "dev"
	    || $file[count($file)-1] !== "js"
	) {
	    continue;
	}

	// 对dev.base.form.js 进行分组，最后得到base.js文件名
	$files[$file[1]][] = implode(".", $file);
    }

    foreach ($files as $fname => $items) {
	// 创建一个文件，将其归纳到dev.base.js 的base.js中
	// 需要先删除文件中的内容
	file_put_contents($dir . $fname . ".js", "");

	foreach ($items as $name) {
	    file_put_contents($dir . $fname . ".js", file_get_contents($dir . $name), FILE_APPEND);
	}
    }
}


//分析目录下所有的文件与文件之间的关系
//  只分析.tpl与js，css文件之间的关系
function a_devwatch_get_files($dir, $type) {
    if (a_bad_string($type)
	|| !is_dir($dir)
	|| false === opendir($dir)
    ) {

	return a_log();
    }


    $files = array();

    while (false !== ( $name = readdir($dir) )) {
	if (is_dir($name)) {}

	$files = array_merge($files, a_devwatch_get_files())

	if (false === ( $pos = strrpos($name, '.') )
	    || $type !== substr($name, $pos + 1)
	) {
	    continue;
	}

	$files[] = $name;
    }


    while (false !== ( $file = readdir($handle) )) {
	// 如果文件不以dev.xx.js这样的格式，不管
	if (!( $file = explode(".", $file) )
	    || $file[0] !== "dev"
	    || $file[count($file)-1] !== "tpl"
	) {
	    continue;
	}

	// 对dev.base.form.js 进行分组，最后得到base.js文件名
	$files[$file[1]][] = implode(".", $file);
    }
}



// 生成各种文件
function a_watching() {
    // 生成js文件
    a_watch_general("js");

    // 生成css文件
    a_watch_general("css");

    return true;
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
