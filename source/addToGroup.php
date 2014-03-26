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
<title>Remove From Group</title>
<style type="text/css">
	.center {
    	position:fixed;
        top:40%;
        left:35%; 
    }
   
   .stylish-button {
    -webkit-box-shadow:rgba(0,0,0,0.2) 0 1px 0 0;
    -moz-box-shadow:rgba(0,0,0,0.2) 0 1px 0 0;
    box-shadow:rgba(0,0,0,0.2) 0 1px 0 0;
    color:#4C4646;
    background-color:none;
    border-radius:5px;
    -moz-border-radius:5px;
    -webkit-border-radius:5px;
    border:none;
    font-family:'Helvetica Neue',Arial,sans-serif;
    font-size:16px;
    font-weight:700;
    height:32px;
    padding:4px 16px;
    text-shadow:#FF0000 0 1px 0
}

.stylish-link {
    -webkit-box-shadow:rgba(0,0,0,0.2) 0 1px 0 0;
    -moz-box-shadow:rgba(0,0,0,0.2) 0 1px 0 0;
    box-shadow:rgba(0,0,0,0.2) 0 1px 0 0;
    color:#4C4646;
    background-color:none;
    border-radius:5px;
    -moz-border-radius:5px;
    -webkit-border-radius:5px;
    border:none;
    font-family:'Helvetica Neue',Arial,sans-serif;
    font-size:16px;
    font-weight:700;
    height:32px;
}

.inputtext { 
	width: 600px; height: 20px; 
}


</style>
</head>

<body>

<div>
<?php include("./loginHeader.php"); ?>
</div>

<form name="searchFriendForm" method="post">
<br/>
<div class="center">
<table>
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

<?php 

	$friendsquery = sprintf("SELECT USER_CONTACT_ID FROM
							 MT_USER_CONTACTS WHERE
							 USERNAME = '$username' AND
							 IS_FRIEND = 'Y' AND USER_CONTACT_ID NOT IN
							 (SELECT USERNAME FROM MT_GROUP_MEMBERS WHERE
							 GROUP_ID = '$groupid')");
	$friends = mysql_query($friendsquery) or die('Failed to load friends');
	echo "<tr><td><select name='friends' id='friends' style='width: 300px;'>";
	echo "<option value='selectfriend'>Select Friend</option>";
	if((mysql_num_rows($friends)) > 0) {
		while($friendsresult = mysql_fetch_array($friends)) {
			$groupmember = $friendsresult["USER_CONTACT_ID"];
			echo "<option value='$groupmember'>$groupmember</option>";
		}
	}
	echo "</select>";
	echo "<td><input type='submit' name='addtogroup' id='addtogroup' value='AddToGroup' style='background:none;border:0;color:#4C4646;font-size: 18px;' class='stylish-link'/>";
	echo "<input type='submit' name='donotremove' id='donotremove' value='GoToGroup' style='background:none;border:0;color:#4C4646;font-size: 18px;' class='stylish-link'/></td></tr>";

	if(isset($_POST['addtogroup'])) {
		$membername = $_POST['friends'];
		
		if($membername == 'selectfriend') {
			echo '<script type="text/javascript">';
			echo 'alert("Please select a person")';
			echo '</script>';
		} else {
			$addfriendsquery = "INSERT INTO MT_GROUP_MEMBERS(GROUP_ID, USERNAME)
								VALUES('$groupid', '$membername')";
			$addfriends = mysql_query($addfriendsquery) or die("Failed to add friends");
			$insertid = mysql_insert_id();
			
			if(isset($insertid)) {
				echo '<script type="text/javascript">';
				echo 'alert("Added to group")';
				echo '</script>';
			}
		}

	} elseif(isset($_POST['donotremove'])) {
		print '<meta http-equiv="refresh" content="0;url=./groupTopics.php?">';
	}
?>


</table>
</div>
</form>