<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Block</title>
<link rel="stylesheet" type="text/css" href="/css/searchStyle.css" />
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
	echo '<div>';
	include("./loginHeader.php");
	echo '</div>';
} else {
	print "<meta http-equiv='refresh' content='0;url=index.php'>";
}
?>
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
    $stmt = mysqli_prepare($dbconnection, "SELECT USER_CONTACT_ID FROM MT_USER_CONTACTS WHERE
                                           USERNAME = ? AND
                                           IS_FRIEND = 'N' AND
                                           IS_BLOCKED = 'Y'");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $blocked = mysqli_stmt_get_result($stmt) or die('Failed to load blocked users');
    mysqli_stmt_close($stmt);

	echo "<select name='blocked' id='blocked' style='width: 300px;'>";
	echo "<option value='selectuser'>Select User</option>";
	if((mysqli_num_rows($blocked)) > 0) {
		while($blockedresult = mysqli_fetch_array($blocked)) {
			$usercontactid = $blockedresult["USER_CONTACT_ID"];
			echo "<option value='$usercontactid'>$usercontactid</option>";
		}
	}
	echo "</select>";
	echo "<input type='submit' name='unblock' value='Unblock' style='background:none;border:0;color:#4C4646;font-size: 18px;'/>";	
	
	if(isset($_POST['unblock'])) {
		$blockedusername = $_POST["blocked"];
		
		if($blockedusername == 'selectuser') {
			echo '<script type="text/javascript">';
			echo 'alert("Please select a user to unblock")';
			echo '</script>';
		} else {
            $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER_CONTACTS
                                                   SET IS_FRIEND = '' AND
                                                   IS_BLOCKED = 'N' WHERE
                                                   USERNAME = ? AND
                                                   USER_CONTACT_ID = ?");
            mysqli_stmt_bind_param($stmt, 'ss', $username, $blockedusername);
            mysqli_stmt_execute($stmt);
            $blockedlists = mysqli_stmt_get_result($stmt) or die('Failed to update blocked list');
            $blockedfromid = mysqli_insert_id($dbconnection);
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER_CONTACTS
                                                   SET IS_FRIEND = '' AND
                                                   IS_BLOCKED = 'N' WHERE
                                                   USERNAME = ? AND
                                                   USER_CONTACT_ID = ?");
            mysqli_stmt_bind_param($stmt, 'ss', $blockedusername, $username);
            mysqli_stmt_execute($stmt);
            $blockedlists = mysqli_stmt_get_result($stmt) or die('Failed to update blocked list');
            $blockedtoid = mysqli_insert_id($dbconnection);
            mysqli_stmt_close($stmt);
			
			if(isset($blockedfromid) && isset($blockedtoid)) {
				echo '<script type="text/javascript">';
				echo 'alert("User has been unlocked")';
				echo '</script>';
			}
			
		}
	}
	?>
</table>
</div>
</form>