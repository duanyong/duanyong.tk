// 根据不同的浏览器取得事件元素
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
}
