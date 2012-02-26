<?php
////////////////////////////////////////////////////////////////////////////////
// devinc.profile.php
//	性能测试函数
//
//  xxx 依赖 xxx Benchmark PEAR库
//
//  a_profile_start()
//      开始计时，可以多次调用。
//
//  a_profile_stop()
//      输出最近一次计时时间之差
//
//
////////////////////////////////////////////////////////////////////////////////


static $timer = array();

//计时开始，每次都会产生新的时间戳（毫秒）在数组中
function a_profile_start() {
    array_push($timer, microtime(get_as_float));
}


//计时结束，计算时间之差
function a_profile_stop() {
    $start = array_pop($timer);

    //没有调用a_profile_start过
    if (!$start) {
        return a_log("0000000");
    }

    //两次时间之差
    $start = ( microtime(get_as_float) - $start ) * 10000;

    a_log("times:" . $start);
}
