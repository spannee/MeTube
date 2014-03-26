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
		$discussionreplyquery = sprintf("SELECT * FROM MT_DISCUSSION WHERE
									     TOPIC_ID = '$topicid' LIMIT $startindex,5 ");
	} else {
		$discussionreplycountquery = sprintf("SELECT COUNT(TOPIC_ID) FROM MT_DISCUSSION WHERE TOPIC_ID = '$topicid' ");
		$discussionreplycount = mysql_query($discussionreplycountquery) or die('Failed to check count');
		$maxdiscussionreplycount = mysql_fetch_row($discussionreplycount);
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
		
		$discussionreplyquery = sprintf("SELECT * FROM (SELECT * FROM MT_DISCUSSION WHERE
 										 TOPIC_ID = '$topicid' ORDER BY CONTENT_REPLIED_TIMESTAMP DESC
     									 LIMIT $shown) AS LAST_DISCUSSION ORDER BY CONTENT_REPLIED_TIMESTAMP");	
	} 
	
	if(isset($discussionreplyquery)) {
		$discussionreply = mysql_query($discussionreplyquery) or die('Failed to retrieve discussion comment');
		if(mysql_num_rows($discussionreply) > 0) {
			while($discussionreplyresult = mysql_fetch_array($discussionreply)) {
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
			$commentsequencequery = sprintf("SELECT MAX(DISCUSSION_REPLIES_SEQ_NO) FROM MT_DISCUSSION WHERE
									 		 TOPIC_ID = '$topicid'");
		
			$commentsequence = mysql_query($commentsequencequery) or die('Failed to check sequence number');
			$maxcommentsequence = mysql_fetch_row($commentsequence);
			if($maxcommentsequence[0] != NULL) {
				$sequencenumber = $maxcommentsequence[0] + 1;				
			} else {
				$sequencenumber = 1;
			}
		
			$addcommentquery = "INSERT INTO MT_DISCUSSION(TOPIC_ID, DISCUSSION_REPLIES, DISCUSSION_REPLIES_SEQ_NO, USERNAME_WHO_REPLIES, CONTENT_REPLIED_TIMESTAMP)
							 	VALUES('$topicid', '$forumcomment', '$sequencenumber', '$username', NOW())";
			$addcomment = mysql_query($addcommentquery) or die("Failed to post");
			$discussionid = mysql_insert_id();
			
			if(isset($discussionid)) {
				print '<meta http-equiv="refresh" content="0;url=./discussions.php?">';
			}
		}
	}
	
	if(isset($_POST['delete'])) {
		$discussionreplyid = $_POST['delete'];
		
		$findcreatorquery = "SELECT USERNAME_WHO_CREATES FROM MT_TOPICS WHERE
						   	 TOPIC_ID IN(SELECT TOPIC_ID FROM MT_DISCUSSION WHERE 
						     DISCUSSION_REPLIES_ID = '$discussionreplyid')";
		$findcreator = mysql_query($findcreatorquery);
		
		$groupcreator = mysql_fetch_row($findcreator);
		
		$findposterquery = "SELECT USERNAME_WHO_REPLIES FROM MT_DISCUSSION WHERE
							DISCUSSION_REPLIES_ID = '$discussionreplyid'";
		$findposter = mysql_query($findposterquery);
		
		$poster = mysql_fetch_row($findposter);
		
		if($username == $groupcreator[0] || $username == $poster[0]) {
			$deletecommentquery = "DELETE FROM MT_DISCUSSION WHERE
								   DISCUSSION_REPLIES_ID = '$discussionreplyid'";
			$deletecomment = mysql_query($deletecommentquery);
			$deletecommentid = mysql_insert_id();
			
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
	$commentsequencequery = sprintf("SELECT MAX(DISCUSSION_REPLIES_SEQ_NO) FROM MT_DISCUSSION WHERE
									 TOPIC_ID = '$topicid'");
	
	$commentsequence = mysql_query($commentsequencequery) or die('Failed to check sequence number');
	$maxcommentsequence = mysql_fetch_row($commentsequence);
	
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