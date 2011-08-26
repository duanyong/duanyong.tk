<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.compress.php
//	压缩静态文件（js与css）
//
//  
//	a_compress_css($string)
//	    将字符串中的注释与不需要的空白去除
//  
//
////////////////////////////////////////////////////////////////////////////////


//将css文件中的无用空白与注释去除
function a_compress_css(&$string) {
    /* remove comments */
    $string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $string);

    /* 清楚 tabs, 空格, 新行, 等. */
    $string = str_replace(array("\r\n", "\r", "\n", "\t", '   ', '    '), '', $string);
    $string = str_replace(array(', ', ': ', '; ', ' {', '{ ', ' }'), array(',', ':', ';', '{', '{', '}'), $string);

    return $string;
}
