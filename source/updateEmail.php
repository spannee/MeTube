<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Email Update</title>
<link rel="stylesheet" type="text/css"	href="/css/registrationStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
<script type="text/javascript" src="js/UpdateValidator.js">

</script>
</head>
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


<body onload="setfocus()">

<?php
if(isset($_POST['emailbutton'])) {
	$email = $_POST['email'];

	$checkUserEmailExists = sprintf("SELECT * FROM MT_USER WHERE EMAIL = '$email' AND USERNAME != '$username'");
	$emailCheck = mysql_query($checkUserEmailExists);

	if(mysql_num_rows($emailCheck) > 0) {
		$emailerror = TRUE;
	} else {
		$emailerror = FALSE;
	}

	if($email == null) {
		$error = TRUE;
	}

	if($error || $emailerror) {

	} else {
		$updateemailnamequery = "UPDATE MT_USER SET
							     EMAIL = '$email'
								 WHERE USERNAME = '$username'";
		$updateemail = mysql_query($updateemailnamequery) or die("Failed to update email");

		if(!isset($updateemail)) {
			echo '<script type="text/javascript">';
			echo 'alert("Some error occured")';
			echo '</script>';
		} else {
			$success = TRUE;
		}
	}
}

if(isset($success)) {
	echo '<script type="text/javascript">';
	echo 'alert("Updated")';
	echo '</script>';
} elseif(isset($emailerror)) {
	echo '<script type="text/javascript">';
	echo 'alert("Email id already exists")';
	echo '</script>';
}

?>

<div id="header">
<h1><span class="redtext">Update Your Email</span></h1>
</div>

<form name="registrationform" method="post" onsubmit="return registrationFields()">
<table align="center">
	
	<tr>
		<td> <label for="email"> <strong>E-mail:</strong> </label> </td> 
		<td> <input type="text" size="40" maxlength="60" name="email" id="email"/></td>
		<td> <input type="submit" name="emailbutton" value="Update Email" class="stylish-link"/> </td>
	</tr>

</table>
</form>

</body>
</html>