<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.modle.php
//
//
//	a_cookie_set($name, $value, $day=0)
//	    向客户端设置cookie, $day开之后失效。如果不设置，者浏览器关闭后就失败
//
//	a_cookie_get($name)
//	    接收smarty模板，将其渲染出来
//
//
////////////////////////////////////////////////////////////////////////////////


function a_model($table, $pid=false) {
    if (a_bad_string($table)) {
        return a_log_arg();
    }

    if ($pid
        && ( $pid = intval($pid) )
    ) {
        return a_db_primary($table, $pid);
    }


    $table = a_db_desc($table);
}

function a_model_field($table) {
    if (a_bad_string($table)) {
        return a_log_arg();
    }

    if (false === ( $desc = a_db_desc($table) )) {
        return false;
    }

    $model = array();

    foreach ($desc as &$d) {
        $field = $d["Field"];

        if (isset($post[$field])) {
            $model[$field] = $post[$filed];
        }

        unset($d);
    }

    return $model;
}
