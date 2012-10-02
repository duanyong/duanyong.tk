<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.all.php
//	将所有基本的php文件引用进来
//
//  
//	devinc.bad.php
//	    检查变量的文件
//
//	devinc.log.php
//	    日志输出的文件
//
//	devinc.smarty.php
//	    smarty渲染的文件
//
//	devinc.action.php
//	    页面操作的文件
//
//
////////////////////////////////////////////////////////////////////////////////


//项目根目录
define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT'] . '/aiyuji');
//系统临时目录
//define("TEMP_DIR", $_SERVER['SERVER_TEMP']);

define('APP_DB_PREFIX', '201208aiyuji');




//基础的php文件（顺序不能乱）
require_once(ROOT_DIR . '/dev/devinc.log.php');
require_once(ROOT_DIR . '/dev/devinc.bad.php');
require_once(ROOT_DIR . '/dev/devinc.safe.php');
require_once(ROOT_DIR . '/dev/devinc.mdb2.php');
require_once(ROOT_DIR . '/dev/devinc.action.php');
require_once(ROOT_DIR . '/dev/devinc.cookie.php');

