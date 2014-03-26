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
<title>CreateGroup</title>
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
<div>
<?php include("./loginHeader.php"); ?>
</div>

<?php 
if(isset($_POST['creategroup'])) {
	$groupname = $_POST['groupname'];
	
	if($groupname != NULL) {
		$friendsquery = sprintf("SELECT USER_CONTACT_ID FROM MT_USER_CONTACTS WHERE
								 USERNAME = '$username' AND
								 IS_FRIEND = 'Y'");
		$friends = mysql_query($friendsquery) or die('Failed to load friends');
		
		if((mysql_num_rows($friends)) > 0) {
			$addgroupquery = "INSERT INTO MT_GROUPS(GROUP_CREATED_BY, GROUP_NAME, CREATED_DATE_TIMESTAMP)
							  VALUES('$username', '$groupname', NOW())";
			$addgroup = mysql_query($addgroupquery) or die("Failed to add group");
			$groupid = mysql_insert_id();
			
			if(isset($groupid)) {
				$_SESSION['groupid'] = $groupid;
				while($friendsresult = mysql_fetch_array($friends)) {
					$usercontactid = $friendsresult["USER_CONTACT_ID"];
					$addfriendsquery = "INSERT INTO MT_GROUP_MEMBERS(GROUP_ID, USERNAME)
										VALUES('$groupid', '$usercontactid')";
					$addfriends = mysql_query($addfriendsquery) or die("Failed to add friends");
				}	
			}
		} else {
			$nofriends = 1;
		}
		
		if(isset($groupid) && !isset($nofriends)) {
			print '<meta http-equiv="refresh" content="0;url=./removeFromGroup.php?">';			
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("Group cannot be created because you dont have friends")';
			echo '</script>';
		}
			
	} 
}
?>

<form name="creategroup" method="post">
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
	<tr><td><label for='creategroup'> <strong>Enter Group Name</strong> </label></td>
	<td><input type='text' size='40' maxlength='20' name='groupname' id='groupname' /></td>
	<td><input type='submit' name='creategroup' id='creategroup' value='Create Group' style='background:none;border:0;color:#4C4646;font-size: 18px;'/></td></tr>
</table>
</div>
</form>


</body>


</html>
