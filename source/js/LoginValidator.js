function loginFields() {
	
	var username=document.getElementById('username').value;
	if(username=="") {
		alert("Please enter your username");
		document.getElementById('username').focus;
		return false;
	}
	if(!isValidUsername(username)) {
	   alert("Username can contain only alphabets or numbers or both");
	   document.getElementById('username').focus;
	   return false;
	}
	
	function isValidUsername(username) {
		return /^[A-Za-z0-9]/.test(username);
	}
	
	var password=document.getElementById('password').value;
	if(password=="") {
		alert("Please enter your password");
		document.getElementById('password').focus;
		return false;
	}
	if(password.length<8 || password.length>16) {
		 alert("Password should have minimum 8 characters and maximum 16 characters");
		 document.getElementById('password').focus;
		 return false;
	}
}