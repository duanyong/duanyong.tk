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


static $timer   = array();


//计时开始，每次都会产生新的时间戳（毫秒）在数组中
function a_profile_start($string = null) {
    global $timer;

    array_push($timer, microtime());
}


//计时结束，计算时间之差
function a_profile_stop() {
    global $timer;

    $stop  = microtime();
    $start = array_pop($timer);

    //没有调用a_profile_start过
    if (!$start) {

        return a_log("profile failed, because not started.");
    }


    $stop = explode(" ", $stop);
    $stop = $stop[1] . substr($stop[0], 1, strlen($stop[0]));

    $start = explode(" ", $start);
    $start = $start[1] . substr($start[0], 1, strlen($start[0]));

    $time  = bcsub($stop, $start, 4);

    if ($time >= 1) {
        $time .= " sec";

    } else {
        $time .= " ms";
    }

    //两次时间之差
    $output = "time elapse:\n";
    $output .= str_pad("start", 25, "-") . str_pad("stop", 25, "-") . str_pad("total", 25, "-") . "\n";
    $output .= str_pad($start, 25, " ") . str_pad($stop,  25, " ") . $time . "\n";


    a_log($output);
}

