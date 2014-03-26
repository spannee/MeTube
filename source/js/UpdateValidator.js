function registrationFields() {
var email=document.getElementById('email').value;
	
	if(!isValidEmailid(email)) {
		alert("Please enter a valid email ID");
		document.getElementById('email').focus;
		return false;
	}
	
}
function isValidEmailid(email) {
	return /^([a-zA-Z0-9_\.]+)@[a-z0-9]+(\.[a-z0-9]+)*(\.[a-z]{2,})$/.test(email);
}