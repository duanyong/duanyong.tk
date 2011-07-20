<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.config.php
//	配置项目属性的文件
//	    数据库用户名、密码、数据库表
//
//	
//	$config["username"]
//	    数据库用户名
//
//	$config["password"]
//	    数据库密码
//
//	$config["database"]
//	    数据库名
//
//
////////////////////////////////////////////////////////////////////////////////


global $config;

$config = array();

$config["username"] = "duanyong";
$config["password"] = "~!@#98$%^<>MNse].baW3xd&%_=j_BVCa";
$config["database"] = "xoxo";
$config["farm"]	    = array(
    "127.0.0.1",
    "192.168.1.104",
);

//压缩的总开关
//如需要不压缩此
$config["compress"]	= true;

$config["compress_js"]	= true;
$config["compress_css"] = true;
$config["compress_tpl"] = true;


