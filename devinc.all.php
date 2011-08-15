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
define("ROOT_DIR", "/var/www/duanyong");


//系统临时目录
define("TMP_DIR", "/tmp");

//系统路径分隔符
define("SEPARATOR", "/");


//配置文件
require_once(ROOT_DIR. '/devinc.farm.php');


//基础的php文件（顺序不能乱）
require_once(ROOT_DIR . '/devinc.bad.php');
require_once(ROOT_DIR . '/devinc.log.php');
require_once(ROOT_DIR . '/devinc.smarty.php');
require_once(ROOT_DIR . '/devinc.action.php');

//数据库文件
require_once(ROOT_DIR . '/devinc.mysql.php');


//smarty模板
require_once(ROOT_DIR . '/devinc.smarty.php');
