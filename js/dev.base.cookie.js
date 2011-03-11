
var a_cookie_set = function(name, value) {
    document.cookie = name + "="+ escape (value) + ";";
}

var a_cookie_get = function(name) {
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));

     return arr != null ? unescape(arr[2]) : null;
}
