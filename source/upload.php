<?php 
session_start();
if(isset($_SESSION['username']) && $_SESSION['username'] != NULL) {
	$username = $_SESSION['username'];
	echo '<div>';
	include("./loginHeader.php");
	echo '</div>';
} else {
	print "<meta http-equiv='refresh' content='0;url=index.php'>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Upload</title>
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
<script type="text/javascript" src="js/UploadValidator.js">
</script>
</head>

<body>
<?php 
if(isset($_POST['upload'])) {
	$title = $_POST['title'];
	$mediacategory = $_POST['mediacategory'];
	$description = $_POST['description'];
	$mediatype = $_POST['mediatype'];
	$privacy = $_POST['privacy'];
	$ratingenabled = $_POST['ratingenabled'];
	$tags = $_POST['tags'];
	
	if($title == null) {
		$titleempty = 1;
		$empty = TRUE;
	} else if($mediacategory == 0) {
		$categoryempty = 1;
		$empty = TRUE;
	} else if($description == null) {
		$descriptionempty = 1;
		$empty = TRUE;
	} else if($mediatype == 0) {
		$mediatypeempty = 1;
		$empty = TRUE;
	} else if($privacy == 0) {
		$privacyempty = 1;
		$empty = TRUE;
	} else if($ratingenabled == 0) {
		$ratingenabledempty = 1;
		$empty = TRUE;
	} else if($tags == null) {
		$tagsempty = 1;
		$empty = TRUE;
	} else {
		$empty = FALSE;
	}
	
	if($empty) {
		
	} else {
		$filename = $_FILES['media']['name'];	
		$fileNameWithoutExt = substr($filename,0,strpos($filename,'.'));
		$fileextension = substr($filename, strpos($filename,'.')+1, strlen($filename)-1);
		$max_filesize = 20485760;
	
		if($mediatype == 1)	{
			$fileLocation = "fileUploads/audio/";
			$dbinsertLocation = "fileUploads/audio/";
			$filetype = 'A';
		} else if($mediatype == 2) {
			$fileLocation = "fileUploads/video/";
			$dbinsertLocation = "fileUploads/video/";
			$filetype = 'V';
		} else if($mediatype == 3) {
			$fileLocation= "fileUploads/image/";
			$dbinsertLocation = "fileUploads/image/";
			$filetype = 'I';
		}
	
		$filepath = $fileLocation.basename( $_FILES['media']['name']) ;
		$dbinsertpath = $dbinsertLocation.basename( $_FILES['media']['name']) ;
	
		if($fileextension != ("jpg" || "JPG" || "jpeg" || "gif" || "MPG" || "png" || "bmp" || "mp3" || "wma" || "mp4" || "mpeg" || "mp4" || "wmv" || "avi" || "mov")) {
			$error = TRUE; 
			$extensionError = 1;
		} else if($_FILES['media']['size'] > $max_filesize) {
			$error = TRUE;
			$sizeError = 1;
		} else if(!is_writable($fileLocation)) {
			$error = TRUE;
			$filepathError = 1;
		} else {
			$error = FALSE;
		}
	
		if($error) {
			
		} else {		

			if($mediacategory == 1)	{
				$filecategory = 'M';
			} else if($mediacategory == 2) {
				$filecategory = 'S';
			} else if($mediacategory == 3) {
				$filecategory = 'G';
			} else if($mediacategory == 4) {
				$filecategory = 'F';
			} else if($mediacategory == 5) {
				$filecategory = 'T';
			} else if($mediacategory == 6) {
				$filecategory = 'N';
			}
		
			if($privacy == 1)	{
				$filePrivacy='S';
			} else if($privacy == 2) {
				$filePrivacy='P';
			} else if($privacy == 3) {
				$filePrivacy = 'F';
			}
		
			if($ratingenabled == 1)	{
				$fileRatingEnabled='Y';
			} else if($ratingenabled == 2) {
				$fileRatingEnabled='N';
			} 
		
			if(move_uploaded_file($_FILES['media']['tmp_name'], $filepath)) {
		
				$dbconnection = "connection.php";
		
				if(file_exists($dbconnection)) {
					include $dbconnection;
				} else if(file_exists("../".$dbconnection)) {
					include "../".$dbconnection;
				} else {
					include "../../".$dbconnection;
				}		
			
				dbConnect();

                $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_CONTENT(USERNAME, CONTENT_TITLE, CONTENT_TYPE,
  								                       CONTENT_LOCATION, CONTENT_FORMAT, CONTENT_DESCRIPTION,
  								                       CONTENT_CATEGORY, CONTENT_SHARING, CONTENT_RATING, RATING_ENABLED,
  								                       LIKES, DISLIKES, VIEW_COUNT, UPLOAD_DATE)
  								                       VALUES(?, ?, ?, ?, ?, ?, ?, ?, '0', ?, '0', '0', '0', NOW())");
                mysqli_stmt_bind_param($stmt, 'ssssssss', $username, $title, $filetype, $dbinsertpath, $fileextension,
                                                        $description, $filecategory, $filePrivacy, $fileRatingEnabled);
                mysqli_stmt_execute($stmt);
                $insertfile = mysqli_stmt_get_result($stmt) or die("Failed to upload media");
                $fileid = mysqli_insert_id($dbconnection);
                mysqli_stmt_close($stmt);
			
				if(!isset($fileid)) {
					die("Failed to upload media");
				} else {
					$mediauploaded = 1;

                    $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_CONTENT_TAGS (CONTENT_ID, TAGS) VALUES(?, ?)");
                    mysqli_stmt_bind_param($stmt, 'is', $fileid, $tags);
                    mysqli_stmt_execute($stmt);
                    $insertkeywords = mysqli_stmt_get_result($stmt) or die("Failed to upload keywords");
                    mysqli_stmt_close($stmt);
					
					if(!isset($insertkeywords)) {
						die("Failed to upload keywords");
					} else {
						$tagsuploaded = 1;
					}
				}
			} else {
				print 'There was an error during the file upload.  Please try again';
			}
		}
	}
					 
	if(isset($mediauploaded) && isset($tagsuploaded)) {
		echo '<script type="text/javascript">';
		echo 'alert("Uploaded successfully")';
		echo '</script>';
	}
}
?>

<br/>
<br/>
<br/>

<form name="uploadform" method="post" action="upload.php" enctype="multipart/form-data" onsubmit="return uploadFields()">
<table align="center">
	<tr>
		<td> <label for="title"> <strong>Title:*</strong> </label> </td>
		<td> <input type="text" size="40" maxlength="60" name="title" id="title"/></td>
		<?php 
		if(isset($titleempty)) {
			echo '<script type="text/javascript">';
			echo 'alert("Please enter any title for your media file")';
			echo '</script>';
		}
		?>
	</tr>

	<tr> 
		<td> <strong>What type of media:*</strong> </td> 
		<td> 
			<select name="mediatype" id="mediatype"> 
 				<option value="0">---</option> 
				<option value="1">Audio</option> 
				<option value="2">Video</option>
				<option value="3">Image</option>
			</select>
		</td>
		<?php 
		if(isset($mediatypeempty)) {
			echo '<script type="text/javascript">';
			echo 'alert("Please select any value from Media Type field")';
			echo '</script>';
		}
		?>
	</tr>
	
	<tr> 
		<td> <strong>Category:*</strong> </td> 
		<td> 
			<select name="mediacategory" id="mediacategory"> 
 				<option value="0">---</option> 
				<option value="1">Music</option> 
				<option value="2">Sport</option>
				<option value="3">Gaming</option>
				<option value="4">Films</option>
				<option value="5">TV Shows</option>
				<option value="6">News</option>
			</select>
		</td>
		<?php 
		if(isset($categoryempty)) {
			echo '<script type="text/javascript">';
			echo 'alert("Please select any value from Category field")';
			echo '</script>';
		}
		?>
	</tr>
	
	<tr>
		<td> <label for="description"> <strong>Description:*</strong> </label> </td>
		<td> <textarea name="description" rows="4" id="description"></textarea></td>
		<?php 
		if(isset($descriptionempty)) {
			echo '<script type="text/javascript">';
			echo 'alert("Please enter any decription for your media file")';
			echo '</script>';
		}
		?>
	</tr>
	
	<tr> 
		<td> <strong>Privacy:*</strong> </td> 
		<td> 
			<select name="privacy" id="privacy"> 
 				<option value="0">---</option> 
				<option value="1">Yes, the media can be viewed by public</option> 
				<option value="2">Keep my media private</option>
				<option value="3">Share only with friends</option>
			</select>
		</td>
		<?php 
		if(isset($privacyempty)) {
			echo '<script type="text/javascript">';
			echo 'alert("Please select any value from Privacy field")';
			echo '</script>';
		}
		?>
		
	</tr>
	
	<tr> 
		<td> <strong>Enable Rating:*</strong> </td> 
		<td> 
			<select name="ratingenabled" id="ratingenabled"> 
 				<option value="0">---</option> 
				<option value="1">Yes</option> 
				<option value="2">No</option>
			</select>
		</td>
		<?php 
		if(isset($ratingenabledempty)) {
			echo '<script type="text/javascript">';
			echo 'alert("Please select any value from Enable Rating field")';
			echo '</script>';
		}
		?>
	</tr>		
	
	<tr>
		<td> <label for="tags"> <strong>Please add tags for your video:*</strong> </label> </td> 
		<td> <input type="text" size="40" maxlength="100" name="tags" id="tags"/></td>
		<?php 
		if(isset($tagsempty)) {
			echo '<script type="text/javascript">';
			echo 'alert("Please add tags to your media file")';
			echo '</script>';
		}
		?>
	</tr>
	
	<tr>
		<td> <label for="addmedia"> <strong>Add Media:*</strong> </label> </td>
		<td>                 
			<input type="hidden" name="MAX_FILE_SIZE" value="20485760" />
            <label><em> (Each file limit 10M)</em></label><br/>
            <input type="file" name="media"  size="50" />
		</td>
		<?php 
		if(isset($extensionError)) { 
			echo '<script type="text/javascript">';
			echo 'alert("Sorry but this filetype is not supported")';
			echo '</script>';	
		} elseif(isset($sizeError)) {
			echo '<script type="text/javascript">';
			echo 'alert("Sorry but this file is too large")';
			echo '</script>';
		} elseif(isset($filepathError)) {
			echo '<script type="text/javascript">';
			echo 'alert("Some error occured. Please try again")';
			echo '</script>';
		}
		
		?>
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
	
	
	<tr> 
		<td> &nbsp; </td> 
		<td> <input type="submit" align="right" name="upload" value="Upload" class="stylish-link"/> </td> 
	</tr>
	
</table>
</form>

</body>
</html>