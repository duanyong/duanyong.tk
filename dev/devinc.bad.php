<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.bad.php
//	判断错误的函数，参数错误返回true，正确返回false
//
//  a_bad_username($username, &$var)
//      判断用户名是否正确
//
//  a_bad_password($password, &$var)
//      判断用户名是否正确
//
//  
//	a_bad_id($id)
//	    判断数字是否正确（大于0）
//  
//	a_bad_string($string)
//	    判断字符串是否正确
//
//	a_bad_array($string, &$var)
//	    判断数组否是正确并赋值给$var变量
//
//  a_bad_email($email, $var)
//	    判断邮箱地址是否正确
//
//  a_bad_post($key, $var=false, $type="string")
//      判断POST中的$key是否正确
//          $type为: string, int, int0, array, email, phone(包含mobile和telphone), mobile, telphone, image
//
//  a_bad_get($key, $var=false, $method="string")
//    判断GET中的$key是否正确
//          $type为: string, int, int0, array, email, phone(包含mobile和telphone), mobile, telphone, image
//
//
////////////////////////////////////////////////////////////////////////////////


//用户名检查，包括格式及长度
function a_bad_username(&$username, &$var=false) {
    //长度检查
    $len = strlen($username);

    if ($len <= 0
        || $len > 25
    ) {
        return true;
    }


    //格式检查
    if (!a_bad_email($username, $var)
        || !a_bad_mobile($username, $var)
    ) {
        //手机号码或邮件地址
        return false;
    }

    return true;
}


//密码检查，只检查长度6到25位
function a_bad_password(&$password, &$var=false) {
    $len = mb_strlen($password);

    if ($len < 6
        || $len > 25
    ) {
        return true;
    }

    if ($var !== false) {
        $var = $password;
    }

    return false;
}


function a_bad_id(&$id, &$var=false) {
    if(!is_numeric($id)
        || ( $id = intval($id) ) <= 0
    ) {
        return true;
    }

    if ($var !== false) {
        $var = $id;
    }

    return false;
}


function a_bad_0id(&$id, &$var=false) {
    if(!is_numeric($id)
        || ( $id = intval($id) ) < 0
    ) {
        return true;
    }

    if ($var !== false) {
        $var = $id;
    }

    return false;
}


function a_bad_string(&$str, &$var=false) {
    if (!is_string($str)
        || $str !== strval($str)
        || empty($str)
    ) {
        return true;
    }

    if ($var !== false) {
        $var = strval($str);
    }


    return false;
}


function a_bad_0string(&$str, &$var=false) {
    if (!is_string($str)
        || $str !== strval($str)
    ) {
        return true;
    }

    if ($var !== false) {
        $var = $str;
    }

    return false;
}

function a_bad_array(&$arr, &$var=false) {
    if (!is_array($arr)
        || empty($arr)
    ) {
        return true;
    }

    if ($var !== false) {
        $var = $arr;
    }

    return false;
}

function a_bad_email(&$email, &$var=false) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        return true;
    }

    if ($var !== false) {
        $var = $email;
    }

    return false;
}


function a_bad_mobile(&$mobile, &$var=false) {
    if (strlen($mobile) !== 11) {
        return true;
    }

    if (!preg_match("/^[13|15]\d+$/", $mobile)) {
        return true;
    }

    if ($var !== false) {
        $var = $mobile;
    }

    return false;
}


//返回$_POST的值，如果对应的$key不存在，返回true，否则返回false。如指定$var变量，那么$key对应的值将赋给它
// $key         $_POST的键
// &$var        如$_POST存在，赋值
// $type        $_POST值类型（string, int0, int, array, email, phone, telphone, mobile）
// $escape      $_POST值是否需要转义（防止SQL注入）
//                      
function a_bad_post($key, &$var=false, $type="string", $escape=true) {
    if (a_bad_string($key)
        || !isset($_POST[$key])
    ) {
        return true;
    }

    if ($type === "string") {
        //字符类型
        if (a_bad_string($_POST[$key], $var)) {
            //不需要转义，直接返回判断结果
            return true;
        }

        //检查post值是否需要转义
        if ($escape === true) {
            $var = a_safe_html($var);
        }

        return false;

    } else if ($type === "int") {
        //整型
        return a_bad_id($_POST[$key], $var);

    } else if ($type === "int0") {
        //整型，可以为0
        return a_bad_0id($_POST[$key], $var);

    } else if ($type === "array") {
        //数组
        return a_bad_array($_POST[$key], $var);

    } else if ($type === "email") {
        //邮箱
        return a_bad_email($_POST[$key], $var);

    } else if ($type === "phone"
        || $type === "telphone"
    ) {
        //手机或电话（只需要验证telphone，因为telphone的规则很松已经包含手机了）
        return a_bad_telphone($_POST[$key], $var);

    } else if ($type === "mobile") {
        //手机
        return a_bad_mobile($_POST[$key], $var);
    }

    return true;
}


//返回get值
function a_bad_get($key, &$var=false, $type="string", $html=true) {
    if (a_bad_string($key)
        || !isset($_GET[$key])
    ) {
        return true;
    }


    if ($type === "string") {
        //字符类型
        if ($html !== true) {
            //不需要转义，直接返回判断结果
            return a_bad_string($_GET[$key], $var);
        }

        //需要对参数转义处理
        if (true === a_bad_string($_GET[$key], $var)) {
            //不需要转义，因为参数已经验证失败
            return true;
        }

        if ($var !== false) {
            $var = a_safe_html($var);
        }

        //验证成功，此处返回
        return false;

    } else if ($type === "int") {
        //整型
        return a_bad_id($_GET[$key], $var);

    } else if ($type === "int0") {
        return a_bad_0id($_GET[$key], $var);

    } else if ($type === "email") {
        //邮箱
        return a_bad_email($_GET[$key], $var);

    } else if ($type === "phone"
        || $type === "telphone"
    ) {
        //手机或电话（只需要验证telphone，因为telphone的规则很松已经包含手机了）
        return a_bad_telphone($_GET[$key], $var);

    } else if ($type === "mobile") {
        //手机
        return a_bad_mobile($_GET[$key], $var);
    }

    return true;
}
