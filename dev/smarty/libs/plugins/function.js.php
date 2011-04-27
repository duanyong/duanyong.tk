<?php
//将name对应的js文件从/js/*.js外联进来
//  {js name="base, login"}
//
function smarty_function_js($params, $template) {
    if (empty($params["name"])) {
	//参数错误，返回空字符串

	return "";
    }

    //是否为内嵌
    if ($params["inner"] === true) {
	return smarty_function_innerjs($params, $template);
    }

    //外联js
    $jses   = array();
    $files  = explode(",", str_replace(" ", "", $params["name"]));

    foreach ($files as $js) {
	$jses[] = '<script type="text/javascript" src="/js/' . $js . '.js" />';
    }

    return implode("\n", $jses);
}


//将name对应的js文件从/js/*.js内嵌进来
//  {js name="base, login" inner=true}
//
function smarty_function_innerjs($params, $template) {
    if (empty($params["name"])) {
	//参数错误，返回空字符串

	return "";
    }

    $content	= "";
    $files	= explode(",", trim($params["name"]));

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
