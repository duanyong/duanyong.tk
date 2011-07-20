<?php
//将file对应的js文件从/js/*.js外联进来
//  {js file="base, login"}
//
function smarty_function_js($params, $template) {
    if (empty($params["file"])) {
	//参数错误，返回空字符串

	return "";
    }

    //是否为内嵌
    if ($params["inner"] === true) {
	return smarty_function_innerjs($params, $template);
    }

    //外联js
    $jses   = array();
    $files  = explode(",", str_replace(" ", "", $params["file"]));

    foreach ($files as &$js) {
	//测试文件是否可读或者存在
	$file = ROOT_DIR . "/js/{$js}.js";

	if (!file_exists($file)
	    || !is_readable($file)
	) {
	    //文件不可读
	    continue;
	}


	$jses[] = "<script type=\"text/javascript\" src=\"/js/{$js}.js\"></script>";

	unset($js);
    }

    return implode("\n", $jses);
}


//将file对应的js文件从/js/*.js内嵌进来
//  {js file="base, login" inner=true}
//
function smarty_function_innerjs($params, $template) {
    if (empty($params["file"])) {
	//参数错误，返回空字符串

	return "";
    }

    $content	= "";
    $files	= explode(",", trim($params["file"]));

    foreach ($files as $js) {
	$file = ROOT_DIR . "/js/{$js}.js";

	if (file_exists($file)
	    && is_readable($file)
	) {

	    $content .= file_get_contents($file) . "\n";
	}
    }

    return "<script>" . $content . "</script>";
}
?>
