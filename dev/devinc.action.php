<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.action.php
//	页面操作相关的函数
//
//
//	a_action_msg($msg)
//	    显示提示的信息，然后返回页面
//
//	a_action_page($tpl, $url, $stay)
//	    显示提示的信息，然后返回页面
//
//	a_action_redirect($msg)
//	    显示提示的信息，然后返回页面
//
//
//	a_action_ajax()
//	    返回ajax请求提交的数据
//
//
////////////////////////////////////////////////////////////////////////////////

function a_action_uuid() {
    $uuid = date('Y-m-d H:i:s')
        . rand(1000000, 9000000)
        . rand(1000000, 9000000)
        . rand(1000000, 9000000)
        . rand(1000000, 9000000)
        . rand(1000000, 9000000)
        . rand(1000000, 9000000)
        . rand(1000000, 9000000)
        . $_SERVER['SCRIPT_FILENAME'];


    return md5($uuid);
}


//如果为ajax提交，直接返回json数据，否则返回页面
function a_action_page($tpl=false, $url=false, $stay=false) {
    global $arg;

    if (a_action_ajax()) {
        //ajax提交
        return json_encode(array(
            "url"   => $url,
            "err"   => $arg["err"],
            "msg"   => $arg["msg"],
        ));
    }


    if ($tpl === false) {
        global $_SERVER;

        $tpl = $_SERVER["REQUEST_URI"];
    }

    if (a_bad_string($tpl)) {
        return a_log();
    }


    //从用户的请求URL中得到渲染那个tpl文件
    $tpl = str_replace(array(".php", ".tpl"), "", $tpl);
    $tpl .= ".tpl";

    //取得tpl的绝对路径
    if (strpos($tpl, ROOT_DIR) !== 0) {
        //构建文件的的绝对路径后给smarty渲染数据
        $tpl = ROOT_DIR . ( strpos($tpl, '/') === 0 ? '' : '/' ) .  $tpl;
    }

    if (a_bad_file($tpl)) {
        //文件不可读
        return a_log();
    }

    if (isset($url)) {
        $arg["url"] = $url;
    }

    //交给smarty去渲染出页面
    a_smarty($tpl, $arg);
}


//返回json格式
function a_action_json($array) {
    if (!is_array($array)) {
        echo json_encode(array(
            'error'     => 500,
            'errmsg'    => '无数据输出',
        ));

        return ;
    }

    echo json_encode($array);
}


function a_action_msg($msg=false, $tpl=false) {
    if (a_bad_string($msg)) {
        //参数有问题
        return a_log();
    }

    global $arg;

    $arg["msg"] = $msg;

    //得到referer，好让用户返回
    if (a_bad_string($_SERVER["HTTP_REFERER"], $referer)) {
        //是否以站内连接
        $referer = "";
    }

    if ($tpl === false) {
        $tpl = "/error.tpl";
    }


    //如果设置err值，那么返回正确的页面，否则返回error.tpl页面
    return a_action_page($tpl, $referer);
}


//页面跳转
function a_action_redirect($url="/", $msg=false, $delay=false) {
    if (a_bad_string($url)) {
        return a_log();
    }

    header("Location: {$url}");

    return "";
}


//服务器发生异常
function a_action_error() {
    global $arg;

    $arg["err"] = "servererror";
    $arg["msg"] = "对不起，服务器出现异常，给您带来不便请谅解。请稍后再试。";

    return a_action_page("/error.tpl");
}



function a_action_time() {
    global $_SERVER;

    return $_SERVER["REQUEST_TIME"];
}


function a_action_ip() {
    global $_SERVER;

    return $_SERVER["REMOTE_ADDR"];
}



//返回ajax请求提交的数据
function a_action_ajax() {
    return false;
}
