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


require_once(ROOT_DIR . '/dev/smarty/Smarty.class.php');

function a_smarty_object() {
    //生成新的Smarty对象，渲染页面
    $smarty = new Smarty();

    $smarty->setCacheDir(TMP_DIR);
    $smarty->setCompileDir(TMP_DIR . SEPARATOR . ".templates_c");
    $smarty->setTemplateDir(ROOT_DIR);
    $smarty->addPluginsDir(ROOT_DIR . "/dev/smarty/userplugins");

    return $smarty;
}


function a_smarty($tpl, &$assign=false) {
    if (!is_file($tpl)
	|| !is_readable($tpl)
    ) {
	return a_log();
    }

    $smarty = a_smarty_object();

    if (a_bad_array($assign)) {
	$smarty->assign($assign);
    }

    $smarty->display($tpl);
}


//返回渲染tpl后的签字串
function a_smarty_tpl($tpl) {
    if (!is_file($tpl)
	|| !is_readable($tpl)
    ) {
	return a_log();
    }


    $smarty = a_smarty_object();

    try {
	return  $smarty->fetch ($tpl);

    } catch (SmartyException $se) {

	return a_log($se->getMessage());
    }
}
