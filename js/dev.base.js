var a_$ = function(element) {
   return a_type(element) === "element" ? element : document.getElementById(element);
}

var a_type = function(item) {
    if (item == null) return 'null';
    //if (item.$family) return item.$family();

    if (item.nodeName){
	if (item.nodeType == 1) return 'element';
	if (item.nodeType == 3) return (/\S/).test(item.nodeValue) ? 'textnode' : 'whitespace';
    } else if (typeof item.length == 'number'){
	if (item.callee) return 'arguments';
	//if ('item' in item) return 'collection';
    }

    return typeof item;
}

var a_merge = function() {
    var ret = {};

    if (arguments.length === 0) {
	return ret;
    }

    for (var obj, key, len=arguments.length; len>=0; --len) {
	if (a_type( obj = arguments[len] ) !== "object") {
	    continue;
	}

	for (key in obj) {
	    ret[key] = obj[key];
	}
    }

    return ret;
}
