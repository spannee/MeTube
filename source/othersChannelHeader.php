<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>OthersChannelHeader</title>
<link rel="stylesheet" type="text/css" href="/css/searchStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
</head>

<body>
<?php 
if(isset($_SESSION['contentownercontent'])) {
	$contentowner = $_SESSION['contentownercontent'];
} elseif(isset($_SESSION['contentownerplaylist'])) {
	$contentowner = $_SESSION['contentownerplaylist'];
} elseif(isset($_SESSION['contentownerfavorites'])) {
	$contentowner = $_SESSION['contentownerfavorites'];
} elseif(isset($_SESSION['contentowneruploads'])) {
	$contentowner = $_SESSION['contentowneruploads'];
} elseif(isset($_SESSION['contentownersearch'])) {
	$contentowner = $_SESSION['contentownersearch'];
} elseif(isset($_SESSION['contentfriendrequestsearch'])) {
	$contentowner = $_SESSION['contentfriendrequestsearch'];
} elseif(isset($_SESSION['contentownersubscribe'])) {
	$contentowner = $_SESSION['contentownersubscribe'];
}

if(isset($_SESSION['username']) && $_SESSION['username'] != NULL) {
	$username = $_SESSION['username'];
	echo '<div>';
	include("./loginHeader.php");
	echo '</div>';
} else {
	print "<meta http-equiv='refresh' content='0;url=index.php'>";
}

?>
<form name="otherschannelform" method="get">
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<table align="left">
	<tr><td> <strong><font size="4">&nbsp;Search <?php echo ucfirst($contentowner); ?>'s Channel</font></strong> </td></tr>
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
	<tr><td> <input type="submit" name="uploadsofothers" value="Uploads" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="favoritesofothers" value="Favorites" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<?php 
		$loadplaylistsquery = sprintf("SELECT PLAYLIST_ID, PLAYLIST_NAME
							  	  	   FROM MT_USER_PLAYLIST WHERE
								  	   USERNAME = '$contentowner'");
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
			echo "<input type='submit' name='selectplaylistofothers' value='Select Playlist' style='background:none;border:0;color:#4C4646;font-size: 16px;'/> </td></tr>";
		}
	?>	
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
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<?php 
	$friendcheck = sprintf("SELECT IS_FRIEND  FROM
							MT_USER_CONTACTS WHERE
							USERNAME = '$username' AND
							USER_CONTACT_ID = '$contentowner'");
	$check = mysql_query($friendcheck) or die('Failed to check friends');
	$isfriend = mysql_fetch_row($check);
	
	if($isfriend[0] == 'N' || $isfriend[0] == '0' || $isfriend[0] == NULL) {
		echo "<tr><td> <input type='submit' name='addfriend' value='Add Friend' style='background:none;border:0;color:#4C4646;font-size: 16px;' /> </td></tr>";
	} elseif($isfriend[0] == 'G') {
		echo "<tr><td> <input type='submit' name='removerequest' value='Remove Request' style='background:none;border:0;color:#4C4646;font-size: 16px;'/> </td></tr>";
	} elseif($isfriend[0] == 'A') {
		echo "<tr><td> <input type='submit' name='approverequest' value='Approve Request' style='background:none;border:0;color:#4C4646;font-size: 16px;'/> </td></tr>";
	} elseif($isfriend[0] == 'Y') {
		echo "<tr><td> <input type='submit' name='deletefriend' value='Delete Friend' style='background:none;border:0;color:#4C4646;font-size: 16px;'/> </td></tr>";
	}
	
	?>	
	
	<?php 
	$subscriptioncheck = sprintf("SELECT *  FROM
							MT_CHANNEL_SUBSCRIBERS WHERE
							CHANNEL_ID = '$contentowner' AND
							USERNAME = '$username'");
	$check = mysql_query($subscriptioncheck) or die('Failed to check subscription');
	
	if(mysql_num_rows($check) == 0) {
	?>
	<tr><td> <input type="submit" name="subscribe" value="Subscribe" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<?php 
	} else {
	?>
	<tr><td> <input type="submit" name="unsubscribe" value="UnSubscribe" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<?php 
	}
	?>
	<tr><td> <input type="submit" name="block" value="Block" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
</table>

</form>
</body>
</html>