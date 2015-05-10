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
                    $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_USER_CONTACTS WHERE
                                                           USERNAME = ? AND USER_CONTACT_ID = ? AND
                                                           IS_FRIEND = 'Y' AND IS_BLOCKED = 'N'");
                    mysqli_stmt_bind_param($stmt, 'ss', $username, $contentowner);
                    mysqli_stmt_execute($stmt);
                    $check = mysqli_stmt_get_result($stmt) or die('Failed to check friends');
                    mysqli_stmt_close($stmt);
					
					if((mysqli_num_rows($check)) == 1) {
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
                    $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_USER_CONTACTS WHERE
                                                           USERNAME = ? AND USER_CONTACT_ID = ? AND
                                                           IS_BLOCKED = 'Y'");
                    mysqli_stmt_bind_param($stmt, 'ss', $username, $contentowner);
                    mysqli_stmt_execute($stmt);
                    $check = mysqli_stmt_get_result($stmt) or die('Failed to check blocked friends');
                    mysqli_stmt_close($stmt);
					
					if((mysqli_num_rows($check)) == 0) {
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
                            $stmt = mysqli_prepare($dbconnection, "SELECT PLAYLIST_ID, PLAYLIST_NAME
														           FROM MT_USER_PLAYLIST WHERE
														           USERNAME = ? AND
                                                                   PLAYLIST_TYPE = ?");
                            mysqli_stmt_bind_param($stmt, 'ss', $username, $contenttype);
                            mysqli_stmt_execute($stmt);
                            $loadplaylists = mysqli_stmt_get_result($stmt) or die('Failed to load playlists');
                            mysqli_stmt_close($stmt);

							if((mysqli_num_rows($loadplaylists)) > 0) {
								echo "<select name='playlists' id='playlists'>";
								echo "<option value='0'>Select Playlist</option>";
								while($playlistresult = mysqli_fetch_array($loadplaylists)) {
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

                $stmt = mysqli_prepare($dbconnection, "SELECT TAGS
                                                       FROM MT_CONTENT_TAGS WHERE
                                                       CONTENT_ID = ?");
                mysqli_stmt_bind_param($stmt, 'i', $contentid);
                mysqli_stmt_execute($stmt);
                $tagsearch = mysqli_stmt_get_result($stmt) or die('Failed to search tags');
                mysqli_stmt_close($stmt);

				$tagsintable = mysqli_fetch_row($tagsearch);
				$tagsmixture = $tagsintable[0];
				$tags = explode(' ',$tagsmixture);

				foreach ($tags as $tag) {
                    $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID
                                                           FROM MT_CONTENT_TAGS WHERE
                                                           TAGS LIKE ?
                                                           AND CONTENT_ID != ?");
                    $tag = '%' . $tag . '%';
                    mysqli_stmt_bind_param($stmt, 'si', $tag, $contentid);
                    mysqli_stmt_execute($stmt);
                    $valuesearch = mysqli_stmt_get_result($stmt) or die('Failed to search tags');
                    mysqli_stmt_close($stmt);

					while($valuesearchresults = mysqli_fetch_array($valuesearch)) {
						$contentidsintags[] = $valuesearchresults["CONTENT_ID"];
					}
				}
				foreach ($contentidsintags as $id) {
                    $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
											               FROM MT_CONTENT WHERE CONTENT_TYPE = ? AND
											               CONTENT_ID = ? AND CONTENT_SHARING != 'P'");
                    mysqli_stmt_bind_param($stmt, 'si', $contenttype, $id);
                    mysqli_stmt_execute($stmt);
                    $contentsearch = mysqli_stmt_get_result($stmt) or die('Failed to search content');
                    mysqli_stmt_close($stmt);

					while($contentsearchresults = mysqli_fetch_array($contentsearch)) {
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
        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_CONTENT SET LIKES=LIKES+1 WHERE
					  		                   CONTENT_ID = ?;");
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
        $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_CONTENT_RATING
                                               WHERE CONTENT_ID = ? AND USERNAME = ?");
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
        $stmt = mysqli_prepare($dbconnection, "SELECT USERNAME FROM MT_CONTENT_RATING
                                               WHERE CONTENT_ID = ? AND USERNAME = ?");
        mysqli_stmt_bind_param($stmt, 'is', $contentid, $username);
        mysqli_stmt_execute($stmt);
        $checkuser = mysqli_stmt_get_result($stmt) or die('Failed to check user');
        mysqli_stmt_close($stmt);
		$userrated = mysqli_fetch_row($checkuser);
		if($userrated[0] != NULL) {
            $stmt = mysqli_prepare($dbconnection, "SELECT AVG(RATING) FROM
                                                   MT_CONTENT_RATING WHERE CONTENT_ID = ?");
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
        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_CONTENT SET CONTENT_RATING=? WHERE CONTENT_ID = ?;");
        mysqli_stmt_bind_param($stmt, 'ii', $ratingaverage, $contentid);
        mysqli_stmt_execute($stmt);
        $updatefinalrating = mysqli_stmt_get_result($stmt) or die("Failed to update rating");
        $ratingid = mysqli_insert_id($dbconnection);
        mysqli_stmt_close($stmt);

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
            $stmt = mysqli_prepare($dbconnection, "SELECT * FROM
										           MT_PLAYLIST_CONTENT WHERE
										           PLAYLIST_ID = ? AND
											       CONTENT_ID = ?");
            mysqli_stmt_bind_param($stmt, 'ii', $playlistnumber, $contentid);
            mysqli_stmt_execute($stmt);
            $checkplaylists = mysqli_stmt_get_result($stmt) or die('Failed to check playlists');
            mysqli_stmt_close($stmt);
			
			if((mysqli_num_rows($checkplaylists)) > 0) {
				echo '<script type="text/javascript">';
				echo 'alert("This video is already there in the selected playlist")';
				echo '</script>';
			} else {
                $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_PLAYLIST_CONTENT
                                                       (PLAYLIST_ID, CONTENT_ID)
                                                       VALUES(?, ?)");
                mysqli_stmt_bind_param($stmt, 'ii', $playlistnumber, $contentid);
                mysqli_stmt_execute($stmt);
                $addcontent = mysqli_stmt_get_result($stmt) or die("Failed to add content to playlist");
                $addcontentid = mysqli_insert_id($dbconnection);
                mysqli_stmt_close($stmt);

				if(isset($addcontentid)) {
					echo '<script type="text/javascript">';
					echo 'alert("Added to playlist")';
					echo '</script>';
				}
			}
		}
	}
	
	if(isset($_POST['addtofavorites'])) {
        $stmt = mysqli_prepare($dbconnection, "SELECT * FROM
                                               MT_USER_FAVOURITES WHERE
                                               USERNAME = ? AND
                                               CONTENT_ID = ?");
        mysqli_stmt_bind_param($stmt, 'si', $username, $contentid);
        mysqli_stmt_execute($stmt);
        $checkfavorites = mysqli_stmt_get_result($stmt) or die('Failed to check favorites');
        mysqli_stmt_close($stmt);
		
		if((mysqli_num_rows($checkfavorites)) > 0) {
			echo '<script type="text/javascript">';
			echo 'alert("This video is already there in favorites")';
			echo '</script>';
		} else {
            $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_USER_FAVOURITES
                                                   (USERNAME, CONTENT_ID, FAVORITES_TYPE)
                                                   VALUES(?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sis', $username, $contentid, $contenttype);
            mysqli_stmt_execute($stmt);
            $addcontent = mysqli_stmt_get_result($stmt) or die("Failed to add content to playlist");
            $addcontentid = mysqli_insert_id($dbconnection);
            mysqli_stmt_close($stmt);

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
