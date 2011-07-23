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
    $files  = explode(",", str_replace(" ", "", $params["name"]));

    foreach ($files as &$css) {
	$file = ROOT_DIR . "/css/{$css}.css";

	if (!file_exists($file)
	    || !is_readable($file)
	) {
	    //文件不可读
	    continue;
	}

	$csses[] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$css}\" media=\"screen\"></link>";

	unset($css);
    }

    return implode("", $csses);
}
