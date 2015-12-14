function validateForm() {
	if (typeof document.forms["linkus"] !== 'undefined') {
		var x = document.forms["linkus"]["name"].value;
		if (x == null || x == "") {
			alert("Name must be filled out");
			return false;
		} 
	}
	if (typeof document.forms["links"] !== 'undefined') {
	    var x = document.forms["links"]["name"].value;
		if (x == null || x == "") {
			alert("Name must be filled out");
			return false;
		}
	    var y = document.forms["links"]["url"].value;
		if (y == null || y == "") {
			alert("URL must be filled out");
			return false;
		}
	}
}