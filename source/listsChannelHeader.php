<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ListChannelHeader</title>
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
		<td>&nbsp;
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
        $stmt = mysqli_prepare($dbconnection, "SELECT PLAYLIST_ID, PLAYLIST_NAME FROM MT_USER_PLAYLIST WHERE USERNAME = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $loadplaylists = mysqli_stmt_get_result($stmt) or die('Failed to load playlists');
        mysqli_stmt_close($stmt);

		if((mysqli_num_rows($loadplaylists)) > 0) {
			echo "<tr><td>&nbsp;&nbsp;";
			echo "<select name='playlists' id='playlists'>"; 
			echo "<option value='0'>Select Playlist</option>";
			while($playlistresult = mysqli_fetch_array($loadplaylists)) {
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
</table>

</form>
</body>
</html>