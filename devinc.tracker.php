<?php
////////////////////////////////////////////////////////////////////////////////
// 文件跟踪相关函数
//  利用inotify监听目录及文件的更改
//	如果目录或者文件发生变化，那么回调用户函数进行处理
//	在执行期间无任何事件发生，自动沉睡2秒后再次检查
//
//
//	a_tracker_add($files, $callback)
//	    $files	需要监听的目录或者文件
//	    $callback	变化的处理函数，参数为经过处理的事件列表
//
//	    callback($events)
//
//		$events = array(
//		    "a_devwatch_callback" => array(
//			array($event),
//			array($event),
//			array($event),
//		    ),
//		    "a_devlog_split"    => array(
//			array($event),
//			array($event),
//			array($event),
//		    ),
//		);
//
//		$event = array(
//		    wd		: 监听的标识（纯数值）
//		    mask	: 何种事件（IN_MODIFY|IN_DELETE|IN_CREATE）
//		    name	: 发生变化的文件名
//		    source	: 事件源（自定义属性）
//		    cookie	: 未知
//		    is_file	: true / false
//		);
//
//
//
//  常量:
//	$inotify    系统级监听事件
//	$watched    是否已开始监听
//	$watching   监听文件列表，目录或者文件对应的回调函数和监听事件句柄
//	$descriptor 监听句柄，事件句柄对应目录或者文件的路径
//
//
//  XXX 注意 XXX
//	监听目录或者文件的创建、修改和删除操作
//	此文件的a_tracker_watching()会造成php执行阻塞，所以
//	运行此函数必须单独生成自己的守护进程（如shell或者php单独进程等）
//
//
////////////////////////////////////////////////////////////////////////////////

require_once(__DIR__ . "/devinc.all.php");

$inotity    = false;
$watched    = false;
$watching   = array();
$descriptor = array();

////////////////////////////////////////////////////////////////////////////////
// 初始化监听事件（会判断是否加载inotify模块）
//
function a_tracker_init() {
    if (!extension_loaded("inotify")) {
	//php没有加载inotify模块

	return a_log("inotify not loaded.");
    }

    global $inotify;

    if (!$inotify) {
	$inotify = inotify_init();
    }

    return $inotify;
}


////////////////////////////////////////////////////////////////////////////////
// 增加监控文件
//  必须要文件列表（files）和回调函数（callback）
//  $files	监听的目录
//  $callback	发生变化时的函数
//
function a_tracker_add($files, $callback) {
    if (!is_array($files)
	|| !function_exists($callback)
    ) {
	return a_log('$files not array or $callback not function.');
    }

    if (!( $inotify = a_tracker_init() )) {
	//有可能内核没有inotify模块
	return a_log("inotify init fail!");
    }

    global $watching;
    global $descriptor;

    //将需要监控的文件存储起来
    foreach ($files as $f) {
	if (isset($watching[$f])
	    || !file_exists($f)
	) {
	    //目录已监听或者目标不存在
	    continue;
	}

	//目录需要创建，删除
	$wdid = inotify_add_watch($inotify, $f, IN_CREATE | IN_DELETE | IN_MODIFY);

	//将监控的id和文件对应起来
	//方便event里只需要取wd时就可以知道是那个监控的对象发生了变化
	$descriptor[$wdid]	    = $f;
	$watching[$f]["descript"]   = $wdid;
	$watching[$f]["callback"]   = $callback;
    }

    //开始监听
    a_tracker_go();
}


////////////////////////////////////////////////////////////////////////////////
// 开始监听文件或者目录的变化
//
function a_tracker_go() {
    global $watched;
    global $watching;
    global $descriptor;

    if (!count($watching)
	|| $watched === true
    ) {
	return ;
    }

    //设置为已在监听中
    $watched = true;

    //得到监听的句柄
    $inotify = a_tracker_init();

    while(true) {
	//读取事件的队列，如果没有任何事件，会阻塞进程
	if (inotify_queue_len($inotify)) {
	    //内核检测到有事件（新建、修改或者删除等）
	    $events = inotify_read($inotify);

	    $es = array();

	    foreach ($events as $event) {
		//检查每个事件是否有对应的文件以及回调函数
		if (a_bad_id($event["wd"], $wdid)
		    || a_bad_string($descriptor[$wdid], $file)
		    || a_bad_array($watching[$file], $watcher)

		    || a_bad_string($watcher["callback"], $callback)
		) {
		    //对于没有文件名和事件监听的文件不需要做任何事
		    continue;
		}


		//事件源（自定义属性）
		$event["source"] = $file;


		if (!isset($es[$callback])) {
		    $es[$callback] = array();
		}

		//相同文件修改会产生很多事件，此处用$file过滤（保留最后发生的事件）
		$es[$callback][] = $event;


		/*  将$es结构变成回调函数数组，数组中是一系列inotify的event列表
		 *
		 *  $es = array(
		 *	"a_devwatch_callback" => array(
		 *	    array($event),
		 *	    array($event),
		 *	    array($event),
		 *	),
		 *	"a_devlog_split"    => array(
		 *	    array($event),
		 *	    array($event),
		 *	    array($event),
		 *	),
		 *  );
		 * */
	    }

	    foreach (array_keys($es) as $callback) {
		//准备回调函数
		@call_user_func($callback, a_tracker_events_unique($es[$callback]));
	    }
	}

	//没有新事件，沉睡两秒后唤醒
	sleep(2);
    }
}


//将回调函数中相同的事件按[删除, 修改, 创建]优先级取值
//  由于修改同一文件在inotify会触发两个事件（创建和修改），
//  所以防止相同的文件被处理多次，采用从高到低地取事件类型
//
//  比如：
//	文件devinc.log.php在事件队列里无序的排列有三种情况（修改，修建，删除），那么直接返回删除事件，忽略创建和修改事件
//
//  隐患：
//	文件devinc.log.php在某一时候先被删除后再创建，inotify有可能同时捕猎了删除和新建事件。在本逻辑中会忽略新建事件而返回删除事件。
//
function a_tracker_events_unique(&$events) {
    $ret = array();

    foreach ($events as &$e) {
	if (( $name = $e["name"] )) {
	    //有文件名才检查，其它忽略
	    if (!$ret[$name]) {
		$ret[$name] = $e;
	    }


	    //与队列中的事件对比mask，根据mask的优先级决定是否保留原来的mask值
	    $e1 = $ret[$name];

	    if ($e["mask"] & IN_DELETE) {
		//删除事件优先级最高，其它事件全部忽略
		$ret[$name]["mask"]	= $e["mask"];
		$ret[$name]["delete"]   = $e["mask"];

		unset($ret[$name]["create"]);
		unset($ret[$name]["modify"]);

	    } else if ($e["mask"] & IN_MODIFY) {
		//修改事件优先级低于删除，但高于创建
		$ret[$name]["mask"]	= $e["mask"];
		$ret[$name]["modify"]   = $e["mask"];

		unset($ret[$name]["create"]);

	    } else if ($e["mask"] & IN_CREATE) {
		//新建事件优先级最低，排在最后
		$ret[$name]["mask"]	= $e["mask"];
		$ret[$name]["create"]   = $e["mask"];
	    }
	}

	//一定要unset掉变量
	unset($e);
    }

    return $ret;
}

