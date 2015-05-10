<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Friend Requests</title>
<link rel="stylesheet" type="text/css" href="/css/searchStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
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
<table align="center">
	
	<?php 
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

    $stmt = mysqli_prepare($dbconnection, "SELECT USER_CONTACT_ID FROM MT_USER_CONTACTS WHERE USERNAME = ? AND IS_FRIEND = 'A'");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $userscheck = mysqli_stmt_get_result($stmt) or die('Failed to check friends');
    mysqli_stmt_close($stmt);

	if((mysqli_num_rows($userscheck)) > 0) {
		$user = array();
		while($usercheckresults = mysqli_fetch_array($userscheck)) {
			$users[] = $usercheckresults["USER_CONTACT_ID"];
		}
		
		$index = 1;
		foreach ($users as $user) {
			echo "<tr><td><a href='./othersProfile.php?content_owner=".$user."' class='stylish-link'><font size='4'>$index.$user</font></a></td></tr>";
			$index++;
		}		
	} else {
		echo "<center><h4>No Requests found</h4></center>";
	}
	
	?>
</table>
</form>