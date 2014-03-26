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
<title>User Profile</title>
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


?>

<div>
<?php 
include("./searchChannelHeader.php");
?>
</div>

<?php 


if(isset($_GET['searchchannelbutton'])) {

	$mediatype = $_GET['mediatype'];
	$searchchannelinput = $_GET['searchchannelinput'];

	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} elseif($searchchannelinput == NULL) {
		$searchchannelerror = 1;
	} else {
		$valuesinsearch = explode(' ',$searchchannelinput);
		$contentidsintags = array();
		$contentids = array();
		$contenttitles = array();
		foreach ($valuesinsearch as $value) {
			$valuesearch = "SELECT CONTENT_ID
							FROM MT_CONTENT_TAGS WHERE
							TAGS LIKE '%$value%'";
			$valuesearchquery = mysql_query($valuesearch) or die('Failed to search tags');
			while($valuesearchresults = mysql_fetch_array($valuesearchquery)) {
				$contentidsintags[] = $valuesearchresults["CONTENT_ID"];
			}
		}
		if(!empty($contentidsintags)) {
			foreach ($contentidsintags as $id) {
				$contentsearch = sprintf("SELECT CONTENT_ID,
									      CONTENT_TITLE,
										  CONTENT_LOCATION
										  FROM MT_CONTENT WHERE
										  USERNAME = '$username' AND
										  CONTENT_TYPE = '$mediatype'
										  AND
									  	  CONTENT_ID = '$id'");
				$valuecontentquery = mysql_query($contentsearch) or die('Failed to search content');
				while($contentsearchresults = mysql_fetch_array($valuecontentquery)) {
					$contentids[] = $contentsearchresults["CONTENT_ID"];
					$contenttitles[] = $contentsearchresults["CONTENT_TITLE"];
				}
			}
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No media found")';
			echo '</script>';
		}
		if(!empty($contentids)) {
			echo "<table style='margin-left:300px;'>";
			for($i=0;$i<count($contentids);$i++) {
				echo "<tr><td><a href='./userUploadsView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userUploadsView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
			}
			echo "</table>";
		}
	}
} elseif(isset($_GET['uploads'])) {
	$mediatypeforchannel = $_GET['mediatypeforchannel'];
	
	if($mediatypeforchannel == 'N') {
		$mediatypeerror = 1;
	} else {
		$uploadsearch = sprintf("SELECT CONTENT_ID,
									  	CONTENT_TITLE,
									    CONTENT_LOCATION
									    FROM MT_CONTENT WHERE
									    USERNAME = '$username' AND
									    CONTENT_TYPE = '$mediatypeforchannel'");
		$search = mysql_query($uploadsearch) or die('Failed to search uploads');
		
		if((mysql_num_rows($search)) > 0) {
			echo "<br/>";
			echo "<br/>";
			echo "<table style='margin-left:300px;'>";
			while($searchresult = mysql_fetch_array($search)) {
				$contentid = $searchresult["CONTENT_ID"];
				$contenttitle = $searchresult["CONTENT_TITLE"];
				echo "<tr><td><a href='./userUploadsView.php?content_id=".$contentid."&media_type=".$mediatypeforchannel."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userUploadsView.php?content_id=".$contentid."&media_type=".$mediatypeforchannel."' class='stylish-link'><font size='4'>$contenttitle</font></a></td></tr>";
			}
			echo '</table>';
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Uploads found")';
			echo '</script>';
		}
	}	
} elseif(isset($_GET['favorites'])) {
	$mediatype = $_GET['mediatypeforchannel'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$contentidsinfavorites = array();
		$favoritessearch = sprintf("SELECT CONTENT_ID
									       FROM MT_USER_FAVOURITES WHERE
									       USERNAME = '$username' AND
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
			echo "<table style='margin-left:300px;'>";
			for($i=0;$i<count($contentids);$i++) {
				echo "<tr><td><a href='./userFavoritesView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userFavoritesView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
			}
			echo "</table>";
		}
	}	
} elseif(isset($_GET['subscriptions'])) {
	$mediatype = $_GET['mediatypeforchannel'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$contentidsinsubscriptions = array();
		$subscriptionsearch = sprintf("SELECT CONTENT_ID FROM MT_CONTENT
									   WHERE USERNAME IN
									   (SELECT CHANNEL_ID FROM MT_CHANNEL_SUBSCRIBERS
									   WHERE USERNAME = '$username') AND
									   CONTENT_TYPE = '$mediatype'");
		$subscriptionsearchquery = mysql_query($subscriptionsearch) or die('Failed to search subscriptions');
		while($subscriptionsearchresults = mysql_fetch_array($subscriptionsearchquery)) {
			$contentidsinsubscriptions[] = $subscriptionsearchresults["CONTENT_ID"];
		}
		if(!empty($contentidsinsubscriptions)) { 	
			foreach ($contentidsinsubscriptions as $id) {
				$contentsearch = sprintf("SELECT CONTENT_ID,
												 CONTENT_TITLE,
												 CONTENT_LOCATION
												 FROM MT_CONTENT WHERE
												 CONTENT_TYPE = '$mediatype' AND
												 CONTENT_ID = '$id' AND 
												 CONTENT_SHARING != 'P'");
				$valuecontentquery = mysql_query($contentsearch) or die('Failed to search subscriptions content');
				while($contentsearchresults = mysql_fetch_array($valuecontentquery)) {
					$contentids[] = $contentsearchresults["CONTENT_ID"];
					$contenttitles[] = $contentsearchresults["CONTENT_TITLE"];
				}
			}
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No subscriptions found")';
			echo '</script>';
		}
		if(!empty($contentids)) {
			echo "<table style='margin-left:300px;'>";
			for($i=0;$i<count($contentids);$i++) {
				echo "<tr><td><a href='./userSubscriptionsView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userSubscriptionsView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
			}
			echo "</table>";
		}
	}	
} elseif(isset($_GET['searchfriend'])) {  
  	echo '<meta http-equiv="refresh" content="0;url=./searchFriend.php?">';
} elseif(isset($_GET['blocked'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./block.php?">';
} elseif(isset($_GET['friendrequests'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./friendRequests.php?">';
} elseif(isset($_GET['creategroup'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./createGroup.php?">';
} elseif(isset($_GET['mygroups'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./myGroups.php?">';
} elseif(isset($_GET['joinedgroups'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./joinedGroups.php?">';
} elseif(isset($_GET['joingroups'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./joinGroups.php?">';
} elseif(isset($_GET['unjoingroups'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./unjoinGroups.php?">';
} elseif(isset($_GET['chat'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./messages.php?">';
} elseif(isset($_GET['updateprofile'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./profileUpdate.php?">';
} elseif(isset($_GET['updateemail'])) { 
	echo '<meta http-equiv="refresh" content="0;url=./updateEmail.php?">';
} elseif(isset($_GET['createplaylist'])) { 
	echo "<form name='addplaylist' method='post' onsubmit='return userChannelfields()'>";
	echo "<table style='margin-left:300px;'>";
	echo "<tr><td><label for='createplaylist'> <strong>Enter Playlist Name</strong> </label></td>";
	echo "<td><input type='text' size='40' maxlength='20' name='playlistname' id='playlistname' /></td>";
	echo "<td>";
	echo "<select name='playlisttype' id='playlisttype'>";
	echo "<option value='N'>Select Type</option>";
	echo "<option value='A'>Audio</option>";
	echo "<option value='V'>Video</option>";
	echo "<option value='I'>Image</option>";
	echo "</select>";
	echo "</td>";
	echo "<td><input type='submit' name='createplaylist' id='createplaylist' value='Create Playlist' style='background:none;border:0;color:#4C4646;font-size: 18px;'/></td></tr>";
	echo "</table>";
	echo "<form>";
} elseif(isset($_GET['selectplaylist'])) {
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
			echo "<table style='margin-left:300px;'>";
			for($i=0;$i<count($contentids);$i++) {
				echo "<tr><td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
			}
			echo "</table>";
		}
	}	
} else {
	$onloadsearch = sprintf("SELECT CONTENT_ID,
							  		CONTENT_TITLE,
									CONTENT_LOCATION
							 FROM MT_CONTENT WHERE
							 USERNAME = '$username' AND
							 CONTENT_TYPE = 'V'");
	$search = mysql_query($onloadsearch) or die('Failed to load');
	
	if((mysql_num_rows($search)) > 0) {
		echo "<br/>";
		echo "<br/>";
		echo "<table style='margin-left:300px;'>";
		$mediatype = 'V';
		while($searchresult = mysql_fetch_array($search)) {
			$contentid = $searchresult["CONTENT_ID"];
			$contenttitle = $searchresult["CONTENT_TITLE"];
			echo "<tr><td><a href='./userUploadsView.php?content_id=".$contentid."&media_type=".$mediatype."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
			echo "<td><a href='./userUploadsView.php?content_id=".$contentid."&media_type=".$mediatype."' class='stylish-link'><font size='4'>$contenttitle</font></a></td></tr>";
		}
		echo '</table>';
	} 
}

if(isset($_POST['createplaylist'])) {
	$playlistname = $_POST["playlistname"];
	$playlisttype = $_POST["playlisttype"];
	
	if($playlistname == null) {
		$playlisterror = TRUE;
	} elseif($playlisttype == 'N') {
		$playlisterror = TRUE;
		$playlisttypeerror = TRUE;
	} else {
		$playlisterror = FALSE;
		$playlisttypeerror = FALSE;
	}

	$loadplaylistsquery = sprintf("SELECT PLAYLIST_NAME
								   FROM MT_USER_PLAYLIST WHERE
								   USERNAME = '$username'");
	$loadplaylists = mysql_query($loadplaylistsquery) or die('Failed to load playlists');
	
	if((mysql_num_rows($loadplaylists)) > 0) {
		$playlistidentity = array();
		while($playlistresult = mysql_fetch_array($loadplaylists)) {
			$playlistidentity[] = $playlistresult["PLAYLIST_NAME"];
		}
		
		foreach($playlistidentity as $name) {
			if($playlistname == $name) {
				$playlisterror = TRUE;
				break 1;
			}
		}
	}
	if(!$playlisterror && !$playlisttypeerror) {
		$addplaylistquery = "INSERT INTO MT_USER_PLAYLIST
							 (USERNAME, PLAYLIST_NAME, PLAYLIST_TYPE)
							 VALUES('$username', '$playlistname', '$playlisttype')";
		$addplaylist = mysql_query($addplaylistquery) or die("Failed to add playlist");
		$addplaylistid = mysql_insert_id();
		if(isset($addplaylistid)) {
			echo '<script type="text/javascript">';
			echo 'alert("Playlist created")';
			echo '</script>';
		}
	} elseif($playlisttypeerror) {
		echo '<script type="text/javascript">';
		echo 'alert("Please select a type")';
		echo '</script>';
	} else {
		echo '<script type="text/javascript">';
		echo 'alert("Playlist could not be created")';
		echo '</script>';
	}
}

if(isset($mediatypeerror)) {
	echo '<script type="text/javascript">';
	echo 'alert("Please select a type to search in your channel")';
	echo '</script>';
}
?>


</body>

</html>