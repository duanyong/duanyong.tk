<?php
//将name对应的css文件从/css/*.css外联进来
//  {css name="layout, index"}
//
function smarty_function_css($params, $template) {
    if (empty($params["name"])) {
	//参数错误，返回空字符串
	return "";
    }

    //外联css
    $csses  = array();
    $files  = explode(",", str_replace(" ", "", $params["file"]));

    foreach ($files as &$css) {
	$file = ROOT_DIR . "/css/{$css}.css";

	if (!file_exists($file)
	    || !is_readable($file)
	) {
	    //文件不可读
	    continue;
	}

	$csses[] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/{$css}.css\" media=\"screen\"></link>";

	unset($css);
    }

    return implode("\n", $csses);
}


//将file对应的css文件从/css/*.css内嵌进来
//  {css file="layout, index" inner=true}
//
function smarty_function_innercss($params, $template) {
    if (empty($params["file"])) {
	return "";
    }

    $content	= "";
    $files	= explode(",", str_replace(" ", "", $params["file"]));

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
