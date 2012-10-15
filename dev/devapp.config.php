<?php
/*********************************************************
 * 应用的配置文件
 * 
 *
**********************************************************/


define('ROOT_DIR',  $_SERVER['DOCUMENT_ROOT'] . '/aiyuji');
define('HTTP_HOST', 'http://' . $_SERVER['HTTP_HOST'] . '/aiyuji');
define('SHF_DIR',   $_SERVER['DOCUMENT_ROOT'] . '/SHFramework');

require_once(SHF_DIR . '/v.13/devcom.site.php');


//数据库前缀
define('APP_DB_PREFIX', '201208aiyuji');

