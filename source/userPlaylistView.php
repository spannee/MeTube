<?php 
session_start();
if(isset($_SESSION['username']) && $_SESSION['username'] != NULL) {
	$username = $_SESSION['username'];
}
if(isset($_SESSION['contentownercontent'])) {
	$others = $_SESSION['contentownercontent'];
} elseif(isset($_SESSION['contentownerplaylist'])) {
	$others = $_SESSION['contentownerplaylist'];
} elseif(isset($_SESSION['contentownerfavorites'])) {
	$others = $_SESSION['contentownerfavorites'];
} elseif(isset($_SESSION['contentowneruploads'])) {
	$others = $_SESSION['contentowneruploads'];
} elseif(isset($_SESSION['contentownersearch'])) {
	$contentowner = $_SESSION['contentownersearch'];
} elseif(isset($_SESSION['contentfriendrequestsearch'])) {
	$contentowner = $_SESSION['contentfriendrequestsearch'];
} elseif(isset($_SESSION['contentownersubscribe'])) {
	$contentowner = $_SESSION['contentownersubscribe'];
}

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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Content</title>
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
<style type="text/css">
  .positionmedia {
    margin-left:260px;
	margin-top:-130px;
	position:absolute;
    
 }
 .positiontable {
 	margin-left:10px; 
    margin-right:100px;
 }
 
</style>
</head>

<body>

<?php
if(isset($_GET['playlist_id']) && isset($_GET['content_id'])) {
	$playlistid = $_GET['playlist_id'];
	$contentid = intval($_GET['content_id']);
	
	if(isset($_GET['others_profile'])) {
		$othersprofile = intval($_GET['others_profile']);
	}

    $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_CONTENT WHERE CONTENT_ID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $contentid);
    mysqli_stmt_execute($stmt);
    $search = mysqli_stmt_get_result($stmt) or die('Failed to fetch video');
    mysqli_stmt_close($stmt);

    if((mysqli_num_rows($search)) == 1) {
        $searchresult = mysqli_fetch_array($search);
		$contentowner = $searchresult["USERNAME"];
		$contenttitle = $searchresult["CONTENT_TITLE"];
		$contenttype = $searchresult["CONTENT_TYPE"];
		$contentlocation = $searchresult["CONTENT_LOCATION"];
		$contentextension = $searchresult["CONTENT_FORMAT"];
		$contentdescription = $searchresult["CONTENT_DESCRIPTION"];
		$contentsharing = $searchresult["CONTENT_SHARING"];
		$contentrating = $searchresult["CONTENT_RATING"];
		$contentratingenabled = $searchresult["RATING_ENABLED"];
		$contentlikes = $searchresult["LIKES"];
		$contentcount = $searchresult["VIEW_COUNT"];
		$contentuploadtime = $searchresult["UPLOAD_DATE"];
	}

    $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_PLAYLIST_CONTENT WHERE PLAYLIST_ID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $playlistid);
    mysqli_stmt_execute($stmt);
    $loadplaylist = mysqli_stmt_get_result($stmt) or die('Failed to load playlist');
    mysqli_stmt_close($stmt);

	$contentidsinplaylists = array();
	while($loadplaylistresults = mysqli_fetch_array($loadplaylist)) {
		$contentidsinplaylists[] = $loadplaylistresults["CONTENT_ID"];
		$playlistnumber = $loadplaylistresults["PLAYLIST_ID"];
	}
	
	if(!empty($contentidsinplaylists)) {
		foreach ($contentidsinplaylists as $id) {
            $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
											       FROM MT_CONTENT WHERE CONTENT_ID = ? AND
					            	    	       CONTENT_SHARING != 'P'");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $contentsearch = mysqli_stmt_get_result($stmt) or die('Failed to search playlist content');
            mysqli_stmt_close($stmt);

			while($contentsearchresults = mysqli_fetch_array($contentsearch)) {
				$contentids[] = $contentsearchresults["CONTENT_ID"];
				$contenttitles[] = $contentsearchresults["CONTENT_TITLE"];
			}
		}
	}
	
	if(isset($othersprofile)) {
		echo "<div>";
		include("./othersChannelHeader.php");
		echo "</div>";
		echo "<br/>";
		echo "<br/>";
	} else {
		echo "<div>";
		include("./listsChannelHeader.php");
		echo "</div>";
		echo "<br/>";
		echo "<br/>";
	}
	
	if(isset($contenttype) && isset($contentids)) {
		if($contentsharing == 'P') {
			if(isset($username)) {
				if($username == $contentowner) {
					$canview = TRUE;
					$setallfeatures = TRUE;
				} else {
					$canview = FALSE;
					$setallfeatures = FALSE;
				}
			} else {
				$canview = FALSE;
				$setallfeatures = FALSE;
			}
		} elseif($contentsharing == 'F') {
			if(isset($username)) {
				if($username == $contentowner) {
					$canview = TRUE;
					$setallfeatures = TRUE;
				} elseif($username != $contentowner) {
                    $stmt = mysqli_prepare($dbconnection, "SELECT *  FROM MT_USER_CONTACTS WHERE USERNAME = ? AND
											               USER_CONTACT_ID = ? AND IS_FRIEND = 'Y'
											               AND IS_BLOCKED = 'N'");
                    mysqli_stmt_bind_param($stmt, 'ss', $username, $contentowner);
                    mysqli_stmt_execute($stmt);
                    $friendcheck = mysqli_stmt_get_result($stmt) or die('Failed to check friends');
                    mysqli_stmt_close($stmt);

                    if((mysqli_num_rows($friendcheck)) == 1) {
						$canview = TRUE;
						$setallfeatures = TRUE;
					} else {
						$canview = FALSE;
						$setallfeatures = FALSE;
						}
					}
				} else {
					$canview = FALSE;
					$setallfeatures = FALSE;
			}
		} elseif($contentsharing == 'S') {
			if(isset($username)) {
				if($username != $contentowner) {
                    $stmt = mysqli_prepare($dbconnection, "SELECT *  FROM MT_USER_CONTACTS WHERE USERNAME = ? AND
								                           USER_CONTACT_ID = ? AND IS_BLOCKED = 'Y'");
                    mysqli_stmt_bind_param($stmt, 'ss', $username, $contentowner);
                    mysqli_stmt_execute($stmt);
                    $blockcheck = mysqli_stmt_get_result($stmt) or die('Failed to check blocked friends');
                    mysqli_stmt_close($stmt);

                    if((mysqli_num_rows($blockcheck)) == 0) {
						$canview = TRUE;
						$setallfeatures = TRUE;
					}
				} else {
					$canview = TRUE;
					$setallfeatures = TRUE;
				}
			} else {
				$canview = TRUE;
				$setallfeatures = FALSE;
			}
		}
	
	
	if($canview) {
        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_CONTENT SET VIEW_COUNT=VIEW_COUNT+1 WHERE
							                   CONTENT_ID = ?;");
        mysqli_stmt_bind_param($stmt, 'i', $contentid);
        mysqli_stmt_execute($stmt);
        $updatecount = mysqli_stmt_get_result($stmt) or die("Failed to view count");
        $countid = mysqli_insert_id($dbconnection);
        mysqli_stmt_close($stmt);
	
		if(!isset($countid)) {
			die("Failed to view count");
		}
		?>
		<form name="videoform" method="post">
		<div class="positionmedia"> 
		<?php
		if($contenttype == 'I') {
			echo "<img src='".$contentlocation."' width='600' height='420'/>";
		} else {
			if($contentextension == 'mp3' || $contentextension == 'wmv' || $contentextension == 'avi' || $contentextension == 'mp3') {
				?>
				<OBJECT ID="MediaPlayer" WIDTH="600" HEIGHT="420" CLASSID="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95" STANDBY="Loading Windows Media Player components..." TYPE="application/x-oleobject" CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112">
				<PARAM name="autoStart" value="True">
				<PARAM name="filename" value="<?php echo $contentlocation ?>">
				<EMBED TYPE="application/x-mplayer2" WIDTH="600" HEIGHT="420" SRC="<?php echo $contentlocation ?>" NAME="MediaPlayer" WIDTH=320 HEIGHT=240>
				</EMBED>	
				</OBJECT>			
				<?php
			} elseif($contentextension == 'mp4' || $contentextension == 'mpg' || $contentextension == 'mov') {
				?>
				<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="600" height="420" codebase="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0" align="middle" >
				<param name="src" value="<?php echo $contentlocation ?>" />
				<param name="autoplay" value="true" />
				<embed src="<?php echo $contentlocation ?>" width="600" height="420" pluginspage=http://www.apple.com/quicktime/download/ align="middle" autoplay="true" bgcolor="black" >
				</embed>
				</object>
				<?php 
			} elseif($contentextension == 'swf') {
				?>
				<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" WIDTH="600" HEIGHT="420" id="sample1">
				<PARAM NAME=movie VALUE="<?php echo $contentlocation ?>">
				<PARAM NAME=quality VALUE=high>
				<PARAM NAME=bgcolor VALUE=#FFFFFF>
				<EMBED src="<?php echo $contentlocation ?>" quality=high bgcolor=#FFFFFF WIDTH="600" HEIGHT="420" NAME="sample1" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>
				</OBJECT>
				<?php
 			} elseif($contentextension = 'flv') {
				?>
				
				<?php
			}	
			}
			?>
			<table>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> 
					<td> <font size="5"> <?php echo $contenttitle; ?> </font>  
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="download.php?file=<?php echo $contentlocation ;?>" class='stylish-link'>Download</a>
					&nbsp;
					<label for="ratingsofar"> <strong>Rating:</strong> </label>
					<?php echo $contentrating; ?>
					&nbsp;
					<label for="likes"> <strong>Likes:</strong> </label>
					<?php echo $contentlikes; ?></td>
				</tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<?php
				if($setallfeatures) {
					$_SESSION['contentownerplaylist'] = $contentowner;
					echo "<tr>"; 
					if($username != $contentowner) {
						echo "<td><label for='uploadedby'> <strong>Uploaded By:</strong> </label>";
						echo "<a href='./othersProfile.php?content_owner=".$contentowner."' class='stylish-link'><font size='4'>$contentowner</font></a>";
					} else {
						echo "<td><label for='uploadedby'> <strong>Uploaded By:</strong> </label>";
						echo "<a href='./userProfile.php' class='stylish-link'><font size='4'>$contentowner</font></a>";
					}
					echo "";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<label for='uploadedon'> <strong>Uploaded On:</strong> </label>";
					echo "<font size='4'>$contentuploadtime</font></a>";
					echo "</td>";
					echo "</tr>";
				} else {
					echo "<tr>";
					echo "<td><label for='uploadedby'> <strong>Uploaded By:</strong> </label>";
					echo "<font size='4'>$contentowner</font>";
					echo "";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "<label for='uploadedon'> <strong>Uploaded On:</strong> </label>";
					echo "<font size='4'>$contentuploadtime</font></a>";
					echo "</td>";
					echo "</tr>";
				}
				?>					
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> 
					<td> 
					<input type="submit" name="like" value="Like"/>
					<?php
					if($contentratingenabled && $setallfeatures) {
						?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="ratethisvideo"> <strong>Rate it</strong> </label>
						<input type="submit" name="ratingone" value="1" style="width: 20px;"/>
						<input type="submit" name="ratingtwo" value="2" style="width: 20px;"/>
						<input type="submit" name="ratingthree" value="3" style="width: 20px;"/>
						<input type="submit" name="ratingfour" value="4" style="width: 20px;"/>
						<input type="submit" name="ratingfive" value="5" style="width: 20px;"/>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<?php 
						}
						?>
						
						</td>
						</tr>
						<tr> </tr>
						<tr> </tr>
						<tr> </tr>
						<tr> </tr>
						<tr> </tr>
						<tr> </tr>
						<tr> </tr>
						<tr> </tr>
						<tr> </tr>
						<tr> </tr>
						<tr> </tr>
						<tr>
							<td> <label for="description"> <strong>Description:</strong> </label> </td>
						</tr>
						<tr>
							<td>
								<?php echo $contentdescription ?>
							</td>
						</tr>
					<?php 
					if($setallfeatures) {
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
					<tr>
					<td>
					<div>
					<?php 
					include("./comments.php");
					?>
					</div>
					</td>
					</tr>
					<?php } ?>
					</table>
					</div>
					<table style="margin-left:920px;">
					<tr> <td> <font size="5"> <strong>Playlist </strong> </font> </td> </tr>
					<tr> </tr>
					<tr> </tr>
					<tr> </tr>
					<tr> </tr>
					<tr> </tr>
					<tr> </tr>
					<?php 
					if(isset($othersprofile)) {		
						for($i=0;$i<count($contentids);$i++) {
							echo "<tr><td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."&others_profile=".$othersprofile."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
							echo "<td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."&others_profile=".$othersprofile."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
						}
					} else {
						for($i=0;$i<count($contentids);$i++) {
							echo "<tr><td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
							echo "<td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
						}
					}
					
					?>
					</table>
					
					</form>
					<?php 
				
			} 
			
			}
			if(isset($_POST['like'])) {
                $stmt = mysqli_prepare($dbconnection, "UPDATE MT_CONTENT SET LIKES=LIKES+1 WHERE CONTENT_ID = ?;");
                mysqli_stmt_bind_param($stmt, 'i', $contentid);
                mysqli_stmt_execute($stmt);
                $updatelike = mysqli_stmt_get_result($stmt) or die('Failed to like');
                $likeid = mysqli_insert_id($dbconnection);
                mysqli_stmt_close($stmt);
				if(!isset($likeid)) {
					die("Failed to like");
				}
								
			}
			
			if(isset($_POST['ratingone']) || isset($_POST['ratingtwo']) || isset($_POST['ratingthree']) || isset($_POST['ratingfour']) || isset($_POST['ratingfive'])) {
				$rate = TRUE;
				if(isset($_POST['ratingone'])) {
					$ratingvalue = 1;
				} elseif(isset($_POST['ratingtwo'])) {
					$ratingvalue = 2;
				} elseif(isset($_POST['ratingthree'])) {
					$ratingvalue = 3;
				} elseif(isset($_POST['ratingfour'])) {
					$ratingvalue = 4;
				} elseif(isset($_POST['ratingfive'])) {
					$ratingvalue = 5;
				}
			} else {
				$rate = FALSE;
			}
			
			if($rate) {
                $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_CONTENT_RATING WHERE
												       CONTENT_ID = ? AND USERNAME = ?");
                mysqli_stmt_bind_param($stmt, 'is', $contentid, $username);
                mysqli_stmt_execute($stmt);
                $checkuserrated = mysqli_stmt_get_result($stmt) or die('Failed to check whether user rated');
                mysqli_stmt_close($stmt);

                if((mysqli_num_rows($checkuserrated)) == 1) {
                    $stmt = mysqli_prepare($dbconnection, "UPDATE MT_CONTENT_RATING SET RATING=? WHERE
										                   CONTENT_ID = ? AND USERNAME = ?");
                    mysqli_stmt_bind_param($stmt, 'iis', $ratingvalue, $contentid, $username);
                    mysqli_stmt_execute($stmt);
                    $updaterating = mysqli_stmt_get_result($stmt) or die("Failed to update rating");
                    mysqli_stmt_close($stmt);
				}
                $stmt = mysqli_prepare($dbconnection, "SELECT USERNAME FROM MT_CONTENT_RATING WHERE
										               CONTENT_ID = ? AND USERNAME = ?");
                mysqli_stmt_bind_param($stmt, 'is', $contentid, $username);
                mysqli_stmt_execute($stmt);
                $checkuser = mysqli_stmt_get_result($stmt) or die('Failed to check user');
                mysqli_stmt_close($stmt);
                $userrated = mysqli_fetch_row($checkuser);

				if($userrated[0] != NULL) {
                    $stmt = mysqli_prepare($dbconnection, "SELECT AVG(RATING) FROM MT_CONTENT_RATING WHERE
												           CONTENT_ID = ?");
                    mysqli_stmt_bind_param($stmt, 'i', $contentid);
                    mysqli_stmt_execute($stmt);
                    $checkrating = mysqli_stmt_get_result($stmt) or die('Failed to check whether user rated');
                    mysqli_stmt_close($stmt);

                    $ratingdetails = mysqli_fetch_row($checkrating);
					$ratingaverage = $ratingdetails[0];
				} else {
					$ratingaverage = $ratingvalue;
                    $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_CONTENT_RATING (CONTENT_ID, USERNAME, RATING)
											               VALUES(?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, 'isi', $contentid, $username, $ratingvalue);
                    mysqli_stmt_execute($stmt);
                    $insertfinalrating = mysqli_stmt_get_result($stmt) or die("Failed to insert rating");
                    mysqli_stmt_close($stmt);
				}

                $stmt = mysqli_prepare($dbconnection, "UPDATE MT_CONTENT SET CONTENT_RATING=? WHERE
										               CONTENT_ID = ?;");
                mysqli_stmt_bind_param($stmt, 'ii', $ratingaverage, $contentid);
                mysqli_stmt_execute($stmt);
                $updatefinalrating = mysqli_stmt_get_result($stmt) or die("Failed to update rating");
                $ratingid = mysqli_insert_id($dbconnection);
                mysqli_stmt_close($stmt);
			
				if(!isset($ratingid)) {
					die("Failed to rate");
				}
			
			}
			
			
}

if(isset($_GET['searchchannelbutton'])) {
	echo "<div>";
	include("./listsChannelHeader.php");
	echo "</div>";
	echo "<br/>";
	echo "<br/>";
	
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
            $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID FROM MT_CONTENT_TAGS WHERE TAGS LIKE ?");
            $value = '%' . $value . '%';
            mysqli_stmt_bind_param($stmt, 's', $value);
            mysqli_stmt_execute($stmt);
            $valuesearch = mysqli_stmt_get_result($stmt) or die('Failed to search tags');
            mysqli_stmt_close($stmt);

            while($valuesearchresults = mysqli_fetch_array($valuesearch)) {
				$contentidsintags[] = $valuesearchresults["CONTENT_ID"];
			}
		}
		if(!empty($contentidsintags)) {
			foreach ($contentidsintags as $id) {
                $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
										               FROM MT_CONTENT WHERE USERNAME = ? AND
										               CONTENT_TYPE = ? AND CONTENT_ID = ?");
                mysqli_stmt_bind_param($stmt, 'ssi', $username, $mediatype, $id);
                mysqli_stmt_execute($stmt);
                $contentsearch = mysqli_stmt_get_result($stmt) or die('Failed to search content');
                mysqli_stmt_close($stmt);

                while($contentsearchresults = mysqli_fetch_array($contentsearch)) {
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
	echo "<div>";
	include("./listsChannelHeader.php");
	echo "</div>";	
	echo "<br/>";
	echo "<br/>";
	
	$mediatypeforchannel = $_GET['mediatypeforchannel'];

	if($mediatypeforchannel == 'N') {
		$mediatypeerror = 1;
	} else {
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
								               FROM MT_CONTENT WHERE USERNAME = ? AND CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $username, $mediatypeforchannel);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search uploads');
        mysqli_stmt_close($stmt);

		if((mysqli_num_rows($search)) > 0) {
			echo "<br/>";
			echo "<br/>";
			echo "<table style='margin-left:300px;'>";
			while($searchresult = mysqli_fetch_array($search)) {
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
	echo "<div>";
	include("./listsChannelHeader.php");
	echo "</div>";
	echo "<br/>";
	echo "<br/>";
	
	$mediatype = $_GET['mediatypeforchannel'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$contentidsinfavorites = array();
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID FROM MT_USER_FAVOURITES WHERE
									           USERNAME = ? AND FAVORITES_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $username, $mediatype);
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
			echo "<table style='margin-left:300px;'>";
			for($i=0;$i<count($contentids);$i++) {
				echo "<tr><td><a href='./userFavoritesView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userFavoritesView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
			}
			echo "</table>";
		}
	}	
} elseif(isset($_GET['subscriptions'])) {
	echo "<div>";
	include("./listsChannelHeader.php");
	echo "</div>";
	echo "<br/>";
	echo "<br/>";
	
	$mediatype = $_GET['mediatypeforchannel'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$contentidsinsubscriptions = array();
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID FROM MT_CONTENT WHERE USERNAME IN
									           (SELECT CHANNEL_ID FROM MT_CHANNEL_SUBSCRIBERS
									           WHERE USERNAME = ?) AND CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $username, $mediatype);
        mysqli_stmt_execute($stmt);
        $subscriptionsearch = mysqli_stmt_get_result($stmt) or die('Failed to search subscriptions');
        mysqli_stmt_close($stmt);

        while($subscriptionsearchresults = mysqli_fetch_array($subscriptionsearch)) {
			$contentidsinsubscriptions[] = $subscriptionsearchresults["CONTENT_ID"];
		}
		if(!empty($contentidsinsubscriptions)) { 	
			foreach ($contentidsinsubscriptions as $id) {
                $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
												       FROM MT_CONTENT WHERE CONTENT_TYPE = ? AND
												       CONTENT_ID = ? AND CONTENT_SHARING != 'P'");
                mysqli_stmt_bind_param($stmt, 'si', $mediatype, $id);
                mysqli_stmt_execute($stmt);
                $contentsearch = mysqli_stmt_get_result($stmt) or die('Failed to search subscriptions content');
                mysqli_stmt_close($stmt);

                while($contentsearchresults = mysqli_fetch_array($contentsearch)) {
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
} elseif(isset($_GET['createplaylist'])) { 

	echo "<div>";
	include("./listsChannelHeader.php");
	echo "</div>";
	echo "<br/>";	
	echo "<br/>";
	
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
	echo "<div>";
	include("./listsChannelHeader.php");
	echo "</div>";
	echo "<br/>";
	echo "<br/>";
	
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
			echo "<table style='margin-left:300px;'>";
			for($i=0;$i<count($contentids);$i++) {
				echo "<tr><td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
				echo "<td><a href='./userPlaylistView.php?content_id=".$contentids[$i]."&playlist_id=".$playlistnumber."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
			}
			echo "</table>";
		}
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

    $stmt = mysqli_prepare($dbconnection, "SELECT PLAYLIST_NAME FROM MT_USER_PLAYLIST WHERE USERNAME = ?");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $loadplaylists = mysqli_stmt_get_result($stmt) or die('Failed to load playlists');
    mysqli_stmt_close($stmt);

	if((mysqli_num_rows($loadplaylists)) > 0) {
		$playlistidentity = array();
		while($playlistresult = mysqli_fetch_array($loadplaylists)) {
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
        $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_USER_PLAYLIST (USERNAME, PLAYLIST_NAME, PLAYLIST_TYPE)
		                                       VALUES(?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $username, $playlistname, $playlisttype);
        mysqli_stmt_execute($stmt);
        $addplaylist = mysqli_stmt_get_result($stmt) or die("Failed to add playlist");
        $addplaylistid = mysqli_insert_id($dbconnection);
        mysqli_stmt_close($stmt);

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

if(isset($_GET['uploadsofothers'])) {
	echo "<div>";
	include("./othersChannelHeader.php");
	echo "</div>";
	echo "<br/>";
	echo "<br/>";
	
	$mediatypeforchannel = $_GET['mediatypeforchannel'];

	if($mediatypeforchannel == 'N') {
		$mediatypeerror = 1;
	} else {
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
                                               FROM MT_CONTENT WHERE USERNAME = ? AND
                                               CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $others, $mediatypeforchannel);
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
	echo "<br/>";
	echo "<br/>";
	
	$mediatype = $_GET['mediatypeforchannel'];

	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$contentidsinfavorites = array();
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID FROM MT_USER_FAVOURITES WHERE
				                               USERNAME = ? AND FAVORITES_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $others, $mediatype);
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
	echo "<br/>";
	echo "<br/>";
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
						    			               FROM MT_CONTENT WHERE CONTENT_ID = ? AND
                                                       CONTENT_SHARING != 'P'");
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
}


if(isset($mediatypeerror)) {
	echo '<script type="text/javascript">';
	echo 'alert("Please select a type to search in your channel")';
	echo '</script>';
}

?>

</body>
</html>