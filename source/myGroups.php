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
<title>My Group</title>
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
<div>
<?php include("./loginHeader.php"); ?>
</div>
<?php 
if(isset($_POST['selectgroup'])) {
	$groupid = $_POST['groupname'];
	
	if($groupid == 0) {
		echo '<script type="text/javascript">';
		echo 'alert("Please select a group")';
		echo '</script>';
	}else {
		$_SESSION['groupid'] = $groupid;
		print '<meta http-equiv="refresh" content="0;url=./groupTopics.php?">';			
	} 
} elseif(isset($_POST['addtogroup'])) {
	$groupid = $_POST['groupname'];

	if($groupid == 0) {
		echo '<script type="text/javascript">';
		echo 'alert("Please select a group")';
		echo '</script>';
	} else {
		$_SESSION['groupid'] = $groupid;
		print '<meta http-equiv="refresh" content="0;url=./addToGroup.php?">';
	}

} elseif(isset($_POST['removefromgroup'])) {
	$groupid = $_POST['groupname'];

	if($groupid == 0) {
		echo '<script type="text/javascript">';
		echo 'alert("Please select a group")';
		echo '</script>';
	} else {
		$_SESSION['groupid'] = $groupid;
		print '<meta http-equiv="refresh" content="0;url=./removeFromGroup.php?">';
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
	<?php
    $stmt = mysqli_prepare($dbconnection, "SELECT GROUP_ID, GROUP_NAME FROM MT_GROUPS WHERE
								           GROUP_CREATED_BY = ?");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $loadgroups = mysqli_stmt_get_result($stmt) or die('Failed to load groups');
    mysqli_stmt_close($stmt);
	echo "<select name='groupname' id='groupname' style='width: 300px;'>";
	echo "<option value='0'>Select Groups</option>";
	if((mysqli_num_rows($loadgroups)) > 0) {
		while($groupresult = mysqli_fetch_array($loadgroups)) {
			$groupid = $groupresult["GROUP_ID"];
			$groupname = $groupresult["GROUP_NAME"];
			echo "<option value='$groupid'>$groupname</option>";
		}
		echo "</select>";
		echo "<input type='submit' name='selectgroup' id='selectgroup' value='Select Group' style='background:none;border:0;color:#4C4646;font-size: 18px;'/>";
	}
	?>
	<tr>
	<td>
		<input type='submit' name='addtogroup' id='addtogroup' value='AddFriendsToGroup' style='background:none;border:0;color:#4C4646;font-size: 18px;'/>
		<input type='submit' name='removefromgroup' id='removefromgroup' value='RemoveFriendsFromGroup' style='background:none;border:0;color:#4C4646;font-size: 18px;'/>
	</td>
	</tr>
	
	
</table>
</div>
</form>


</body>


</html>
