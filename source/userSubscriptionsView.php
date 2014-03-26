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
<title>Subscription</title>
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
if(isset($_GET['content_id']) && isset($_GET['media_type'])) {
	$contentid = intval($_GET['content_id']);
	
	if(isset($_GET['others_profile'])) {
		$othersprofile = intval($_GET['others_profile']);
	}
	
	$contentsearch = sprintf("SELECT * FROM
							  MT_CONTENT WHERE
							  CONTENT_ID = '$contentid'");
	$search = mysql_query($contentsearch) or die('Failed to fetch video');
	
	if((mysql_num_rows($search)) == 1) {
		$searchresult = mysql_fetch_array($search);
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
	} else {
	
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
	
	$mediatype = $_GET['media_type'];
	
	$contentidsinfavorites = array();
	if(isset($othersprofile)) {
		$favoritessearch = sprintf("SELECT CONTENT_ID
									FROM MT_USER_FAVOURITES WHERE
									USERNAME = '$others' AND
									FAVORITES_TYPE = '$mediatype'");
	} else {
		$favoritessearch = sprintf("SELECT CONTENT_ID FROM MT_CONTENT
									WHERE USERNAME IN
									(SELECT CHANNEL_ID FROM MT_CHANNEL_SUBSCRIBERS
									WHERE USERNAME = '$username') AND
									CONTENT_TYPE = '$mediatype'");
	}
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
		echo 'alert("No Subscriptions found")';
		echo '</script>';
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
					$friendcheck = sprintf("SELECT *  FROM
											MT_USER_CONTACTS WHERE
											USERNAME = '$username' AND
											USER_CONTACT_ID = '$contentowner' AND
											IS_FRIEND = 'Y' AND
											IS_BLOCKED = 'N'");
					$check = mysql_query($friendcheck) or die('Failed to check friends');
			
					if((mysql_num_rows($check)) == 1) {
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
					$blockcheck = sprintf("SELECT *  FROM
										   MT_USER_CONTACTS WHERE
										   USERNAME = '$username' AND
								           USER_CONTACT_ID = '$contentowner' AND
								           IS_BLOCKED = 'Y'");
					$check = mysql_query($blockcheck) or die('Failed to check blocked friends');
					if((mysql_num_rows($check)) == 0) {
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
		$updatecountquery = "UPDATE MT_CONTENT SET VIEW_COUNT=VIEW_COUNT+1 WHERE
							 CONTENT_ID = '$contentid';";
		$updatecount = mysql_query($updatecountquery) or die("Failed to view count");
		$countid = mysql_insert_id();
	
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
					$_SESSION['contentownersubscribe'] = $contentowner;
					echo "<tr>"; 
					if($username != $contentowner) {
						echo "<td><label for='uploadedby'> <strong>Uploaded By:</strong> </label>";
						echo "<a href='./othersProfile.php?content_owner=".$contentowner."' class='stylish-link'><font size='4'>$contentowner</font></a>";
					} else {
						echo "<td><label for='uploadedby'> <strong>Uploaded By:</strong> </label>";
						echo "<a href='./userProfile.php'><font size='4' class='stylish-link'>$contentowner</font></a>";
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
					<tr> <td> <font size="5"> <strong>Subscriptions </strong> </font> </td> </tr>
					<tr> </tr>
					<tr> </tr>
					<tr> </tr>
					<tr> </tr>
					<tr> </tr>
					<tr> </tr>
					<?php 
					if(isset($othersprofile)) {
						for($i=0;$i<count($contentids);$i++) {
							echo "<tr><td><a href='./userSubscriptionsView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."&others_profile=".$othersprofile."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
							echo "<td><a href='./userSubscriptionsView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."&others_profile=".$othersprofile."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
						}
					} else {
						for($i=0;$i<count($contentids);$i++) {
							echo "<tr><td><a href='./userSubscriptionsView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
							echo "<td><a href='./userSubscriptionsView.php?content_id=".$contentids[$i]."&media_type=".$mediatype."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
						}			
					}					
					?>
					</table>
					
					</form>
					<?php 
					
			} 
			
			}
			
			if(isset($_POST['like'])) {
				$updatelikequery = "UPDATE MT_CONTENT SET LIKES=LIKES+1 WHERE
									CONTENT_ID = '$contentid';";
				$updatelike = mysql_query($updatelikequery) or die("Failed to like");
				$likeid = mysql_insert_id();
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
				$checkuserratedquery = sprintf("SELECT * FROM
												MT_CONTENT_RATING WHERE
												CONTENT_ID = '$contentid' AND
												USERNAME = '$username'");
				$checkuserrated = mysql_query($checkuserratedquery) or die('Failed to check whether user rated');
				if((mysql_num_rows($checkuserrated)) == 1) {
					$updateratingquery = "UPDATE MT_CONTENT_RATING SET RATING='$ratingvalue' WHERE
										  CONTENT_ID = '$contentid' AND
										  USERNAME = '$username'";
					$updaterating = mysql_query($updateratingquery) or die("Failed to update rating");
				}
				$checkuserquery = sprintf("SELECT USERNAME FROM
										   MT_CONTENT_RATING WHERE
										   CONTENT_ID = '$contentid' AND
									       USERNAME = '$username'");
				$checkuser = mysql_query($checkuserquery) or die('Failed to check user');
				$userrated = mysql_fetch_row($checkuser);
				if($userrated[0] != NULL) {
					$checkratingquery = sprintf("SELECT AVG(RATING) FROM
												 MT_CONTENT_RATING WHERE
												 CONTENT_ID = '$contentid'");
					$checkrating = mysql_query($checkratingquery) or die('Failed to check whether user rated');
					$ratingdetails = mysql_fetch_row($checkrating);
					$ratingaverage = $ratingdetails[0];
				} else {
					$ratingaverage = $ratingvalue;
					$insertfinalratingquery = "INSERT INTO MT_CONTENT_RATING
											   (CONTENT_ID, USERNAME, RATING)
											   VALUES('$contentid', '$username', '$ratingvalue')";
					$insertfinalrating = mysql_query($insertfinalratingquery) or die("Failed to insert rating");
				}
				$updatefinalratingquery = "UPDATE MT_CONTENT SET CONTENT_RATING='$ratingaverage' WHERE
										   CONTENT_ID = '$contentid';";
				$updatefinalrating = mysql_query($updatefinalratingquery) or die("Failed to update rating");
				$ratingid = mysql_insert_id();
			
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
	echo "<div>";
	include("./listsChannelHeader.php");
	echo "</div>";	
	echo "<br/>";
	echo "<br/>";
	
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
		$uploadsearch = sprintf("SELECT CONTENT_ID,
								 CONTENT_TITLE,
								 CONTENT_LOCATION
							     FROM MT_CONTENT WHERE
								 USERNAME = '$others' AND
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
	echo "<br/>";
	echo "<br/>";

	$mediatype = $_GET['mediatypeforchannel'];

	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$contentidsinfavorites = array();
		$favoritessearch = sprintf("SELECT CONTENT_ID
				FROM MT_USER_FAVOURITES WHERE
				USERNAME = '$others' AND
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
	echo "<br/>";
	echo "<br/>";
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
		$updatefriends = mysql_query($blockfriends) or die("Failed to block");
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
}



if(isset($mediatypeerror)) {
	echo '<script type="text/javascript">';
	echo 'alert("Please select a type to search in your channel")';
	echo '</script>';
}


?>

</body>
</html>