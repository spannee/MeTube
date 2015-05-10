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

session_start();
if(isset($_SESSION['username']) && $_SESSION['username'] != NULL) {
	$username = $_SESSION['username'];
} else {
	print "<meta http-equiv='refresh' content='0;url=index.php'>";
}

if(isset($_GET['topic_id'])) {
	$topicid = $_GET['topic_id'];
	$_SESSION['topicid'] = $topicid;
} elseif(isset($_SESSION['topicid'])) {
	$topicid = $_SESSION['topicid'];
} 


?>


<!DOCTYPE html PUBLIC -//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd>
<html xmlns=http://www.w3.org/1999/xhtml>

<head>
<meta http-equiv=Content-Type content=text/html charset=UTF-8 />
<title>Discussion</title>
<style type="text/css">
	.center {
    	position:fixed;
        top:65%;
        left:23%; 
    }
</style>
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
</head>

<body>
<div>
<?php include("./loginHeader.php"); ?>
</div>
<form name="discussions" method="post">
	<table align="center" border="1" width=80% height="100">
	<?php 
	
	if(isset($_POST['index'])) {
		$indexvalue = $_POST['index'];
		$startindex = ($indexvalue * 5) - 5;
        $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_DISCUSSION WHERE
									           TOPIC_ID = ? LIMIT ?,5 ");
        mysqli_stmt_bind_param($stmt, 'ii', $topicid, $startindex);
	} else {
        $stmt = mysqli_prepare($dbconnection, "SELECT COUNT(TOPIC_ID) FROM MT_DISCUSSION WHERE TOPIC_ID = ? ");
        mysqli_stmt_bind_param($stmt, 'i', $topicid);
        mysqli_stmt_execute($stmt);
        $discussionreplycount = mysqli_stmt_get_result($stmt) or die('Failed to check count');
        mysqli_stmt_close($stmt);
		$maxdiscussionreplycount = mysqli_fetch_row($discussionreplycount);
		if($maxdiscussionreplycount[0] != 0) {
			$required = $maxdiscussionreplycount[0] % 5;
			
			if($required == 0) {
				$shown = 5;
			} else {
				$shown = $required;
			}
		} else {
			$shown = 5;
		}

        $stmt = mysqli_prepare($dbconnection, "SELECT * FROM (SELECT * FROM MT_DISCUSSION WHERE
 										       TOPIC_ID = ? ORDER BY CONTENT_REPLIED_TIMESTAMP DESC
                                               LIMIT ?) AS LAST_DISCUSSION ORDER BY CONTENT_REPLIED_TIMESTAMP");
        mysqli_stmt_bind_param($stmt, 'ii', $topicid, $shown);
	} 
	
	if(isset($stmt)) {
        mysqli_stmt_execute($stmt);
        $discussionreply = mysqli_stmt_get_result($stmt) or die('Failed to retrieve discussion comment');
        mysqli_stmt_close($stmt);
		if(mysqli_num_rows($discussionreply) > 0) {
			while($discussionreplyresult = mysqli_fetch_array($discussionreply)) {
				echo '<tr></tr>';
				echo '<tr></tr>';
				echo '<tr></tr>';
				echo '<tr></tr>';
				echo '<tr></tr>';
				echo '<tr></tr>';
				echo '<tr></tr>';
				echo '<tr></tr>';
				echo '<tr></tr>';
				echo '<tr></tr>';
				$discussionreplyid = $discussionreplyresult["DISCUSSION_REPLIES_ID"];
				$discussioncomment = $discussionreplyresult["DISCUSSION_REPLIES"];
				$discussionsequenceno = $discussionreplyresult["DISCUSSION_REPLIES_SEQ_NO"];
				$discussionposter = $discussionreplyresult["USERNAME_WHO_REPLIES"];
				$discussedtime = $discussionreplyresult["CONTENT_REPLIED_TIMESTAMP"];
				echo '<tr>';
				echo '<td>';
				echo $discussioncomment;
				echo '</td>';
				echo '<td>';
				echo "PostedBy:";
				echo $discussionposter;
				echo "<br/>";
				echo "PostedOn:";
				echo $discussedtime;
				echo '</td>';
				echo '<td>';
				echo '<button type="submit" name="delete" value="';
				echo $discussionreplyid;
				echo '" class="stylish-link"/>Delete</button>';
				echo '</td>';
				echo '</tr>';
			}
		}
	}
	
	if(isset($_POST['postreply'])) {
		$forumcomment = $_POST['forumcomment'];
		
		if($forumcomment != NULL) {
            $stmt = mysqli_prepare($dbconnection, "SELECT MAX(DISCUSSION_REPLIES_SEQ_NO) FROM MT_DISCUSSION WHERE
									 		       TOPIC_ID = ?");
            mysqli_stmt_bind_param($stmt, 'i', $topicid);
            mysqli_stmt_execute($stmt);
            $commentsequence = mysqli_stmt_get_result($stmt) or die('Failed to check sequence number');
            mysqli_stmt_close($stmt);
			$maxcommentsequence = mysqli_fetch_row($commentsequence);
			if($maxcommentsequence[0] != NULL) {
				$sequencenumber = $maxcommentsequence[0] + 1;				
			} else {
				$sequencenumber = 1;
			}

            $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_DISCUSSION(TOPIC_ID, DISCUSSION_REPLIES,
                                                   DISCUSSION_REPLIES_SEQ_NO, USERNAME_WHO_REPLIES,
                                                   CONTENT_REPLIED_TIMESTAMP)
							 	                   VALUES(?, ?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, 'isis', $topicid, $forumcomment, $sequencenumber, $username);
            mysqli_stmt_execute($stmt);
            $addcomment = mysqli_stmt_get_result($stmt) or die("Failed to post");
            $discussionid = mysqli_insert_id($dbconnection);
            mysqli_stmt_close($stmt);

			if(isset($discussionid)) {
				print '<meta http-equiv="refresh" content="0;url=./discussions.php?">';
			}
		}
	}
	
	if(isset($_POST['delete'])) {
		$discussionreplyid = $_POST['delete'];

        $stmt = mysqli_prepare($dbconnection, "SELECT USERNAME_WHO_CREATES FROM MT_TOPICS WHERE
						   	                   TOPIC_ID IN(SELECT TOPIC_ID FROM MT_DISCUSSION WHERE
						                       DISCUSSION_REPLIES_ID = ?)");
        mysqli_stmt_bind_param($stmt, 'i', $discussionreplyid);
        mysqli_stmt_execute($stmt);
        $findcreator = mysqli_stmt_get_result($stmt) or die('Failed to find creator');
        mysqli_stmt_close($stmt);
		$groupcreator = mysqli_fetch_row($findcreator);

        $stmt = mysqli_prepare($dbconnection, "SELECT USERNAME_WHO_REPLIES FROM MT_DISCUSSION WHERE
							                   DISCUSSION_REPLIES_ID = ?");
        mysqli_stmt_bind_param($stmt, 'i', $discussionreplyid);
        mysqli_stmt_execute($stmt);
        $findposter = mysqli_stmt_get_result($stmt) or die('Failed to find poster');
        mysqli_stmt_close($stmt);
		$poster = mysqli_fetch_row($findposter);
		
		if($username == $groupcreator[0] || $username == $poster[0]) {
            $stmt = mysqli_prepare($dbconnection, "DELETE FROM MT_DISCUSSION WHERE
                                                   DISCUSSION_REPLIES_ID = ?");
            mysqli_stmt_bind_param($stmt, 'i', $discussionreplyid);
            mysqli_stmt_execute($stmt);
            $deletecomment = mysqli_stmt_get_result($stmt) or die('Failed to delete comment');
            $deletecommentid = mysqli_insert_id($dbconnection);
            mysqli_stmt_close($stmt);
			
			if(isset($deletecommentid)) {
				print '<meta http-equiv="refresh" content="0;url=./discussions.php?">';
			}			
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("You dont have Permission")';
			echo '</script>';
		}
		
		
	}
	?>
	</table>
	
	<div class="center">
	<table>
	<?php
    $stmt = mysqli_prepare($dbconnection, "SELECT MAX(DISCUSSION_REPLIES_SEQ_NO) FROM MT_DISCUSSION WHERE
									       TOPIC_ID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $topicid);
    mysqli_stmt_execute($stmt);
    $commentsequence = mysqli_stmt_get_result($stmt) or die('Failed to check sequence number');
    mysqli_stmt_close($stmt);
	$maxcommentsequence = mysqli_fetch_row($commentsequence);
	
	if($maxcommentsequence[0] != NULL) {
		$extra = $maxcommentsequence % 5; 
	}
	
	if($maxcommentsequence[0] != NULL && $maxcommentsequence[0] <= 5) {
		
	} elseif($maxcommentsequence[0] != NULL && $maxcommentsequence[0] > 5){
		$extra = $maxcommentsequence % 5;
		
		$numbersrequired = intval($maxcommentsequence[0] / 5);
		if($extra > 0) {
			$numbersrequired = $numbersrequired + 1;
		}
		echo '<tr>';
		echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		for($i=1;$i<=$numbersrequired;$i++) {
			echo '<input type="submit" name="index" value="';
			echo $i;
			echo '" style="width: 20px;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			
		}
		echo '</td>';
		echo '</tr>';
	}
	?>
	<tr><td> <textarea name="forumcomment" rows="6" cols="80" id="forumcomment"></textarea> </td></tr>
	<tr>
		
		<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" name="postreply" value="Post Message" class="stylish-link"/>
		</td> 
	</tr>
	</table>
	</div>
</form>
 </body>
 </html>