<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.smarty.php
//	smarty渲染的函数
//
//	
//	a_smarty($tpl, $arg)
//	    将数据合并到模板中，供模板输出
//
//
////////////////////////////////////////////////////////////////////////////////


require_once(ROOT_DIR . '/dev/smarty/libs/Smarty.class.php');


function a_smarty($shtml, &$arg=array()) {
    $smarty = new Smarty();

    $smarty->setCacheDir(ROOT_DIR . "/cache");
    $smarty->setConfigDir(ROOT_DIR . "/dev/smarty/configs");
    $smarty->setCompileDir(ROOT_DIR . "/dev/smarty/templates_c");
    $smarty->setTemplateDir(ROOT_DIR . "/dev/smarty/templates");


    foreach ($arg as $key => $value) {
	$smarty->assign($key, $value);
    }

    $smarty->display($shtml);
}
