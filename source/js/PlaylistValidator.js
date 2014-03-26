function userChannelfields() {
 	
	var playlistname = document.getElementById('playlistname').value;
	
	if(playlistname.length=="") {
		alert("Please enter any playlist name");
		document.getElementById('playlistname').focus;
		return false;
	}else if(playlistname.length>20) {
		alert("Playlist Name cannot exceed 20 characters");
		document.getElementById('playlistname').focus;
		return false;
	}else if(!isValidPlaylistname(playlistname)) {
		   alert("Playlist Name can contain only alphabets or numbers or both");
		   document.getElementById('playlistname').focus;
		   return false;
	}
	
}

function isValidPlaylistname(playlistidentity) {
	return /^[A-Za-z0-9]/.test(playlistidentity);
}