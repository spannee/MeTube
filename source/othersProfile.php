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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION FROM MT_CONTENT WHERE
								               USERNAME = ? AND CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $contentowner, $mediatypeforchannel);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search uploads');
        mysqli_stmt_close($stmt);

		if((mysqli_num_rows($search)) > 0) {
			echo "<br/>";
			echo "<br/>";
			echo "<table style='margin-left:300px;'>";
			$othersprofile = 1;
			while($searchresult = mysqli_fetch_array($search)) {
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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID FROM MT_USER_FAVOURITES WHERE
									           USERNAME = ? AND FAVORITES_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $contentowner, $mediatype);
        mysqli_stmt_execute($stmt);
        $favoritessearch = mysqli_stmt_get_result($stmt) or die('Failed to search favorites');
        mysqli_stmt_close($stmt);

		while($favoritesearchresults = mysqli_fetch_array($favoritessearch)) {
			$contentidsinfavorites[] = $favoritesearchresults["CONTENT_ID"];
		}
		if(!empty($contentidsinfavorites)) { 	
			foreach ($contentidsinfavorites as $id) {
                $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
												       FROM MT_CONTENT WHERE CONTENT_TYPE = ? AND
												       CONTENT_ID = ? AND CONTENT_SHARING != 'P'");
                mysqli_stmt_bind_param($stmt, 'si', $mediatype, $id);
                mysqli_stmt_execute($stmt);
                $contentsearch = mysqli_stmt_get_result($stmt) or die('Failed to search favorite content');
                mysqli_stmt_close($stmt);

				while($contentsearchresults = mysqli_fetch_array($contentsearch)) {
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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID FROM MT_PLAYLIST_CONTENT WHERE PLAYLIST_ID = ?");
        mysqli_stmt_bind_param($stmt, 'i', $playlistnumber);
        mysqli_stmt_execute($stmt);
        $playlistsearch = mysqli_stmt_get_result($stmt) or die('Failed to search playlist');
        mysqli_stmt_close($stmt);

		while($playlistsearchresults = mysqli_fetch_array($playlistsearch)) {
			$contentidsinplaylists[] = $playlistsearchresults["CONTENT_ID"];
		}
		if(!empty($contentidsinplaylists)) { 	
			foreach ($contentidsinplaylists as $id) {
                $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
												       FROM MT_CONTENT WHERE CONTENT_ID = ?
												       AND CONTENT_SHARING != 'P'");
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                $contentsearch = mysqli_stmt_get_result($stmt) or die('Failed to search playlist content');
                mysqli_stmt_close($stmt);

				while($contentsearchresults = mysqli_fetch_array($contentsearch)) {
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

    $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_USER_CONTACTS
							               (USERNAME, USER_CONTACT_ID, IS_FRIEND, IS_BLOCKED)
							               VALUES(?, ?, 'G', 'N')");
    mysqli_stmt_bind_param($stmt, 'ss', $username, $contentowner);
    mysqli_stmt_execute($stmt);
    $requestfriends = mysqli_stmt_get_result($stmt) or die("Failed to add friends");
    $gaverequestid = mysqli_insert_id($dbconnection);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_USER_CONTACTS
							               (USERNAME, USER_CONTACT_ID, IS_FRIEND, IS_BLOCKED)
							               VALUES(?, ?, 'A', 'N')");
    mysqli_stmt_bind_param($stmt, 'ss', $contentowner, $username);
    mysqli_stmt_execute($stmt);
    $approvefriends = mysqli_stmt_get_result($stmt) or die("Failed to add friends");
    $approverequestid = mysqli_insert_id($dbconnection);
    mysqli_stmt_close($stmt);
	
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

    $stmt = mysqli_prepare($dbconnection, "DELETE FROM MT_USER_CONTACTS WHERE USERNAME = ?");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $deletefriends = mysqli_stmt_get_result($stmt) or die("Failed to delete friends");
    $gaverequestid = mysqli_insert_id($dbconnection);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($dbconnection, "DELETE FROM MT_USER_CONTACTS WHERE USERNAME = ?");
    mysqli_stmt_bind_param($stmt, 's', $contentowner);
    mysqli_stmt_execute($stmt);
    $deletefriends = mysqli_stmt_get_result($stmt) or die("Failed to delete friends");
    $removerequestid = mysqli_insert_id($dbconnection);
    mysqli_stmt_close($stmt);
	
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

    $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER_CONTACTS SET IS_FRIEND='Y' WHERE
						                   USERNAME = ? AND USER_CONTACT_ID = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $username, $contentowner);
    mysqli_stmt_execute($stmt);
    $updatefriends = mysqli_stmt_get_result($stmt) or die("Failed to approve request");
    $approveid = mysqli_insert_id($dbconnection);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER_CONTACTS SET IS_FRIEND='Y' WHERE
						                   USERNAME = ? AND USER_CONTACT_ID = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $contentowner, $username);
    mysqli_stmt_execute($stmt);
    $updatefriends = mysqli_stmt_get_result($stmt) or die("Failed to approve request");
    $getapproveid = mysqli_insert_id($dbconnection);
    mysqli_stmt_close($stmt);

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

    $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_USER_CONTACTS WHERE
							               USERNAME = ? AND USER_CONTACT_ID = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $username, $contentowner);
    mysqli_stmt_execute($stmt);
    $check = mysqli_stmt_get_result($stmt) or die('Failed to check friends');
    mysqli_stmt_close($stmt);
	
	if((mysqli_num_rows($check)) > 0) {
        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER_CONTACTS SET IS_FRIEND='N', IS_BLOCKED='Y' WHERE
						  	                   USERNAME = ? AND USER_CONTACT_ID = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $username, $contentowner);
        mysqli_stmt_execute($stmt);
        $blockfriends = mysqli_stmt_get_result($stmt) or die("Failed to block");
        $blockfromid = mysqli_insert_id($dbconnection);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER_CONTACTS SET IS_FRIEND='N', IS_BLOCKED='Y' WHERE
						  	                   USERNAME = ? AND USER_CONTACT_ID = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $contentowner, $username);
        mysqli_stmt_execute($stmt);
        $blockfriends = mysqli_stmt_get_result($stmt) or die("Failed to block");
        $blocktoid = mysqli_insert_id($dbconnection);
        mysqli_stmt_close($stmt);

		if(isset($blockfromid) && isset($blocktoid)) {
			print '<meta http-equiv="refresh" content="0;url=./index.php?">';
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("Failed to block")';
			echo '</script>';
		}
	} else {
        $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_USER_CONTACTS
							                   (USERNAME, USER_CONTACT_ID, IS_FRIEND, IS_BLOCKED)
							                   VALUES(?, ?, 'N', 'Y')");
        mysqli_stmt_bind_param($stmt, 'ss', $username, $contentowner);
        mysqli_stmt_execute($stmt);
        $blockfriends = mysqli_stmt_get_result($stmt) or die("Failed to block");
        $blockfromid = mysqli_insert_id($dbconnection);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_USER_CONTACTS
							                   (USERNAME, USER_CONTACT_ID, IS_FRIEND, IS_BLOCKED)
							                   VALUES(?, ?, 'N', 'Y')");
        mysqli_stmt_bind_param($stmt, 'ss', $contentowner, $username);
        mysqli_stmt_execute($stmt);
        $blockfriends = mysqli_stmt_get_result($stmt) or die("Failed to block");
        $blocktoid = mysqli_insert_id($dbconnection);
        mysqli_stmt_close($stmt);
		
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

    $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_CHANNEL_SUBSCRIBERS WHERE
			                               CHANNEL_ID = ? AND USERNAME = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $contentowner, $username);
    mysqli_stmt_execute($stmt);
    $check = mysqli_stmt_get_result($stmt) or die('Failed to check subscription');
    mysqli_stmt_close($stmt);
	
	if(mysqli_num_rows($check) > 0) {
		echo '<script type="text/javascript">';
		echo 'alert("Already Subscribed")';
		echo '</script>';
	} else  {
        $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_CHANNEL_SUBSCRIBERS (CHANNEL_ID, USERNAME) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $contentowner, $username);
        mysqli_stmt_execute($stmt);
        $subscribe = mysqli_stmt_get_result($stmt) or die('Failed to subscribe');
        $subscribeid = mysqli_insert_id($dbconnection);
        mysqli_stmt_close($stmt);

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

    $stmt = mysqli_prepare($dbconnection, "DELETE FROM MT_CHANNEL_SUBSCRIBERS WHERE CHANNEL_ID = ?
					                       AND USERNAME = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $contentowner, $username);
    mysqli_stmt_execute($stmt);
    $unsubscribe = mysqli_stmt_get_result($stmt) or die('Failed to unsubscribe');
    $unsubscribeid = mysqli_insert_id($dbconnection);
    mysqli_stmt_close($stmt);
	
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


