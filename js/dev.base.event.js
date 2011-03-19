/*
 * 基础函系列 - a_event_xxx()
 *
 *  a_event_target(e)
 *	返回事件源的dom元素
 *
 * */


// 获取事件源的元素
var a_event_target = function(e) {
    if (a_type(e) === "event") {
	return a_log();
    }

    if (a_brower_isIE()) {
	return e.srcElement;
    } else {
	return e.target;
    }

    return null;
};
