<?php
////////////////////////////////////////////////////////////////////////////////
// 监控css和js文件的变化并生成对应的文件
//  将多个文件粘合在一起，如dev.base.form.js 生成dev.base.js文件
//
//
////////////////////////////////////////////////////////////////////////////////

require_once(__DIR__ . "/devinc.all.php");


////////////////////////////////////////////////////////////////////////////////
// 生成js文件
//  监视js目录将dev.base.js和dev.base.form.js生成同一个文件
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

function a_watching() {
    // 生成js文件
    a_watch_general("js");

    // 生成css文件
    a_watch_general("css");
}


// 每次有变化时启动
a_watching();
