<?php 
session_start();
if(isset($_SESSION['username']) && $_SESSION['username'] != NULL) {
	$username = $_SESSION['username'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Others Profile</title>
<link rel="stylesheet" type="text/css" href="/css/searchStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
<script type="text/javascript" src="js/PlaylistValidator.js"></script>
</head>

<body>

<?php
$dbconnection = "connection.php";

if(file_exists($dbconnection)) {
	include $dbconnection;
} else if(file_exists("../".$dbconnection)) {
	include "../".$dbconnection;
} else {
	include "../../".$dbconnection;
}

dbConnect();

if(isset($_GET['content_owner'])) {
	$_SESSION['contentfriendrequestsearch'] = $_GET['content_owner'];
	
	$contentowner = $_GET['content_owner'];
	echo "<div>";	
	include("./othersChannelHeader.php");
	echo "</div>";
}

if(isset($_GET['uploadsofothers'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	
	$mediatypeforchannel = $_GET['mediatypeforchannel'];

	if($mediatypeforchannel == 'N') {
		$mediatypeerror = 1;
	} else {
		$uploadsearch = sprintf("SELECT CONTENT_ID,
								 CONTENT_TITLE,
								 CONTENT_LOCATION
								 FROM MT_CONTENT WHERE
								 USERNAME = '$contentowner' AND
								 CONTENT_TYPE = '$mediatypeforchannel'");
		$search = mysql_query($uploadsearch) or die('Failed to search uploads');

		if((mysql_num_rows($search)) > 0) {
			echo "<br/>";
			echo "<br/>";
			echo "<table style='margin-left:300px;'>";
			$othersprofile = 1;
			while($searchresult = mysql_fetch_array($search)) {
				$contentid = $searchresult["CONTENT_ID"];
				$contenttitle = $searchresult["CONTENT_TITLE"];
				echo "<tr><td><a href='./userUploadsView.php?content_id=".$contentid."&media_type=".$mediatypeforchannel."&others_profile=".$othersprofile."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userUploadsView.php?content_id=".$contentid."&media_type=".$mediatypeforchannel."&others_profile=".$othersprofile."' class='stylish-link'><font size='4'>$contenttitle</font></a></td></tr>";
			}
			echo '</table>';
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Uploads found")';
			echo '</script>';
		}
	}
} elseif(isset($_GET['favoritesofothers'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	
	$mediatype = $_GET['mediatypeforchannel'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$contentidsinfavorites = array();
		$favoritessearch = sprintf("SELECT CONTENT_ID
									       FROM MT_USER_FAVOURITES WHERE
									       USERNAME = '$contentowner' AND
										   FAVORITES_TYPE = '$mediatype'");
		$favoritessearchquery = mysql_query($favoritessearch) or die('Failed to search favorites');
		while($favoritesearchresults = mysql_fetch_array($favoritessearchquery)) {
			$contentidsinfavorites[] = $favoritesearchresults["CONTENT_ID"];
		}
		if(!empty($contentidsinfavorites)) { 	
			foreach ($contentidsinfavorites as $id) {
				$contentsearch = sprintf("SELECT CONTENT_ID,
												 CONTENT_TITLE,
												 CONTENT_LOCATION
												 FROM MT_CONTENT WHERE
												 CONTENT_TYPE = '$mediatype' AND
												 CONTENT_ID = '$id' AND 
												 CONTENT_SHARING != 'P'");
				$valuecontentquery = mysql_query($contentsearch) or die('Failed to search favorite content');
				while($contentsearchresults = mysql_fetch_array($valuecontentquery)) {
					$contentids[] = $contentsearchresults["CONTENT_ID"];
					$contenttitles[] = $contentsearchresults["CONTENT_TITLE"];
				}
			}
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Favorites found")';
			echo '</script>';
		}
		if(!empty($contentids)) {
			$othersprofile = 1;
			echo "<table style='margin-left:300px;'>";
			for($i=0;$i<count($contentids);$i++) {
				echo "<tr><td><a href='./userFavoritesView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."&others_profile=".$othersprofile."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userFavoritesView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."&others_profile=".$othersprofile."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
			}
			echo "</table>";
		}
	}	
} elseif(isset($_GET['selectplaylistofothers'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	
	$playlistnumber = $_GET["playlists"];
	
	if($playlistnumber == 0) {
		echo '<script type="text/javascript">';
		echo 'alert("Please select a playlist")';
		echo '</script>';
	} else {
		$contentidsinplaylists = array();
		$playlistsearch = sprintf("SELECT CONTENT_ID
								   FROM MT_PLAYLIST_CONTENT WHERE
								   PLAYLIST_ID = '$playlistnumber'");
		$playlistsearchquery = mysql_query($playlistsearch) or die('Failed to search playlist');
		while($playlistsearchresults = mysql_fetch_array($playlistsearchquery)) {
			$contentidsinplaylists[] = $playlistsearchresults["CONTENT_ID"];
		}
		if(!empty($contentidsinplaylists)) { 	
			foreach ($contentidsinplaylists as $id) {
				$contentsearch = sprintf("SELECT CONTENT_ID,
												 CONTENT_TITLE,
												 CONTENT_LOCATION
												 FROM MT_CONTENT WHERE
												 CONTENT_ID = '$id' AND 
												 CONTENT_SHARING != 'P'");
				$valuecontentquery = mysql_query($contentsearch) or die('Failed to search playlist content');
				while($contentsearchresults = mysql_fetch_array($valuecontentquery)) {
					$contentids[] = $contentsearchresults["CONTENT_ID"];
					$contenttitles[] = $contentsearchresults["CONTENT_TITLE"];
				}
			}
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No media found in playlist")';
			echo '</script>';
		}
		if(!empty($contentids)) {
			$othersprofile = 1;
			echo "<table style='margin-left:300px;'>";
			for($i=0;$i<count($contentids);$i++) {
				echo "<tr><td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."&others_profile=".$othersprofile."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."&others_profile=".$othersprofile."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
			}
			echo "</table>";
		}
	}	
} elseif(isset($_GET['addfriend'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	
	$requestfriendsquery = "INSERT INTO MT_USER_CONTACTS 
							(USERNAME, USER_CONTACT_ID, IS_FRIEND, IS_BLOCKED)
							VALUES('$username', '$contentowner', 'G', 'N')";	
	$requestfriends = mysql_query($requestfriendsquery) or die("Failed to add friends");
	$gaverequestid = mysql_insert_id();
	
	$approvefriendsquery = "INSERT INTO MT_USER_CONTACTS
							(USERNAME, USER_CONTACT_ID, IS_FRIEND, IS_BLOCKED)
							VALUES('$contentowner', '$username', 'A', 'N')";
	$approvefriends = mysql_query($approvefriendsquery) or die("Failed to add friends");
	$approverequestid = mysql_insert_id();
	
	if(isset($gaverequestid) && isset($approverequestid)) {
		echo '<script type="text/javascript">';
		echo 'alert("A request has been given")';
		echo '</script>';
	} else {
		echo '<script type="text/javascript">';
		echo 'alert("A request could not be given")';
		echo '</script>';
	}
} elseif(isset($_GET['removerequest']) || isset($_GET['deletefriend'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	
	$deletefriendsquery = "DELETE FROM MT_USER_CONTACTS WHERE
					       USERNAME = '$username'";
	$deletefriends = mysql_query($deletefriendsquery) or die("Failed to delete friends");
	$gaverequestid = mysql_insert_id();
	
	$deletefriendsquery = "DELETE FROM MT_USER_CONTACTS WHERE
						   USERNAME = '$contentowner'";
	$deletefriends = mysql_query($deletefriendsquery) or die("Failed to delete friends");
	$removerequestid = mysql_insert_id();
	
	if(isset($gaverequestid) && isset($removerequestid)) {
		echo '<script type="text/javascript">';
		echo 'alert("The request has been removed")';
		echo '</script>';
	} else {
		echo '<script type="text/javascript">';
		echo 'alert("The request could not be removed")';
		echo '</script>';
	}
} elseif(isset($_GET['approverequest'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	
	$updatefriendsquery = "UPDATE MT_USER_CONTACTS SET IS_FRIEND='Y' WHERE
						   USERNAME = '$username' AND 
						   USER_CONTACT_ID = '$contentowner'";
	$updatefriends = mysql_query($updatefriendsquery) or die("Failed to approve request");
	$approveid = mysql_insert_id();	

	$updatefriendsquery = "UPDATE MT_USER_CONTACTS SET IS_FRIEND='Y' WHERE
						   USERNAME = '$contentowner' AND
						   USER_CONTACT_ID = '$username'";
	$updatefriends = mysql_query($updatefriendsquery) or die("Failed to approve request");
	$getapproveid = mysql_insert_id();
	if(isset($approveid) && isset($getapproveid)) {
		echo '<script type="text/javascript">';
		echo 'alert("The request has been be approved")';
		echo '</script>';
	} else {
		echo '<script type="text/javascript">';
		echo 'alert("The request could not be approved")';
		echo '</script>';
	}
} elseif(isset($_GET['block'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	
	$friendcheck = sprintf("SELECT *  FROM
							MT_USER_CONTACTS WHERE
							USERNAME = '$username' AND
							USER_CONTACT_ID = '$contentowner'");
	$check = mysql_query($friendcheck) or die('Failed to check friends');
	
	if((mysql_num_rows($check)) > 0) {
		$blockfriendsquery = "UPDATE MT_USER_CONTACTS SET IS_FRIEND='N', IS_BLOCKED='Y' WHERE
						  	  USERNAME = '$username' AND 
						  	  USER_CONTACT_ID = '$contentowner'";
		$blockfriends = mysql_query($blockfriendsquery) or die("Failed to block");
		$blockfromid = mysql_insert_id();	

		$blockfriendsquery = "UPDATE MT_USER_CONTACTS SET IS_FRIEND='N', IS_BLOCKED='Y' WHERE
						  	  USERNAME = '$contentowner' AND
						  	  USER_CONTACT_ID = '$username'";
		$blockfriends = mysql_query($blockfriendsquery) or die("Failed to block");
		$blocktoid = mysql_insert_id();
		if(isset($blockfromid) && isset($blocktoid)) {
			print '<meta http-equiv="refresh" content="0;url=./index.php?">';
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("Failed to block")';
			echo '</script>';
		}
	} else {
		$blockfriendsquery = "INSERT INTO MT_USER_CONTACTS
							  (USERNAME, USER_CONTACT_ID, IS_FRIEND, IS_BLOCKED)
							  VALUES('$username', '$contentowner', 'N', 'Y')";
		$blockfriends = mysql_query($blockfriendsquery) or die("Failed to block");
		$blockfromid = mysql_insert_id();

		$blockfriendsquery = "INSERT INTO MT_USER_CONTACTS
							  (USERNAME, USER_CONTACT_ID, IS_FRIEND, IS_BLOCKED)
							  VALUES('$contentowner', '$username', 'N', 'Y')";
		$blockfriends = mysql_query($blockfriendsquery) or die("Failed to block");
		$blocktoid = mysql_insert_id();
		
		if(isset($blockfromid) && isset($blocktoid)) {
			print '<meta http-equiv="refresh" content="0;url=./index.php?">';
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("Failed to block")';
			echo '</script>';
		}
	}
} elseif(isset($_GET['subscribe'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	$subscriptioncheck = sprintf("SELECT *  FROM
			MT_CHANNEL_SUBSCRIBERS WHERE
			CHANNEL_ID = '$contentowner' AND
			USERNAME = '$username'");
	$check = mysql_query($subscriptioncheck) or die('Failed to check subscription');
	
	if(mysql_num_rows($check) > 0) {
		echo '<script type="text/javascript">';
		echo 'alert("Already Subscribed")';
		echo '</script>';
	} else  {	
		$subscribequery = "INSERT INTO MT_CHANNEL_SUBSCRIBERS
						   (CHANNEL_ID, USERNAME) VALUES
						   ('$contentowner', '$username')";
		$subscribe = mysql_query($subscribequery) or die("Failed to suscribe");
		$subscribeid = mysql_insert_id();	
	
		if(isset($subscribeid)) {
			echo '<script type="text/javascript">';
			echo 'alert("Subscribed")';
			echo '</script>';
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("Cannot Subscribe")';
			echo '</script>';
		}
	}
} elseif(isset($_GET['unsubscribe'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	
	$unsubscribequery = "DELETE FROM MT_CHANNEL_SUBSCRIBERS
					   	 WHERE CHANNEL_ID = '$contentowner'
					     AND USERNAME = '$username'";
	$unsubscribe = mysql_query($unsubscribequery) or die("Failed to unsubscribe");
	$unsubscribeid = mysql_insert_id();	
	
	if(isset($unsubscribeid)) {
		echo '<script type="text/javascript">';
		echo 'alert("Unsubscribed")';
		echo '</script>';
	} else {
		echo '<script type="text/javascript">';
		echo 'alert("Cannot Unsubscribe")';
		echo '</script>';
	}
}		
?>

</body>
</html>


