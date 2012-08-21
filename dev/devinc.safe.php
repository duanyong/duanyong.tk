<?php
////////////////////////////////////////////////////////////////////////////////
//
// devinc.safe.php
//	主动防御和被动防御控制
//
//
//	a_safe_value($string, $trim=false)
//      将特殊字符替换掉（', ", >, <, &），以防止sql注入
//
//
//	a_safe_html($string, $trim=false)
//      将特殊字符替换掉（', ", >, <, &），以防止CSRF攻击
//
//
//
////////////////////////////////////////////////////////////////////////////////


//将特殊字符替换掉，以防止sql注入
function a_safe_value($string, $trim=false) {
    if (a_bad_string($string, $string, $trim)) {
        return false;
    }

    $string = str_replace('\\', '\\\\', $string);
    $string = str_replace("'", "\'", $string);
    $string = str_replace('"', '\"', $string);
    $string = str_replace('>', '\>', $string);
    $string = str_replace('<', '\<', $string);

    return $string;
}


//将特殊字符替换掉，以防止CSRF攻击
function a_safe_html($string, $trim=false) {
    if (a_bad_string($string, $string, $trim)) {
        return false;
    }

    $string = str_replace('&', '&amp;', $string);
    $string = str_replace("'", '&apos;', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace('<', '&lt;', $string);

    return $string;
}

