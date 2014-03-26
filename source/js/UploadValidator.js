function uploadFields() {
 	
	var title=document.getElementById('title').value;
	if(!isValid(title)) {
		   alert("Title can contain only alphabets or numbers or both");
		   document.getElementById('title').focus;
		   return false;
	}

	var tags=document.getElementById('tags').value;
	if(!isValid(tags)) {
		   alert("Tags can contain only alphabets or numbers or both");
		   document.getElementById('tags').focus;
		   return false;
	}
}

function isValid(field) {
	return /^[A-Za-z0-9]/.test(field);
}
