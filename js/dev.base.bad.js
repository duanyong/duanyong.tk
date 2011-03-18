var a_bad_element = function(element) {
    return a_type( element = a_$(element) ) !== "element";
}

var a_bad_number = function(num) {
    return !(/^\d+$/.test(num));
}

var a_bad_string = function(str) {
    return a_type(str) !== "string" && str === "";
}

var a_bad_array = function(arr) {
    return a_type(arr) !== "array" && arr.length === 0;
}

var a_bad_object = function(obj) {
    if ( a_type(obj) !== "object" ) {
	return true;
    }

    for (var k in obj) {
	return false
    }

    return true;
}

var a_bad_function = function(func) {
    return a_type(func) !== "function";
}
