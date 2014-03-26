<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SearchChannelHeader</title>
<link rel="stylesheet" type="text/css" href="/css/searchStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
</head>

<body>
<?php 
if(isset($_SESSION['username']) && $_SESSION['username'] != NULL) {
	$username = $_SESSION['username'];
	echo '<div>';
	include("./loginHeader.php");
	echo '</div>';
} else {
	print "<meta http-equiv='refresh' content='0;url=index.php'>";
}
?>
<form name="searchchannelform" method="get">
<br/>
<table align="center">
	<tr>
		<td> 
			<select name="mediatype" id="mediatype"> 
 				<option value="N">Select Type</option> 
				<option value="A">Audio</option> 
				<option value="V">Video</option>
				<option value="I">Image</option>
			</select>
		</td>
		<td> <input type="text" size="60" class="inputtext" maxlength="300" name="searchchannelinput" id="searchchannelinput"/></td>
		<td> <input type="submit" name="searchchannelbutton" value="Search Channel" style="background:none;border:0;color:#4C4646;font-size: 18px;"/> </td>		
	</tr>	
</table>

<br/>
<br/>
<br/>
<br/>
<br/>
<table align="left">
	<tr><td> <strong><font size="4">&nbsp;Search Channel</font></strong> </td></tr>
	<tr>	
		<td> &nbsp;
			<select name="mediatypeforchannel" id="mediatypeforchannel"> 
 				<option value="N">Select Type</option> 
				<option value="A">Audio</option> 
				<option value="V">Video</option>
				<option value="I">Image</option>
			</select>
		</td>
	</tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>	
	<tr><td> <input type="submit" name="uploads" value="Uploads" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="favorites" value="Favorites" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td>  <input type="submit" name="subscriptions" value="Subscriptions" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="createplaylist" value="Create a Playlist" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
		
	<?php 
		$loadplaylistsquery = sprintf("SELECT PLAYLIST_ID, PLAYLIST_NAME
							  	  	   FROM MT_USER_PLAYLIST WHERE
								  	   USERNAME = '$username'");
		$loadplaylists = mysql_query($loadplaylistsquery) or die('Failed to load playlists');
		if((mysql_num_rows($loadplaylists)) > 0) {
			echo "<tr><td>&nbsp;&nbsp;";
			echo "<select name='playlists' id='playlists'>"; 
			echo "<option value='0'>Select Playlist</option>";
			while($playlistresult = mysql_fetch_array($loadplaylists)) {
				$playlistid = $playlistresult["PLAYLIST_ID"];
				$playlistidentity = $playlistresult["PLAYLIST_NAME"];
				echo "<option value='$playlistid'>$playlistidentity</option>";
			}
			echo "</select>";
			echo "<input type='submit' name='selectplaylist' value='Select Playlist' style='background:none;border:0;color:#4C4646;font-size: 16px;'/> </td></tr>";
		}
	?>	
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	
	<tr><td> <strong><font size="4">&nbsp;Update Your Profile</font></strong> </td></tr>
	<tr><td> <input type="submit" name="updateprofile" value="Update Profile" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="updateemail" value="Update Email" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
		
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr><td> <strong><font size="4">&nbsp;Manage Friends</font></strong> </td></tr>
	<?php 
	$userscheckquery = sprintf("SELECT USER_CONTACT_ID FROM MT_USER_CONTACTS WHERE
								USERNAME = '$username' AND IS_FRIEND = 'A'");
	$userscheck = mysql_query($userscheckquery) or die('Failed to check friends');
	if((mysql_num_rows($userscheck)) == 0) {	
	?>
	<tr><td> <input type="submit" name="friendrequests" value="Friend Requests" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<?php 
	} else {
	?>
	<tr><td> <input type="submit" name="friendrequests" value="Friend Requests" style="background:none;border:0;color:#4C4646;font-size: 18px;"/> </td></tr>
	<?php 
	}
	?>
	<tr><td> <input type="submit" name="searchfriend" value="Search Friends" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<?php 
	$unreadmessagequery = sprintf("SELECT FROM_USERNAME FROM MT_USER_MESSAGES WHERE
								   TO_USERNAME = '$username' AND IS_MESSAGE_VIEWED = 'N'");
	$unreadmessages = mysql_query($unreadmessagequery) or die('Failed to check unread messages');
	if(mysql_num_rows($unreadmessages) == 0) {
	?>
	<tr><td> <input type="submit" name="chat" value="Chat" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<?php 
	} else {
	?>
	<tr><td> <input type="submit" name="chat" value="Chat" style="background:none;border:0;color:#4C4646;font-size: 18px;"/> </td></tr>
	<?php 
	}
	?>
	<tr><td> <input type="submit" name="blocked" value="Blocked Users" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr><td> <strong><font size="4">&nbsp;Manage Groups</font></strong> </td></tr>
	<tr><td> <input type="submit" name="creategroup" value="Create a Group" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="mygroups" value="My Groups" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="joinedgroups" value="Joined Groups" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="joingroups" value="Join Groups" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="unjoingroups" value="Unjoin Groups" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
</table>

</form>
</body>
</html>