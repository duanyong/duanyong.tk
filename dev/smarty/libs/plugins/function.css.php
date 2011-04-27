<?php
//将name对应的css文件从/css/*.css外联进来
//  {css name="layout, index"}
//
function smarty_function_css($params, $template) {
    if (empty($params["name"])) {
	//参数错误，返回空字符串

	return "";
    }


    //是否为内嵌
    if ($params["inner"] === true) {
	return smarty_function_innercss($params, $template);
    }

    //外联css
    $csses  = array();
    $files  = explode(",", str_replace(" ", "", $params["name"]));

    foreach ($files as &$file) {
	$csses[] = '<link rel="stylesheet" type="text/css" href="/css/' . $file . '.css" media="screen" />';
    }

    return implode("\n", $csses);
}


//将name对应的css文件从/css/*.css内嵌进来
//  {css name="layout, index" inner=true}
//
function smarty_function_innercss($params, $template) {
    if (empty($params["name"])) {
	return "";
    }

    $content	= "";
    $files	= explode(",", str_replace(" ", "", $params["name"]));

    foreach ($files as $css) {
	$file = ROOT_DIR . "/css/{$css}.css";

	if (file_exists($file)
	    && is_readable($file)
	) {

	    $content .= file_get_contents($file) . "\n";
	}
    }

    return '<link type="text/css" rel="stylesheet" media="screen">' . $content . '</link>';
}

?>
