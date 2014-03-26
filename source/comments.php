<?php 
if(isset($_GET['content_id'])) {
	$_SESSION['id'] = $contentid;
}
if(!isset($id))
	$id = $_SESSION['id'];

if(isset($_SESSION['username']) && $_SESSION['username'] != NULL) {
	$username = $_SESSION['username'];
}
?>

<!DOCTYPE html PUBLIC -//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd>
<html xmlns=http://www.w3.org/1999/xhtml>

<head>
<meta http-equiv=Content-Type content=text/html charset=UTF-8 />
<title>Comments</title>
<link rel="stylesheet" type="text/css" href="/css/linkStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
<style type="text/css">
	.center {
    	position:fixed;
        top:40%;
        left:35%; 
    }

</style>
</head>

<body>
<form name="discussions" method="post">
<p><strong>Comments</strong></p>
<table align="center" border="1" width=100% height="100">
<tr><td></td></tr>

<?php 
if(isset($_POST['index'])) {
	$indexvalue = $_POST['index'];
	$startindex = ($indexvalue * 10) - 10;
	$commentquery = sprintf("SELECT * FROM MT_CONTENT_COMMENTS WHERE
							 CONTENT_ID = '$id' LIMIT $startindex,10 ");
} else {
	$commentcountquery = sprintf("SELECT COUNT(CONTENT_ID) FROM MT_CONTENT_COMMENTS WHERE CONTENT_ID = '$id' ");
	$commentcount = mysql_query($commentcountquery) or die('Failed to check count');
	$maxcommentcount = mysql_fetch_row($commentcount);
	if($maxcommentcount[0] != 0) {
		$required = $maxcommentcount[0] % 10;
		if($required == 0) {
			$shown = 10;
		} else {
			$shown = $required;
		}
	} else {
		$shown = 10;
	}
		
	$commentquery = sprintf("SELECT * FROM (SELECT * FROM MT_CONTENT_COMMENTS WHERE
 							 CONTENT_ID = '$id' ORDER BY CONTENT_COMMENT_TIME DESC
     						 LIMIT $shown) AS LAST_COMMENT ORDER BY CONTENT_COMMENT_TIME");	
} 

if(isset($commentquery)) {
	$commentrequired = mysql_query($commentquery) or die('Failed to retrieve comment');
	if(mysql_num_rows($commentrequired) > 0) {
		while($commentresult = mysql_fetch_array($commentrequired)) {
			$commenttabelid = $commentresult["CONTENT_COMMENT_ID"];
			$commentsequenceno = $commentresult["CONTENT_COMMENT_SEQ_NO"];
			$commentposter = $commentresult["USERNAME"];
			$comment = $commentresult["COMMENT_DATA"];
			echo '<tr>';
			echo '<td>';
			echo '<p>';
			echo ucfirst($commentposter);
			echo '&nbsp;';
			echo 'posted:';
			echo '</br>';
			echo $comment;
			echo '</p>';
			echo '</td>';
			echo '<td>';
			echo '<button type="submit" name="reply" value="';
			echo $commenttabelid;
     		echo '" class="stylish-link"/>Reply</button>';
			echo '</tr>';
			
			$replyquery = sprintf("SELECT * FROM (SELECT * FROM MT_CONTENT_COMMENT_REPLY WHERE
								   CONTENT_ID = '$id' AND CONTENT_COMMENT_ID = '$commenttabelid'
								   ORDER BY CONTENT_REPLY_TIME DESC)
								   AS LAST_REPLY ORDER BY CONTENT_REPLY_TIME");
			$reply = mysql_query($replyquery) or die('Failed to retrieve reply');
			if(mysql_num_rows($reply) > 0) {
				while($replyresult = mysql_fetch_array($reply)) {
					$replyposter = $replyresult["USERNAME"];
					$replydata = $replyresult["REPLY_DATA"];
					echo '<tr>';
					echo '<td>';
					echo '<table border="1" width=60% height="50">';
					echo '<tr>';
					echo '<td>';
					echo '<p>';
					echo ucfirst($replyposter);
					echo '&nbsp;';
					echo 'replied:';
					echo '</br>';
					echo $replydata;
					echo '</p>';
					echo '</td>';
					echo '</tr>';
					echo '</table>';
					echo '</td>';
					echo '</tr>';
				}
			}			
		}
	}
}

?>
</table>
	
<?php 
if(isset($_POST['postcomment'])) {
	
		$comment = $_POST['comment'];
		
		if($comment != NULL) {
			$commentsequencequery = sprintf("SELECT MAX(CONTENT_COMMENT_SEQ_NO) FROM MT_CONTENT_COMMENTS WHERE
									 		 CONTENT_ID = '$id'");
		
			$commentsequence = mysql_query($commentsequencequery) or die('Failed to check sequence number');
			$maxcommentsequence = mysql_fetch_row($commentsequence);
			if($maxcommentsequence[0] != NULL) {
				$sequencenumber = $maxcommentsequence[0] + 1;				
			} else {
				$sequencenumber = 1;
			}
			
			$commentindexquery = sprintf("SELECT CONTENT_INDEX FROM MT_CONTENT_COMMENTS WHERE
										  CONTENT_ID = '$id' ORDER BY CONTENT_COMMENT_TIME DESC LIMIT 1");
			
			$commentindex = mysql_query($commentindexquery) or die('Failed to check index number');
			$maxcommentindex = mysql_fetch_row($commentindex);
			if($maxcommentindex[0] == NULL) {
				$indexnumber = 1;
			} elseif($maxcommentindex[0] < 5) {
				$indexnumber = $maxcommentindex[0] + 1;
			} elseif($maxcommentindex[0] == 5) {
				$indexnumber = 1;
			}
			
			$addcommentquery = "INSERT INTO MT_CONTENT_COMMENTS(CONTENT_ID, CONTENT_COMMENT_SEQ_NO, CONTENT_COMMENT_TIME, USERNAME, COMMENT_DATA, CONTENT_INDEX)
							 	VALUES('$id', '$sequencenumber', NOW(), '$username', '$comment', '$indexnumber')";
			$addcomment = mysql_query($addcommentquery) or die("Failed to post");
			$commentid = mysql_insert_id();
			if(isset($commentid)) {
				print '<META HTTP-EQUIV="refresh" CONTENT="15">';
			}
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("Please add your comment")';
			echo '</script>';
		}
} 

if(isset($_POST['reply'])) {
	$reply = $_POST['comment'];
	$commentreplyid = $_POST['reply'];

	if($reply != NULL) {		
		$replysequencequery = sprintf("SELECT MAX(COMMENT_REPLY_SEQ_NO) FROM MT_CONTENT_COMMENT_REPLY WHERE
									   CONTENT_ID = '$id' AND
									   CONTENT_COMMENT_ID = '$commentreplyid'");

		$replysequence = mysql_query($replysequencequery) or die('Failed to check reply sequence number');
		$maxreplysequence = mysql_fetch_row($replysequence);
		if($maxreplysequence[0] != NULL) {
			$replysequencenumber = $maxreplysequence[0] + 1;
		} else {
			$replysequencenumber = 1;
		}


		$addcommentquery = "INSERT INTO MT_CONTENT_COMMENT_REPLY(CONTENT_ID, CONTENT_COMMENT_ID, COMMENT_REPLY_SEQ_NO, USERNAME, REPLY_DATA, CONTENT_REPLY_TIME)
							VALUES('$id', '$commentreplyid', '$replysequencenumber', '$username', '$reply', NOW())";
		$addcomment = mysql_query($addcommentquery) or die("Failed to post");
		$replyid = mysql_insert_id();

		if(isset($replyid)) {
			print '<META HTTP-EQUIV="refresh" CONTENT="15">';
		}
	} else {
		echo '<script type="text/javascript">';
		echo 'alert("Please add your reply")';
		echo '</script>';
	}		
}
?>
<table>
	<?php 
	$commentsequencequery = sprintf("SELECT MAX(CONTENT_COMMENT_SEQ_NO) FROM MT_CONTENT_COMMENTS WHERE
									 CONTENT_ID = '$id'");
	
	$commentsequence = mysql_query($commentsequencequery) or die('Failed to check sequence number');
	$maxcommentsequence = mysql_fetch_row($commentsequence);
	
	if($maxcommentsequence[0] != NULL) {
		$extra = $maxcommentsequence % 10; 
	}
	
	if($maxcommentsequence[0] != NULL && $maxcommentsequence[0] <= 10) {
		
	} elseif($maxcommentsequence[0] != NULL && $maxcommentsequence[0] > 10){
		$extra = $maxcommentsequence % 10;
		
		$numbersrequired = intval($maxcommentsequence[0] / 10);
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
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr></tr>
<tr><td> <textarea name="comment" rows="3" cols="40" id="comment"></textarea> </td></tr>
	<tr>		
		<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" name="postcomment" value="Comment" class="stylish-link"/>
				
		</td> 
	</tr>
</table>
</form>
</body>
</html>
