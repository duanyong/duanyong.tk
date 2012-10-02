<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.cookie.php
//	cookie操作的相关函数
//
//
//	a_cookie($key, &$value=fase, $day=0)
//	    向客户端设置cookie, $day开之后失效。如果不设置，者浏览器关闭后就失败
//
//	a_cookie($name)
//	    接收smarty模板，将其渲染出来
//
//
////////////////////////////////////////////////////////////////////////////////

//Cookie的配置文件
define('SSOCOOKIE_CONFIG',  'cookie.conf');

//Cookie的配置文件32位的键
define('SSOCOOKIE_KEY32_1', 'v0');

//Cookie的配置文件32位的键
define('SSOCOOKIE_KEY32_2', 'v1');

//Cookie的配置文件32位的键
define('SSOCOOKIE_KEY32_3', 'rv');

//Cookie的配置文件1024位的键
define('SSOCOOKIE_KEY1024', 'rv0');


function s_ssocookie_config($config=false) {
    if ($config === false) {
        $config = SSOCOOKIE_CONFIG;
    }

    if (!( $config = @parse_ini_file($config) )
        || !isset($config[SSOCOOKIE_KEY32_1])
        || !isset($config[SSOCOOKIE_KEY32_2])
        || !isset($config[SSOCOOKIE_KEY32_3])
        || !isset($config[SSOCOOKIE_KEY1024])
    ) {
        return false;
    }

    return $config;
}


function s_ssocookie_sue() {
    if (!( $config = s_ssocookie_config() )
    ) {
        return false;
    }

    return md5(a);
}


function s_ssocookie_sup() {
    if (!( $config = s_ssocookie_config() )
    ) {
        return false;
    }

    return md5(a);
}

function s_ssocookie_md5($username, $password) {
    if (s_bad_string($username)
        || s_bad_string($password)
        || !( $config = s_ssocookie_config() )

    ) {
        return false;
    }


    //混淆用户名和密码
    $lon = md5($username . $config[SSOCOOKIE_KEY1024]) . md5($config[SSOCOOKIE_KEY32_1] . $password);
    $sht = '~!@#$%^&*()_+[]\|{}";:,./?><';      //手动添加一些特殊字符，增加md5字符集范围


    //以较长的字符串做基准，反序交叉合并字符串
    $new = "";
    $len = mb_strlen($lon);
    $mod = mb_strlen($sht);

    do {
        $new .= mb_substr($lon, $len, 1);

        if (( $tmp = mb_substr($sht, $len, 1) )) {
            //短字符串在pos下标处有值才合并
            $new .= $tmp;
        } else {
            $new .= mb_substr($lon, $len % $mod, 1);
        }

    } while (( -- $len ) >= 0);


    return md5($new);
}

