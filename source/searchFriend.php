<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Search Friend</title>
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
	<tr>
		<td> <input type="text" size="60" class="inputtext" maxlength="300" name="emailvalue" id="emailvalue"/></td>
		<td> <input type="submit" name="searchfriendbutton" value="Search Users by Email"/> </td>		
	</tr>	
	
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
	
	if(isset($_POST['searchfriendbutton'])) {
		$email = $_POST['emailvalue'];

        $stmt = mysqli_prepare($dbconnection, "SELECT USERNAME FROM MT_USER WHERE
                                               EMAIL = ? AND USERNAME != ?");
        mysqli_stmt_bind_param($stmt, 'ss', $email, $username);
        mysqli_stmt_execute($stmt);
        $userssearch = mysqli_stmt_get_result($stmt) or die('Failed to search friends');
        mysqli_stmt_close($stmt);
	
		if((mysqli_num_rows($userssearch)) == 1) {
			while($userssearchresults = mysqli_fetch_array($userssearch)) {
				$user = $userssearchresults["USERNAME"];
			}
            $stmt = mysqli_prepare($dbconnection, "SELECT IS_FRIEND FROM MT_USER_CONTACTS WHERE
										           USERNAME = ? AND USER_CONTACT_ID = ?");
            mysqli_stmt_bind_param($stmt, 'ss', $username, $user);
            mysqli_stmt_execute($stmt);
            $userscheck = mysqli_stmt_get_result($stmt) or die('Failed to check friends');
            mysqli_stmt_close($stmt);

			$isfriend = mysqli_fetch_row($userscheck);
						
			if($isfriend[0] == '0' || $isfriend[0] != 'N' || $isfriend[0] != NULL) {
				while($usercheckresults = mysqli_fetch_array($userscheck)) {
					$user = $userssearchresults["USER_CONTACT_ID"];
				}
				$_SESSION['contentownersearch'] = $user;
				echo "<tr><td><a href='./othersProfile.php?content_owner=".$user."' class='stylish-link'><font size='4'>$user</font></a></td></tr>";
			}
			else {
				echo '<script type="text/javascript">';
				echo 'alert("No users found")';
				echo '</script>';
			}
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No users found")';
			echo '</script>';
		}
	}
	
	?>
</table>
</form>