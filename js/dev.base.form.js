var a_form_serialize = function(form) {
    if (a_bad_element( form = a_$(form) )) {
	return a_log();
    }

    var elements;

    if (a_bad_array( elements = form.elements ) ) {
	return {};
    }

    // 对form中的各个元素的name取值，根据类型设置返回对应的值
    for (var name, value, ret={}, len=elements.length-1; len>=0; --len) {
	if (a_bad_element( element = elements[len] )
		|| !( name  = element.name )
		|| !( tname = element.tagName )
	   ) {
	    continue;
	}

	tname = tname.toLowerCase();

	if (tname === "input") {
	    // 简单值
	    value = a_form_input_value(element);

	} else if (tname === "select") {
	    return "select";
	}

	if (!value) {
	    continue;
	}

	ret[name] = value;
    }

    return a_bad_object(ret) ? false : ret;
};


var a_form_input_value = function(input) {
    if (!( input = a_$(input) )
	    || input.tagName.toLowerCase() !== "input"
       ) {
	return a_log();
    }

    var type = input.type.toLowerCase();

    if (type === "text"
	    || type === "file"
	    || type === "hidden"
	    || type === "password"
       ) {
	return input.value;

    } else if (type === "radio"
	    || type === "checkbox"
	    ) {
	return input.checked ? input.value : false;
    }

    return false;
};
