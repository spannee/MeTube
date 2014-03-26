<?php 
session_start();
if(isset($_SESSION['username']) && $_SESSION['username'] != NULL) {
	$username = $_SESSION['username'];
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

<div>
<?php
include("./contentHeader.php");
?>
</div>

<br/>
<br/>


<?php 

if(isset($_GET['content_id'])) {
	$contentid=intval($_GET['content_id']);
	
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
	
	if(isset($contenttype)) {
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
		
		if(isset($canview)) {
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
					<EMBED TYPE="application/x-mplayer2" WIDTH="600" HEIGHT="420" SRC="<?php echo $contentlocation ?>" NAME="MediaPlayer">
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
						$_SESSION['contentownercontent'] = $contentowner;
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
							if($setallfeatures) {
							?>
							<input type="submit" name="addtofavorites" value="Favorites" style="width: 70px;"/>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<?php
							$loadplaylistsquery = sprintf("SELECT PLAYLIST_ID, PLAYLIST_NAME
														   FROM MT_USER_PLAYLIST WHERE
														   USERNAME = '$username' AND
														   PLAYLIST_TYPE = '$contenttype'");
							$loadplaylists = mysql_query($loadplaylistsquery) or die('Failed to load playlists');
							if((mysql_num_rows($loadplaylists)) > 0) {
								echo "<select name='playlists' id='playlists'>";
								echo "<option value='0'>Select Playlist</option>";
								while($playlistresult = mysql_fetch_array($loadplaylists)) {
									$playlistid = $playlistresult["PLAYLIST_ID"];
									$playlistidentity = $playlistresult["PLAYLIST_NAME"];
									echo "<option value='$playlistid'>$playlistidentity</option>";
								}
								echo "</select>";
								echo "<input type='submit' name='addtoplaylist' value='Add To Playlist' style='width: 105px;'/>";
							}
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
				<tr> <td> <font size="5"> <strong>Featured </strong> </font> </td> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<tr> </tr>
				<?php 
				$contentidsintags = array();
				$contentids = array();
				$contenttitles = array();
				$tagsearchquery = sprintf("SELECT TAGS
										   FROM MT_CONTENT_TAGS WHERE
										   CONTENT_ID = '$contentid'");
				$tagsearch = mysql_query($tagsearchquery) or die('Failed to search tags');
				$tagsintable = mysql_fetch_row($tagsearch);
				$tagsmixture = $tagsintable[0];
				$tags = explode(' ',$tagsmixture);

				foreach ($tags as $tag) {
					$valuesearch = "SELECT CONTENT_ID
									FROM MT_CONTENT_TAGS WHERE
									TAGS LIKE '%$tag%'
									AND CONTENT_ID != '$contentid'";
					$valuesearchquery = mysql_query($valuesearch) or die('Failed to search tags');
					while($valuesearchresults = mysql_fetch_array($valuesearchquery)) {
						$contentidsintags[] = $valuesearchresults["CONTENT_ID"];
					}
				}
				foreach ($contentidsintags as $id) {
					$contentsearch = sprintf("SELECT CONTENT_ID,
											  CONTENT_TITLE,
											  CONTENT_LOCATION
											  FROM MT_CONTENT WHERE
											  CONTENT_TYPE = '$contenttype'
											  AND
											  CONTENT_ID = '$id' AND 
									   		  CONTENT_SHARING != 'P'");
					$valuecontentquery = mysql_query($contentsearch) or die('Failed to search content');
					while($contentsearchresults = mysql_fetch_array($valuecontentquery)) {
						$contentids[] = $contentsearchresults["CONTENT_ID"];
						$contenttitles[] = $contentsearchresults["CONTENT_TITLE"];
					}
				}
				
				for($i=0;$i<count($contentids);$i++) {
					echo "<tr><td><a href='./content.php?content_id=".$contentids[$i]."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
					echo "<td><a href='./content.php?content_id=".$contentids[$i]."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
				}
				
				?>
				</table>
				
				</form>
				<?php 
			
		} 
		
		} else {
			print '<meta http-equiv="refresh" content="0;url=./index.php?">';
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
	
	if(isset($_POST['addtoplaylist'])) {
		$playlistnumber = $_POST["playlists"];
		
		if($playlistnumber == 0) {
			echo '<script type="text/javascript">';
			echo 'alert("Please select a playlist")';
			echo '</script>';
		} else {
			$checkplaylistsquery = sprintf("SELECT * FROM
										    MT_PLAYLIST_CONTENT WHERE
										    PLAYLIST_ID = '$playlistnumber' AND
											CONTENT_ID = '$contentid'");
			$checkplaylists = mysql_query($checkplaylistsquery) or die('Failed to check playlists');
			
			if((mysql_num_rows($checkplaylists)) > 0) {
				echo '<script type="text/javascript">';
				echo 'alert("This video is already there in the selected playlist")';
				echo '</script>';
			} else {
				$addcontentquery = "INSERT INTO MT_PLAYLIST_CONTENT
									(PLAYLIST_ID, CONTENT_ID)
									VALUES('$playlistnumber', '$contentid')";
				$addcontent = mysql_query($addcontentquery) or die("Failed to add content to playlist");
				$addcontentid = mysql_insert_id();
				
				if(isset($addcontentid)) {
					echo '<script type="text/javascript">';
					echo 'alert("Added to playlist")';
					echo '</script>';
				}
			}
		}
	}
	
	if(isset($_POST['addtofavorites'])) {
		$checkfavoritesquery = sprintf("SELECT * FROM
										MT_USER_FAVOURITES WHERE
										USERNAME = '$username' AND
										CONTENT_ID = '$contentid'");
		$checkfavorites = mysql_query($checkfavoritesquery) or die('Failed to check favorites');
		
		if((mysql_num_rows($checkfavorites)) > 0) {
			echo '<script type="text/javascript">';
			echo 'alert("This video is already there in favorites")';
			echo '</script>';
		} else {
			$addcontentquery = "INSERT INTO MT_USER_FAVOURITES
								(USERNAME, CONTENT_ID, FAVORITES_TYPE)
								VALUES('$username', '$contentid', '$contenttype')";
			$addcontent = mysql_query($addcontentquery) or die("Failed to add content to favorites");
			$addcontentid = mysql_insert_id();
			if(isset($addcontentid)) {
				echo '<script type="text/javascript">';
				echo 'alert("Added to Favorites")';
				echo '</script>';
			}
		}
	}
	
	
}
?>



</body>
</html>
