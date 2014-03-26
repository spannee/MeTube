<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Profile Update</title>
<link rel="stylesheet" type="text/css"	href="/css/registrationStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />

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
if(isset($_POST['firstnamebutton'])) {	
	$firstname = $_POST['firstname'];

   	if($firstname == null) {
		$error = TRUE;
   	}
	
	if($error) {
	
	} else {			
		$updatefirstnamequery = "UPDATE MT_USER SET 
						     	 USER_FIRST_NAME = '$firstname'
						    	 WHERE USERNAME = '$username'";
		$updatefirstname = mysql_query($updatefirstnamequery) or die("Failed to update first name");
		
		if(!isset($updatefirstname)) {
			echo '<script type="text/javascript">';
			echo 'alert("Some error occured")';
			echo '</script>';	
		} else {
			$success = TRUE;
		}						
	}		
} elseif(isset($_POST['lastnamebutton'])) {	
	$lastname = $_POST['lastname'];

   	if($lastname == null) {
		$error = TRUE;
   	}
	
	if($error) {
	
	} else {			
		$updatelastnamequery = "UPDATE MT_USER SET 
						     	USER_LAST_NAME = '$lastname'
						    	WHERE USERNAME = '$username'";
		$updatelastname = mysql_query($updatelastnamequery) or die("Failed to update last name");
		
		if(!isset($updatelastname)) {
			echo '<script type="text/javascript">';
			echo 'alert("Some error occured")';
			echo '</script>';	
		} else {
			$success = TRUE;
		}						
	}		
} elseif(isset($_POST['emailbutton'])) {	
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
} elseif(isset($_POST['passwordbutton'])) {	
	$password = $_POST['password'];
	$confirmpassword = $_POST['confirmpassword'];
	
	if($password == null) {
		$pwderror = TRUE;
   	} elseif($confirmpassword == null) {
		$pwderror = TRUE;
   	} elseif($password != $confirmpassword) {
		$pwdmismatch = 1;
		$pwderror = TRUE;
	}
	
	$password = md5($_POST['password']);
	$confirmpassword = md5($_POST['confirmpassword']);
	
	if($pwderror) {
	
	} else {			
		$updatepwdquery = "UPDATE MT_USER SET 
						   PASSWORD = '$password'
						   WHERE USERNAME = '$username'";
		$updatepwd = mysql_query($updatepwdquery) or die("Failed to update password");
		
		if(!isset($updatepwd)) {
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
} elseif(isset($error)) {
	echo '<script type="text/javascript">';
	echo 'alert("Please enter a value")';
	echo '</script>';
} elseif(isset($emailerror)) {
	echo '<script type="text/javascript">';
	echo 'alert("Email id already exists")';
	echo '</script>';
} elseif($pwderror)  {
	echo '<script type="text/javascript">';
	echo 'alert("Cannot update")';
	echo '</script>';
}

?>

<div id="header">
<h1><span class="redtext">Update Your Profile</span></h1>
</div>

<form name="registrationform" method="post" onsubmit="return registrationFields()">
<table align="center">
	<tr>
		<td> <label for="firstname"> <strong>First Name:</strong> </label> </td>
		<td> <input type="text" size="40" maxlength="60" name="firstname" id="firstname"/></td>
		<td> <input type="submit" name="firstnamebutton" value="Update FirstName" class="stylish-link"/> </td>
	</tr>
	
	<tr></tr>
	<tr></tr>
	<tr></tr>
	
	<tr>
		<td> <label for="lastname"> <strong>Last Name:</strong> </label> </td>
		<td> <input type="text" size="40" maxlength="60" name="lastname" id="lastname"/></td>
		<td> <input type="submit" name="lastnamebutton" value="Update LastName" class="stylish-link"/> </td>
	</tr>
	
	<tr></tr>
	<tr></tr>
	<tr></tr>
	
	<tr>
		<td><label for="password"> <strong>New Password:</strong> </label> </td>
		<td> <input type="password" size="40" maxlength="16" name="password" id="password"/>
		     <?php if(isset($pwdmismatch)) echo "<br/> <font color=red>Passwords mismatch. Please re-enter </font>"; ?> </td>
	</tr>
	
	<tr>
		<td> <label for="confirmpassword"> <strong>Re-enter Password:</strong> </label> </td>
		<td> <input type="password" size="40" maxlength="16" name="confirmpassword" id="confirmpassword"/> </td>
		<td> <input type="submit" name="passwordbutton" value="Update Password" class="stylish-link"/> </td>
	</tr>

</table>
</form>

</body>
</html>