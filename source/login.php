<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Login</title>
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
<script type="text/javascript" src="js/LoginValidator.js">
</script>
</head>
<?php 
echo '<div>';
include("./registerHeader.php");
echo '</div>';
?>
<body>
<?php 

if(isset($_POST["signin"])) {
	
	$dbconnection = "connection.php";
	
	if(file_exists($dbconnection)) {
		include $dbconnection;
	} else if(file_exists("../".$dbconnection)) {
		include "../".$dbconnection;
	} else {
		include "../../".$dbconnection;
	}

    dbConnect();

	$username=$_POST["username"];
	$password=md5($_POST['password']);

    $stmt = mysqli_prepare($dbconnection, 'SELECT * FROM MT_USER WHERE USERNAME = ? AND PASSWORD = ?');
    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
    mysqli_stmt_execute($stmt);
    $loginuser = mysqli_stmt_get_result($stmt) or die('Failed to login');
    mysqli_stmt_close($stmt);
	
	if((mysqli_num_rows($loginuser)) == 1) {
		$userdetails = mysqli_fetch_row($loginuser);
		session_start();
		$_SESSION['username'] = $userdetails[0];
		$loginsucess = TRUE;
	} else	{
		$loginsucess = FALSE;
		echo '<script type="text/javascript">';
		echo 'alert("The username or the password is wrong")';
		echo '</script>';
	}
	
	if($loginsucess) {
		echo '<meta http-equiv="refresh" content="0;url=./index.php?">';
	}
}
?>

<form name="loginform" method="post" onsubmit="return loginFields()">
<br/>

<br/>
<br/>
<br/>
<br/>
<br/>

<table align="right">
	<tr>
		<td> <label for="username"> <strong>Username:*</strong> </label> </td>
		<td> <input type="text" size="40" maxlength="60" name="username" id="username"/></td>
	</tr>

	<tr>
		<td><label for="password"> <strong>New Password:*</strong> </label> </td>
		<td> <input type="password" size="40" maxlength="16" name="password" id="password"/>
	</tr>
	
	<tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
	
	<tr> 
		<td> &nbsp; </td> 
		<td> &nbsp; &nbsp; &nbsp;&nbsp;<input type="submit" align="right" name="signin" value="Sign in" class="stylish-link"/>&nbsp;&nbsp;&nbsp;  
		      </td>
	</tr>
	
	</table>
</form>

</body>

</html>