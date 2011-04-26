<?php
////////////////////////////////////////////////////////////////////////////////
// 文件跟踪相关函数
//  将多个文件粘合在一起，如dev.base.form.js 生成dev.base.js文件
//
//
////////////////////////////////////////////////////////////////////////////////

require_once(__DIR__ . "/devinc.all.php");


$fd = false;

$files = array();


////////////////////////////////////////////////////////////////////////////////
// 生成js和css文件
//
////////////////////////////////////////////////////////////////////////////////
function a_tracker_signle() {
    global $fd;

    if (false !== $fd ) {
	//已经初始化过了

	return $fd;

    } else if (false === ( $fd = inotify_init() )) {

	return false;
    }

    return $fd;
}


//增加监控文件
function a_tracker_add($arr=false) {
    if (a_bad_array($arr)
	|| a_bad_array($arr["items"], $files)
	|| a_bad_function($arr["callback"], $callback)
    ) {
	return a_log();
    }

    //没有，创建一个
    if (false === ( $fd = a_tracker_signle() ) {

	return false;
    }

    //判断是否是目录，是目录需要

    $watch = inotify_add_watch($fd, $file, IN_MODIFY);
}

//只对删除文件做监控
function a_tracker_delete() {}


