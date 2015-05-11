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


?>

<!DOCTYPE html PUBLIC -//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd>
<html xmlns=http://www.w3.org/1999/xhtml>

<head>
<meta http-equiv=Content-Type content=text/html charset=UTF-8 />
<title>Messages</title>
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
<style type="text/css">
	.center {
    	position:fixed;
        top:40%;
        left:0%; 
    }
    .bottom {
    	position:fixed;
        top:75%;
        left:35%; 
    }
    .display {
    	position:fixed;
        top:30%;
        left:45%; 
    }
</style>
</head>

<body>
<div>
<?php include("./loginHeader.php"); ?>
</div>

<form name="messages" method="post">
<br/>
<div class="center">
<table>

<?php 
	if(!isset($_SESSION['friendselected'])) {
        $stmt = mysqli_prepare($dbconnection, "SELECT FROM_USERNAME FROM MT_USER_MESSAGES WHERE
								   	           TO_USERNAME = ? AND IS_MESSAGE_VIEWED = 'N' LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $unreadmessages = mysqli_stmt_get_result($stmt) or die('Failed to check unread messages');
        mysqli_stmt_close($stmt);

		if(mysqli_num_rows($unreadmessages) > 0) {
			$unreadmessage = mysqli_fetch_row($unreadmessages);
			$_SESSION['friendselected'] = $unreadmessage[0];
		}
	}

    $stmt = mysqli_prepare($dbconnection, "SELECT USER_CONTACT_ID FROM MT_USER_CONTACTS WHERE
							               USERNAME = ? AND IS_FRIEND = 'Y'");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $friends = mysqli_stmt_get_result($stmt) or die('Failed to load friends');
    mysqli_stmt_close($stmt);

	echo "<tr><td><select name='friends' id='friends' style='width: 200px;'>";
	echo "<option value='selectfriend'>Select Friend</option>";
	if((mysqli_num_rows($friends)) > 0) {
		while($friendsresult = mysqli_fetch_array($friends)) {
			$friend = $friendsresult["USER_CONTACT_ID"];
			echo "<option value='$friend'>$friend</option>";
		}
	}
	echo "</select>";
	echo "<td><input type='submit' name='selectone' id='selectone' value='Select Friend' style='background:none;border:0;color:#4C4646;font-size: 18px;'/>";
	

	if(isset($_POST['selectone'])) {
		$selectedfriend = $_POST['friends'];
		
		if($selectedfriend != 'selectfriend') {
			$_SESSION['friendselected'] = $_POST['friends'];
		}
	} 
	
	if(isset($_POST['postreply'])) {

		if(isset($_SESSION['friendselected'])) {
			$chatfriend = $_SESSION['friendselected'];
			
			$messageitem = $_POST['messageitem'];
			
			if($messageitem != NULL) {
                $stmt = mysqli_prepare($dbconnection, "SELECT MAX(MESSAGE_SEQ_NO) FROM MT_USER_MESSAGES WHERE
												       FROM_USERNAME = ? AND TO_USERNAME = ?
												       OR FROM_USERNAME = ? AND TO_USERNAME = ?");
                mysqli_stmt_bind_param($stmt, 'ssss', $username, $chatfriend, $chatfriend, $username);
                mysqli_stmt_execute($stmt);
                $messagesequence = mysqli_stmt_get_result($stmt) or die('Failed to check sequence number');
                mysqli_stmt_close($stmt);

				$maxmessagesequence = mysqli_fetch_row($messagesequence);
				if($maxmessagesequence[0] != NULL) {
					$sequencenumber = $maxmessagesequence[0] + 1;
				} else {
					$sequencenumber = 1;
				}

                $stmt = mysqli_prepare($dbconnection, "INSERT INTO MT_USER_MESSAGES(FROM_USERNAME, TO_USERNAME,
                                                       MESSAGE_CONTENT, IS_MESSAGE_VIEWED, MESSAGE_SEQ_NO, MESSAGE_TIMESTAMP)
								                       VALUES(?, ?, ?, 'N', ?, NOW())");
                mysqli_stmt_bind_param($stmt, 'sssi', $username, $chatfriend, $messageitem, $sequencenumber);
                mysqli_stmt_execute($stmt);
                $addmessage = mysqli_stmt_get_result($stmt) or die("Failed to post");
                $messageid = mysqli_insert_id($dbconnection);
                mysqli_stmt_close($stmt);

				if(isset($messageid)) {
					print '<meta http-equiv="refresh" content="0;url=./messages.php?">';
				}
			} else {

			}			
		}		
	}
?>
</table>
</div>

<?php 
	if(isset($_SESSION['friendselected'])) {
		$chatfriend = $_SESSION['friendselected'];
		
		if(isset($_POST['index'])) {
			$indexvalue = $_POST['index'];
			$startindex = ($indexvalue * 5) - 5;
            $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_USER_MESSAGES WHERE
				  					               FROM_USERNAME = ? AND TO_USERNAME = ?
									               OR FROM_USERNAME = ? AND TO_USERNAME = ? LIMIT ?,5");
            mysqli_stmt_bind_param($stmt, 'ssssi', $username, $chatfriend, $chatfriend, $username, $startindex);
		} else {
            $stmt = mysqli_prepare($dbconnection, "SELECT COUNT(MESSAGE_ID) FROM MT_USER_MESSAGES WHERE
								                   FROM_USERNAME = ? AND TO_USERNAME = ?
										           OR FROM_USERNAME = ? AND TO_USERNAME = ?");
            mysqli_stmt_bind_param($stmt, 'ssss', $username, $chatfriend, $chatfriend, $username);
            mysqli_stmt_execute($stmt);
            $messagecount = mysqli_stmt_get_result($stmt) or die('Failed to check count');
            mysqli_stmt_close($stmt);

			$maxmessagecount = mysqli_fetch_row($messagecount);
			if($maxmessagecount[0] != 0) {
				$required = $maxmessagecount[0] % 5;
					
				if($required == 0) {
					$shown = 5;
				} else {
					$shown = $required;
				}
			} else {
				$shown = 5;
			}

            $stmt = mysqli_prepare($dbconnection, "SELECT * FROM (SELECT * FROM MT_USER_MESSAGES WHERE
									               FROM_USERNAME = ? AND TO_USERNAME = ?
									               OR FROM_USERNAME = ? AND TO_USERNAME = ?
                                                   ORDER BY MESSAGE_TIMESTAMP DESC LIMIT ?)
									               AS LAST_MESSAGE ORDER BY MESSAGE_TIMESTAMP");
            mysqli_stmt_bind_param($stmt, 'ssssi', $username, $chatfriend, $chatfriend, $username, $shown);
		}
		
		if(isset($stmt)) {
            mysqli_stmt_execute($stmt);
            $message = mysqli_stmt_get_result($stmt) or die('Failed to read message');
            mysqli_stmt_close($stmt);
			if(mysqli_num_rows($message) > 0) {
				$messageids = array();
				$messagereceiver = array();
				echo '<div class="display">';
				echo '<table border="1">';
				while($messageresult = mysqli_fetch_array($message)) {
					$messageids[] = $messageresult["MESSAGE_ID"];
					$messagereceiver[] = $messageresult["TO_USERNAME"];
					$messagecontent = $messageresult["MESSAGE_CONTENT"];
					$messagesequenceno = $messageresult["MESSAGE_SEQ_NO"];
					$fromuser = $messageresult["FROM_USERNAME"];
					$senttime = $messageresult["MESSAGE_TIMESTAMP"];
					$id= $messageresult["MESSAGE_ID"];
					echo '<tr>';
					echo '<td>';
					echo $messagecontent;
					echo '</td>';
					echo '<td>';
					echo $fromuser;
					echo "<br/>";
					echo $senttime;
					echo '</td>';
					echo '<td>';
					echo '<button type="submit" name="delete" value="';
					echo $id;
					echo '" class="stylish-link"/>Delete</button>';
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
				echo '</div>';
			}
			if(isset($messageids)) {
				for($i=0;$i<count($messageids);$i++) {
					if($messagereceiver[$i] == $username) {
                        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER_MESSAGES
                                                               SET IS_MESSAGE_VIEWED = 'Y' WHERE
								  			                   MESSAGE_ID = ");
                        mysqli_stmt_bind_param($stmt, 'i', $messageids[$i]);
                        mysqli_stmt_execute($stmt);
                        $updateview = mysqli_stmt_get_result($stmt) or die("Failed to update view");
                        mysqli_stmt_close($stmt);
					}
				}
			}
		}
		
		if(isset($_POST['delete'])) {
			$id = $_POST['delete'];
            $stmt = mysqli_prepare($dbconnection, "SELECT FROM_USERNAME FROM MT_USER_MESSAGES WHERE MESSAGE_ID = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $findcreator = mysqli_stmt_get_result($stmt) or die("Failed to find creator");
            mysqli_stmt_close($stmt);
		
			$messagecreator = mysqli_fetch_row($findcreator);
			
			if($username == $messagecreator[0]) {
                $stmt = mysqli_prepare($dbconnection, "DELETE FROM MT_USER_MESSAGES WHERE MESSAGE_ID = ?");
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                $deletemessage = mysqli_stmt_get_result($stmt) or die("Failed to delete message");
                $deletemessageid = mysqli_insert_id($dbconnection);
                mysqli_stmt_close($stmt);

				if(isset($deletemessageid)) {
					print '<meta http-equiv="refresh" content="0;url=./messages.php?">';
				}
			} else {
				echo '<script type="text/javascript">';
				echo 'alert("You dont have Permission")';
				echo '</script>';
			}	
		
		}
		
	}
?>	
<div class="bottom">
	<table>
	<?php 
	if(isset($_SESSION['friendselected'])) {
		$chatfriend = $_SESSION['friendselected'];
        $stmt = mysqli_prepare($dbconnection, "SELECT MAX(MESSAGE_SEQ_NO) FROM MT_USER_MESSAGES WHERE
									 	       FROM_USERNAME = ? AND TO_USERNAME = ?
									 	       OR FROM_USERNAME = ? AND TO_USERNAME = ?");
        mysqli_stmt_bind_param($stmt, 'ssss', $username, $chatfriend, $chatfriend, $username);
        mysqli_stmt_execute($stmt);
        $messagesequence = mysqli_stmt_get_result($stmt) or die('Failed to check sequence number');
        mysqli_stmt_close($stmt);
		$maxmessagesequence = mysqli_fetch_row($messagesequence);
		
		if($maxmessagesequence[0] != NULL) {
			$extra = $maxmessagesequence % 5; 
		}
		
		if($maxmessagesequence[0] != NULL && $maxmessagesequence[0] <= 5) {
			
		} elseif($maxmessagesequence[0] != NULL && $maxmessagesequence[0] > 5){
			$extra = $maxmessagesequence % 5;
		
			$numbersrequired = intval($maxmessagesequence[0] / 5);
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
	}
		?>
	<tr><td> <textarea name="messageitem" rows="3" cols="40" id="messageitem"></textarea> </td></tr>
	<tr>
		<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" name="postreply" value="Send" class="stylish-link"/>
		</td> 
	</tr>
	</table>
</div>
</form>