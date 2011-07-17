<?php
////////////////////////////////////////////////////////////////////////////////
// 文件跟踪相关函数
//  将多个文件粘合在一起，如dev.base.form.js 生成dev.base.js文件
//
//  常量:
//	$fd	    监听事件句柄
//	$watched    是否已监听
//	$watching   监听文件列表
//
//
//  XXX 注意 XXX
//	监听文件的修改操作
//	监听目录下文件的创建和删除操作
//	此文件的a_tracker_watching()会造成php执行阻塞，所以
//	运行此函数必须单独生成自己的守护进程（如shell或者php单独进程等）
//
//
////////////////////////////////////////////////////////////////////////////////

require_once(__DIR__ . "/devinc.all.php");


$fd	    = false;
$watched    = false;
$watching   = array();
$descriptor = array();

////////////////////////////////////////////////////////////////////////////////
// 初始化监听事件（会判断是否加载inotify模块）
//
function a_tracker_signle() {
    global $fd;

    if (!extension_loaded("inotify")) {
	//php没有加载inotify模块

	return a_log("inotify not loaded.");
    }

    if (false !== $fd ) {
	//已经初始化过直接返回

	return $fd;
    }

    return $fd = inotify_init();
}


////////////////////////////////////////////////////////////////////////////////
// 增加监控文件
//  必须要文件列表（files）和回调函数（callback）
//
function a_tracker_add($arr=false) {
    if (a_bad_array($arr)
	|| (
	    a_bad_array($arr["files"], $files)
	    && a_bad_string($arr["files"], $files)
	)
	|| a_bad_string($arr["callback"], $callback)
    ) {
	return a_log();
    }

    //没有监听的句柄
    if (false === ( $fd = a_tracker_signle() )) {
	//创建不了，有可能是没有加载php inotify模块

	return false;
    }

    global $watched;
    global $watching;
    global $descriptor;

    if (!a_bad_string($files)) {
	$list[] = &$files;
    } else {
	$list = &$files;
    }

    //将需要监控的文件存储起来
    foreach ($list as &$file) {
	$info = pathinfo($file);
	$file = $info["dirname"] . "/" . $info["basename"];

	if (!file_exists($file)
	    || isset($watching[$file])
	) {
	    //文件、目录并不存在或者已经监控过了
	    continue;
	}


	//目录需要创建，删除
	$wd = inotify_add_watch($fd, $file, IN_CREATE | IN_MODIFY | IN_DELETE);

	//将监控的id和文件对应起来
	//方便event里只需要取wd时就可以知道是那个监控的对象发生了变化
	$descriptor[$wd]	     = $file;
	$watching[$file]["descript"] = $wd;
	$watching[$file]["callback"] = $callback;

	unset($file);
    }


    if ($watched === false) {
	//如果还未监听，那么就开始监听
	a_tracker_go();
    }
}


////////////////////////////////////////////////////////////////////////////////
// 开始监听文件或者目录的变化
//
function a_tracker_go() {
    global $fd;
    global $watched;
    global $watching;
    global $descriptor;

    if (!count($watching)) {
	return ;
    }

    $watched = true;

    while(true) {
	if (inotify_queue_len($fd)) {
	    //内核检测到有事件（创造、修改或者删除等）

	    $events = inotify_read($fd);

	    foreach ($events as &$event) {
		//检查每个事件是否有对应的文件以及回调函数
		if (a_bad_id($event["mask"], $mask)
		    || a_bad_id($event["wd"], $wd)
		    || a_bad_string($event["name"], $file)
		    || a_bad_string($descriptor[$wd], $name)

		    || a_bad_array($watching[$name], $watch)
		    || a_bad_string($watch["callback"], $callback)
		) {
		    //对于没有文件名和事件监听的文件不需要做任何事
		    continue;
		}


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

		//准备回调函数
		@call_user_func_array($callback, array($event));

		unset($event);
	    }
	}

	//没有，沉睡一秒，等待唤醒
	sleep(1);
    }
}


