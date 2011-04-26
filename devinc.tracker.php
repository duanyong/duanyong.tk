<?php
////////////////////////////////////////////////////////////////////////////////
// 文件跟踪相关函数
//  将多个文件粘合在一起，如dev.base.form.js 生成dev.base.js文件
//
//  常量:
//	$fd	    监听事件句柄
//	$watched    是否已监听
//	$watchings  监听文件列表
//
//
//  XXX 注意 XXX
//	此文件的a_tracker_watching()会造成php执行阻塞，所以
//	运行此函数必须单独生成自己的守护进程（如shell或者php单独进程等）
//
//
////////////////////////////////////////////////////////////////////////////////

require_once(__DIR__ . "/devinc.all.php");


$fd	    = false;
$watched    = false;
$watchings  = array();

//监控事件
//  目录：创建，修改，删除
//  文件：修改
$masks["create"] = IN_CREATE;
$masks["modify"] = IN_MODIFY;
$masks["delete"] = IN_DELETE;


////////////////////////////////////////////////////////////////////////////////
// 初始化监听事件（会判断是否加载inotify模块）
//
function a_tracker_signle() {
    global $fd;

    if (!extension_loaded("inotify")) {
	//没有加载inotify模块

	return a_log("inotify not loaded.");
    }

    if (false !== $fd ) {
	//已经初始化过了

	return $fd;
    }

    return $fd = inotify_init();
}


////////////////////////////////////////////////////////////////////////////////
// 增加监控文件
//
function a_tracker_add($arr=false) {
    if (a_bad_array($arr)
	|| a_bad_array($arr["files"], $list)
	|| a_bad_function($arr["callback"], $callback)
    ) {
	return a_log();
    }

    //没有，创建一个
    if (false === ( $fd = a_tracker_signle() ) {

	return false;
    }

    global $masks;
    global $watchings;

    //将需要监控的文件存储起来，
    foreach ($list as &$file) {
	if (file_exists($file)
	    || isset($files[$file])
	) {
	    //文件、目录并不存在或者已经监控过了
	    continue;
	}

	$watchings[$file]["callback"]   = &$callback;
	$watchings[$file]["descriptor"] = inotify_add_watch($fd, $file,
	    is_dir($file)
	    ? ( $masks["create"] | $masks["delete"] | $mask["modify"] )
	    : $masks["modify"] );

	unset($file);
    }
}


////////////////////////////////////////////////////////////////////////////////
// 开始监听文件或者目录的变化
//
function a_tracker_watching() {
    global $fd;
    global $masks;
    global $watchings;

    if (count($watchings)) {
	return ;
    }

    $watched = true;

    while(true) {
	if (inotify_queue_len($fd)) {
	    //有事件需要处理

	    $events = inotify_read($fd);

	    foreach ($events as $event) {
		if (f_bad_id($event["mask"])
		    || f_bad_string($event["name"], $name)
		    || f_bad_array($watchings[$name], $watch)
		    || f_bad_function($watch["callback"], $callback)
		) {
		    //对于没有文件名和事件监听的文件不需要做任何事
		    continue;
		}

		switch ($event["mask"]) {
		case $mask["create"] :
		    break;

		case $mask["modify"] :
		    break;

		case $mask["delete"] :
		    //删除得先把监听事件注销
		    delete $watchings[$name];
		    break;

		    //TODO:调用回调函数
		}
	    }
	}
    }
}


