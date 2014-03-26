function registrationFields() {
 	
	var firstname=document.getElementById('firstname').value;
	if(firstname=="") {
		alert("Please enter your first name");
		document.getElementById('firstname').focus;
		return false;
	}
	if(firstname.length>20) {
		alert("First name cannot exceed 20 characters");
		document.getElementById('firstname').focus;
		return false;
	}
	
	var lastname=document.getElementById('lastname').value;
	if(lastname=="") {
		alert("Please enter your last name");
		document.getElementById('lastname').focus;
		return false;
	}
	if(lastname.length>20) {
		alert("Last name cannot exceed 20 characters");
		document.getElementById('lastname').focus;
		return false;
	}

	var email=document.getElementById('email').value;
	if(email=="") {
		alert("Please enter your email");
		document.getElementById('email').focus;
		return false;
	}
	if(!isValidEmailid(email)) {
		alert("Please enter a valid email ID");
		document.getElementById('email').focus;
		return false;
	}

	var username=document.getElementById('username').value;
	if(username=="") {
		alert("Please enter any username");
		document.getElementById('username').focus;
		return false;
	}
	if(!isValidUsername(username)) {
	   alert("Username can contain only alphabets or numbers or both");
	   document.getElementById('username').focus;
	   return false;
	}
	
	var password=document.getElementById('password').value;
	if(password=="") {
		alert("Please enter a new password");
		document.getElementById('password').focus;
		return false;
	}
	if(password.length<8 || password.length>16) {
		 alert("Password should have minimum 8 characters and maximum 16 characters");
		 document.getElementById('password').focus;
		 return false;
	}
	
	var confirmpassword=document.getElementById('confirmPassword').value;
	if(confirmpassword=="") {
		alert("Please re-enter your password");
		document.getElementById('confirmpassword').focus;
		return false;
	}

}

function isValidUsername(username) {
	return /^[A-Za-z0-9]/.test(username);
}

function isValidEmailid(email) {
	return /^([a-zA-Z0-9_\.]+)@[a-z0-9]+(\.[a-z0-9]+)*(\.[a-z]{2,})$/.test(email);
}



