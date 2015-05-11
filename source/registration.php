<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Register</title>
<link rel="stylesheet" type="text/css"	href="/css/registrationStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
<script type="text/javascript" src="js/RegistrationValidator.js">
function setfocus(){
	document.getElementById('firstname').focus;
}
</script>
</head>

<?php 
echo '<div>';
include("./registerHeader.php");
echo '</div>';
?>

<body onload="setfocus()">

<?php
if(isset($_POST['signup'])) {
	
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	$username = $_POST['username'];
   	$password = md5($_POST['password']);
   	$confirmpassword = md5($_POST['confirmpassword']);
   	$day = $_POST['day'];
   	$month = $_POST['month'];
   	$year = $_POST['year'];
   	$gender = $_POST['gender'];
   	
   	if($firstname == null || $lastname == null || $email == null || $username == null || $password == null || $confirmpassword == null) {
		$error = TRUE;
	}else if($password != $confirmpassword) {
		$pwdmismatch = 1;
		$error = TRUE;
	} else if($day == 0) {
		$dayerror = 1;
		$error = TRUE;
	} else if($month == 0) {
		$montherror = 1;
		$error = TRUE;
	} else if($year == 0) {
		$yearerror = 1;
		$error = TRUE;
	} else if($gender == 0) {
		$gendererror = 1;
		$error = TRUE;
	} else {
		$error = FALSE;
	}
	
	if($error) {
	
	} else {
		$dbconnection = "connection.php";
		
		if(file_exists($dbconnection)) {
			include $dbconnection;
		} else if(file_exists("../".$dbconnection)) {
			include "../".$dbconnection;
		} else {
			include "../../".$dbconnection;
		}
			
		dbConnect();

        $stmt = mysqli_prepare($dbconnection, 'SELECT * FROM MT_USER WHERE EMAIL = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $emailCheck = mysqli_stmt_get_result($stmt) or die('Failed to check email');
        mysqli_stmt_close($stmt);

		if(mysqli_num_rows($emailCheck) == NULL) {
			$alreadyEmailExists = FALSE;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("Email id already exists")';
			echo '</script>';
			$alreadyEmailExists = TRUE;
		}

        $stmt = mysqli_prepare($dbconnection, 'SELECT * FROM MT_USER WHERE USERNAME = ?');
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $usernameCheck = mysqli_stmt_get_result($stmt) or die('Failed to check username');
        mysqli_stmt_close($stmt);

		if(mysqli_num_rows($usernameCheck) == NULL) {
			$alreadyUsernameExists = FALSE;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("Username already exists")';
			echo '</script>';
			$alreadyUsernameExists = TRUE;				
		}
		
		if(!$alreadyEmailExists && !$alreadyUsernameExists) {
			$dob = $year."-".$month."-".$day;
            $stmt = mysqli_prepare($dbconnection, 'INSERT INTO MT_USER VALUES(?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'ssssss', $username, $password, $firstname, $lastname, $email, $dob);
            mysqli_stmt_execute($stmt);
            $insert = mysqli_stmt_get_result($stmt) or die('Failed to add user');
            mysqli_stmt_close($stmt);
			
			if(!isset($insert)) {
				echo '<script type="text/javascript">';
				echo 'alert("Some error occured")';
				echo '</script>';	
			} else {
				echo "<center><h4>Registration Successful</h4></center>";
				$success = TRUE;
			}
		}				
	}	
	
}

if(isset($success)) {
	print '<meta http-equiv="refresh" content="0;url=./login.php?">';
}

?>

<div id="header">
<h1><span class="redtext">Create a new MeTube Account</span></h1>
</div>

<form name="registrationform" method="post" onsubmit="return registrationFields()">
<table align="right">
	<tr>
		<td> <label for="firstname"> <strong>First Name:*</strong> </label> </td>
		<td> <input type="text" size="40" maxlength="60" name="firstname" id="firstname"/></td>
	</tr>

	<tr>
		<td> <label for="lastname"> <strong>Last Name:*</strong> </label> </td>
		<td> <input type="text" size="40" maxlength="60" name="lastname" id="lastname"/></td>
	</tr>
	
	<tr>
		<td> <label for="email"> <strong>E-mail:*</strong> </label> </td> 
		<td> <input type="text" size="40" maxlength="60" name="email" id="email"/></td>
	</tr>
	
	<tr>
		<td> <label for="username"> <strong>Username:*</strong> </label> </td>
		<td> <input type="text" size="40" maxlength="60" name="username" id="username"/></td>
	</tr>
	
	<tr>
		<td><label for="password"> <strong>New Password:*</strong> </label> </td>
		<td> <input type="password" size="40" maxlength="16" name="password" id="password"/>
		     <?php if(isset($pwdmismatch)) echo "<br/> <font color=red>Passwords mismatch. Please re-enter </font>"; ?> </td>
	</tr>
	
	<tr>
		<td> <label for="confirmpassword"> <strong>Re-enter Password:*</strong> </label> </td>
		<td> <input type="password" size="40" maxlength="16" name="confirmpassword" id="confirmpassword"/> </td>
	</tr>

	<tr> 
		<td> <strong>Birthday:*</strong> </td> 
		<td> 
			<select name="day" id="day"> 
 				<option value="0">---</option> 
				<option value="1">1</option> 
				<option value="2">2</option> 
				<option value="3">3</option> 
				<option value="4">4</option> 
				<option value="5">5</option> 
				<option value="6">6</option> 
				<option value="7">7</option> 
				<option value="8">8</option> 
				<option value="9">9</option> 
				<option value="10">10</option> 
				<option value="11">11</option> 
				<option value="12">12</option> 
				<option value="13">13</option> 
				<option value="14">14</option> 
				<option value="15">15</option> 
				<option value="16">16</option> 
				<option value="17">17</option> 
				<option value="18">18</option> 
				<option value="19">19</option> 
				<option value="20">20</option> 
				<option value="21">21</option> 
				<option value="22">22</option> 
				<option value="23">23</option> 
				<option value="24">24</option> 
				<option value="25">25</option> 
				<option value="26">26</option> 
				<option value="27">27</option> 
				<option value="28">28</option> 
				<option value="29">29</option> 
				<option value="30">30</option> 
				<option value="31">31</option> 
			</select> 
 
			<select name="month" id="month"> 
 				<option value="0">---</option> 
				<option value="1" >January</option> 
				<option value="2" >February</option> 
				<option value="3" >March</option> 
				<option value="4" >April</option> 
				<option value="5" >May</option> 
				<option value="6" >June</option> 
				<option value="7" >July</option> 
				<option value="8" >August</option> 
				<option value="9" >September</option> 
				<option value="10" >October</option> 
				<option value="11" >November</option> 
				<option value="12" >December</option> 
			</select> 
 
 			<select name="year" id="year"> 
 				<option value="0">---</option> 
 				<option value="2012">2009</option>
 				<option value="2011">2009</option>
				<option value="2010">2009</option>
				<option value="2009">2009</option> 
				<option value="2008">2008</option> 
				<option value="2007">2007</option> 
				<option value="2006">2006</option> 
				<option value="2005">2005</option> 
				<option value="2004">2004</option> 
				<option value="2003">2003</option> 
				<option value="2002">2002</option> 
				<option value="2001">2001</option> 
				<option value="2000">2000</option> 
				<option value="1999">1999</option> 
				<option value="1998">1998</option> 
				<option value="1997">1997</option> 
				<option value="1996">1996</option> 
				<option value="1995">1995</option> 
				<option value="1994">1994</option> 
				<option value="1993">1993</option> 
				<option value="1992">1992</option> 
				<option value="1991">1991</option> 
				<option value="1990">1990</option> 
				<option value="1989">1989</option> 
				<option value="1988">1988</option> 
				<option value="1987">1987</option> 
				<option value="1986">1986</option> 
				<option value="1985">1985</option> 
				<option value="1984">1984</option> 
				<option value="1983">1983</option> 
				<option value="1982">1982</option> 
				<option value="1981">1981</option> 
				<option value="1980">1980</option> 
				<option value="1979">1979</option> 
				<option value="1978">1978</option> 
				<option value="1977">1977</option> 
				<option value="1976">1976</option> 
				<option value="1975">1975</option> 
				<option value="1974">1974</option> 
				<option value="1973">1973</option> 
				<option value="1972">1972</option> 
				<option value="1971">1971</option> 
				<option value="1970">1970</option> 
				<option value="1969">1969</option> 
				<option value="1968">1968</option> 
				<option value="1967">1967</option> 
				<option value="1966">1966</option> 
				<option value="1965">1965</option> 
				<option value="1964">1964</option> 
				<option value="1963">1963</option> 
				<option value="1962">1962</option> 
				<option value="1961">1961</option> 
				<option value="1960">1960</option> 
				<option value="1959">1959</option> 
				<option value="1958">1958</option> 
				<option value="1957">1957</option> 
				<option value="1956">1956</option> 
				<option value="1955">1955</option> 
				<option value="1954">1954</option> 
				<option value="1953">1953</option> 
				<option value="1952">1952</option> 
				<option value="1951">1951</option> 
				<option value="1950">1950</option> 
				<option value="1949">1949</option> 
				<option value="1948">1948</option> 
				<option value="1947">1947</option> 
				<option value="1946">1946</option> 
				<option value="1945">1945</option> 
				<option value="1944">1944</option> 
				<option value="1943">1943</option> 
				<option value="1942">1942</option> 
				<option value="1941">1941</option> 
				<option value="1940">1940</option> 
				<option value="1939">1939</option> 
				<option value="1938">1938</option> 
				<option value="1937">1937</option> 
				<option value="1936">1936</option> 
				<option value="1935">1935</option> 
				<option value="1934">1934</option> 
				<option value="1933">1933</option> 
				<option value="1932">1932</option> 
				<option value="1931">1931</option> 
				<option value="1930">1930</option> 
				<option value="1929">1929</option> 
				<option value="1928">1928</option> 
				<option value="1927">1927</option> 
				<option value="1926">1926</option> 
				<option value="1925">1925</option> 
				<option value="1924">1924</option> 
				<option value="1923">1923</option> 
				<option value="1922">1922</option> 
				<option value="1921">1921</option> 
				<option value="1920">1920</option> 
				<option value="1919">1919</option> 
				<option value="1918">1918</option> 
				<option value="1917">1917</option> 
				<option value="1916">1916</option> 
				<option value="1915">1915</option> 
				<option value="1914">1914</option> 
				<option value="1913">1913</option> 
				<option value="1912">1912</option> 
			</select> 
		</td>
		<?php 
		if(isset($dayerror)) { 
			echo '<script type="text/javascript">';
			echo 'alert("Please enter your date of birth")';
			echo '</script>';	
		}	
		?> 	
	</tr> 
	
	<tr> 
		<td> <strong>Gender:*</strong> </td> 
		<td> 
			<select name="gender" id="gender"> 
 				<option value="0">---</option> 
				<option value="1">Male</option> 
				<option value="2">Female</option>
			</select>
		</td>
		<?php 
		if(isset($gendererror)) { 
			echo '<script type="text/javascript">';
			echo 'alert("Please enter your gender")';
			echo '</script>';	
		}	
		?> 	
	</tr>
	
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	
	<tr> 
		<td> &nbsp; </td> 
		<td> <input type="submit" align="right" name="signup" value="Sign Up" class="stylish-link"/> </td> 
	</tr>
	
</table>
</form>

</body>
</html>