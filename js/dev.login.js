var submit = function() {
    a_ajax({
	    "url"	: "/login.php",
	    "form"	: "login_form",
	    "callback"	: function () {
		console.log(this.getResponseHeader("Referer"));
		}
	    });

}
