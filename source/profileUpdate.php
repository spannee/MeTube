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
        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER SET USER_FIRST_NAME = ? WHERE USERNAME = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $firstname, $username);
        mysqli_stmt_execute($stmt);
        $updatefirstname = mysqli_stmt_get_result($stmt) or die("Failed to update first name");
        mysqli_stmt_close($stmt);
		
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
        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER SET USER_LAST_NAME = ? WHERE USERNAME = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $lastname, $username);
        mysqli_stmt_execute($stmt);
        $updatelastname = mysqli_stmt_get_result($stmt) or die("Failed to update last name");
        mysqli_stmt_close($stmt);
		
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

    $stmt = mysqli_prepare($dbconnection, "SELECT * FROM MT_USER WHERE EMAIL = ? AND USERNAME != ?");
    mysqli_stmt_bind_param($stmt, 'ss', $email, $username);
    mysqli_stmt_execute($stmt);
    $emailCheck = mysqli_stmt_get_result($stmt) or die("Failed to check email");
    mysqli_stmt_close($stmt);
	
	if(mysqli_num_rows($emailCheck) > 0) {
		$emailerror = TRUE;
	} else {
		$emailerror = FALSE;
	}
	
   	if($email == null) {
		$error = TRUE;
   	}
	
	if($error || $emailerror) {
	
	} else {
        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER SET EMAIL = ? WHERE USERNAME = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $email, $username);
        mysqli_stmt_execute($stmt);
        $updateemail = mysqli_stmt_get_result($stmt) or die("Failed to update email");
        mysqli_stmt_close($stmt);
		
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
        $stmt = mysqli_prepare($dbconnection, "UPDATE MT_USER SET PASSWORD = ? WHERE USERNAME = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $password, $username);
        mysqli_stmt_execute($stmt);
        $updatepwd = mysqli_stmt_get_result($stmt) or die("Failed to update password");
        mysqli_stmt_close($stmt);
		
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