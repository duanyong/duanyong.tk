<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.action.php
//	页面操作相关的函数
//
//
//	a_action_done($tpl)
//	    接收smarty模板，将其渲染出来
//
//
////////////////////////////////////////////////////////////////////////////////

function a_action_done($tpl) {
    global $arg;

    if (a_bad_string($tpl)
	|| a_bad_array($arg)
    ) {
	a_log();
    }

    // 取得tpl的绝对路径
    if (strpos($tpl, ROOT_DIR) !== 0) {
	// 构建绝对路径

	$tpl = ROOT_DIR . $tpl;
    }


    if (a_bad_file($tpl)) {
	// 文件不可读
	return a_log();
    }

    // 交给smarty去渲染出页面
    a_smarty($tpl, $arg);
}

