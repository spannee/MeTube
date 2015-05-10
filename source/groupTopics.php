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

if(isset($_SESSION['groupid'])) {
	$groupid = $_SESSION['groupid'];
} else {
	print "<meta http-equiv='refresh' content='0;url=index.php'>";
}


?>


<!DOCTYPE html PUBLIC -//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd>
<html xmlns=http://www.w3.org/1999/xhtml>

<head>
<meta http-equiv=Content-Type content=text/html charset=UTF-8 />
<title>Topic</title>
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
<form name="topics" method="get">
	<table align="center" border="1" width=80% height="100">
	<?php 
	
	if(isset($_GET['index'])) {
		$indexvalue = $_GET['index'];
		$startindex = ($indexvalue * 10) - 10;
        $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_TOPICS WHERE GROUP_ID = ? LIMIT ?,10 ");
        mysqli_stmt_bind_param($stmt, 'ii', $groupid, $startindex);
	} else {
        $stmt = mysqli_prepare($dbconnection, "SELECT COUNT(GROUP_ID) FROM MT_TOPICS WHERE GROUP_ID = ?");
        mysqli_stmt_bind_param($stmt, 'i', $groupid);
        mysqli_stmt_execute($stmt);
        $topiccount = mysqli_stmt_get_result($stmt) or die('Failed to check count');
        mysqli_stmt_close($stmt);

		$maxtopiccount = mysqli_fetch_row($topiccount);
		if($maxtopiccount[0] != 0) {
			$required = $maxtopiccount[0] % 10;
			
			if($required == 0) {
				$shown = 10;
			} else {
				$shown = $required;
			}
		} else {
			$shown = 10;
		}

        $stmt = mysqli_prepare($dbconnection, "SELECT * FROM (SELECT * FROM MT_TOPICS WHERE
 										       GROUP_ID = ? ORDER BY CREATED_DATE_TIME DESC
     									       LIMIT ?) AS LAST_TOPIC ORDER BY CREATED_DATE_TIME");
        mysqli_stmt_bind_param($stmt, 'ii', $groupid, $shown);
	} 
	
	if(isset($topicquery)) {
        mysqli_stmt_execute($stmt);
        $topic = mysqli_stmt_get_result($stmt) or die('Failed to retrieve topic');
        mysqli_stmt_close($stmt);

		if(mysqli_num_rows($topic) > 0) {
			while($topicresult = mysqli_fetch_array($topic)) {
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
				$topicid = $topicresult["TOPIC_ID"];
				$topictitle = $topicresult["TOPIC_TITLE"];
				$topicsequenceno = $topicresult["TOPIC_SEQ_NO"];
				$topicposter = $topicresult["USERNAME_WHO_CREATES"];
				$topictime = $topicresult["CREATED_DATE_TIME"];
				echo '<tr>';
				echo '<td>';
				echo "<a href='./discussions.php?topic_id=".$topicid."'>$topictitle</a>";
				echo '</td>';
				echo '<td>';
				echo "Created By:";
				echo $topicposter;
				echo "<br/>";
				echo "Created On:";
				echo $topictime;
				echo '</td>';
				echo '<td>';
				echo '<button type="submit" name="delete" value="';
				echo $topicid;
				echo '" class="stylish-link"/>Delete</button>';
				echo '</td>';
				echo '</tr>';
			}
		}
	}
	
	if(isset($_GET['createtopic'])) {
		$topictitle = $_GET['topictitle'];
		
		if($topictitle != NULL) {
            $stmt = mysqli_prepare($dbconnection, "SELECT MAX(TOPIC_SEQ_NO) FROM MT_TOPICS WHERE GROUP_ID = ?");
            mysqli_stmt_bind_param($stmt, 'i', $groupid);
            mysqli_stmt_execute($stmt);
            $topicsequence = mysqli_stmt_get_result($stmt) or die('Failed to check sequence number');
            mysqli_stmt_close($stmt);

            $maxtopicsequence = mysqli_fetch_row($topicsequence);
			if($maxtopicsequence[0] != NULL) {
				$sequencenumber = $maxtopicsequence[0] + 1;				
			} else {
				$sequencenumber = 1;
			}

            $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_TOPICS(GROUP_ID, TOPIC_TITLE, TOPIC_SEQ_NO,
                                                   USERNAME_WHO_CREATES, CREATED_DATE_TIME)
							                       VALUES(?, ?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, 'isis', $groupid, $topictitle, $sequencenumber, $username);
            mysqli_stmt_execute($stmt);
            $addtopic = mysqli_stmt_get_result($stmt) or die("Failed to post");
            $topicid = mysqli_insert_id($dbconnection);
            mysqli_stmt_close($stmt);
			
			if(isset($topicid)) {
				print '<meta http-equiv="refresh" content="0;url=./groupTopics.php?">';
			}
		}
	}
	
	if(isset($_GET['delete'])) {
		$topicid = $_GET['delete'];
        $stmt = mysqli_prepare($dbconnection, "SELECT GROUP_CREATED_BY FROM MT_GROUPS WHERE
						   	                   GROUP_ID IN(SELECT GROUP_ID FROM MT_TOPICS WHERE
						                       TOPIC_ID = ?)");
        mysqli_stmt_bind_param($stmt, 'i', $topicid);
        mysqli_stmt_execute($stmt);
        $findcreator = mysqli_stmt_get_result($stmt) or die('Failed to find creator');
        mysqli_stmt_close($stmt);
		$groupcreator = mysqli_fetch_row($findcreator);
		
		if($username == $groupcreator[0]) {
            $stmt = mysqli_prepare($dbconnection, "DELETE FROM MT_TOPICS WHERE TOPIC_ID = ?");
            mysqli_stmt_bind_param($stmt, 'i', $topicid);
            mysqli_stmt_execute($stmt);
            $deletetopic = mysqli_stmt_get_result($stmt) or die('Failed to delete topic');
            $deletetopicid = mysqli_insert_id($dbconnection);
            mysqli_stmt_close($stmt);

			if(isset($deletetopicid)) {
				print '<meta http-equiv="refresh" content="0;url=./groupTopics.php?">';
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
    $stmt = mysqli_prepare($dbconnection, "SELECT MAX(TOPIC_SEQ_NO) FROM MT_TOPICS WHERE GROUP_ID = ?");
    mysqli_stmt_bind_param($stmt, 'i', $groupid);
    mysqli_stmt_execute($stmt);
    $topicsequence = mysqli_stmt_get_result($stmt) or die('Failed to check sequence number');
    mysqli_stmt_close($stmt);

	$maxtopicsequence = mysqli_fetch_row($topicsequence);
	
	if($maxtopicsequence[0] != NULL) {
		$extra = $maxtopicsequence % 10; 
	}
	
	if($maxtopicsequence[0] != NULL && $maxtopicsequence[0] <= 10) {
		
	} elseif($maxtopicsequence[0] != NULL && $maxtopicsequence[0] > 10){
		$extra = $maxtopicsequence % 10;
		
		$numbersrequired = intval($maxtopicsequence[0] / 10);
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
	<tr><td> <textarea name="topictitle" rows="6" cols="80" id="topictitle"></textarea> </td></tr>
	<tr>
		
		<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" name="createtopic" value="Create Topic" class="stylish-link"/>
		</td> 
	</tr>
	</table>
	</div>
</form>
 </body>
 </html>