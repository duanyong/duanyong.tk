var a_log = function(log) {
    if (console
	    && console.log
       ) {
	console.log(log === undefined ? this : log);
    }
}
