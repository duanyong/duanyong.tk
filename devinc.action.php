<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.action.php
//	页面操作相关的函数
//
//
//	a_action_done($tpl)
//	    接收smarty模板，将其渲染出来
//
//	a_action_is_get()
//	    返回true表示是GET请求
//
//	a_action_is_post()
//	    返回true表示是POST请求
//
//
////////////////////////////////////////////////////////////////////////////////


function a_action_done() {
    global $arg;
    global $_SERVER;

    // 先做ajax判断，如果是直接返回json_encode($arg);
    if (!a_bad_ajax()) {
	// 直接返回json格式

	return json_encode($arg);
    }


    if (a_bad_string($_SERVER["REQUEST_URI"], $tpl)) {
	return a_log();
    }
    
    // 从用户的请求URL中得到渲染那个tpl文件
    $tpl = str_replace(".php", "", $tpl);
    $tpl .= ".tpl";


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


function a_action_timestamp() {
    global $_SERVER;

    return $_SERVER["REQUEST_TIME"];
}


function a_action_ip() {
    global $_SERVER;

    return $_SERVER["REMOTE_ADDR"];
}



function a_action_redirect($url="/", $msg="", $delay=3) {
    global $arg;

    $arg["delay"]	= $delay;
    $arg["errmsg"]	= $msg;
    $arg["redirection"] = $url;


    // 准备页面跳转
    header("Status: 200");
    header("Referer: {$url}");


    a_action_done();
}


//TODO 
// 查看浏览器cookie中取得uid再和cookie中password与数据库中的密码是否匹配
function a_action_user() {
    // 从浏览器的Cookie中取
    return array(
	"uid"	    => 1,
	"mobile"    => "15821231614",
    );

    // 可能没有数据
    if (a_bad_table_id("user", $uid, $user) ) {

	return false;
    }

    // 用户是否被禁言
    return $user["status"] != 44;

    //只有._-及非数字开头的英文字母，数字符合要求
    if (a_bad_string($username)) {
	return false;
    }

    if ($var !== false) {
	$var = $username;
    }

    return true;
}
