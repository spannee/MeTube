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
<title>Unjoin Group</title>
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
if(isset($_POST['unjoingroup'])) {
	$groupid = $_POST['groupname'];
	
	if($groupid == 0) {
		echo '<script type="text/javascript">';
		echo 'alert("Please select a group")';
		echo '</script>';
	}else {
		$unjoingroupquery = "DELETE FROM MT_GROUP_MEMBERS WHERE
							 GROUP_ID = '$groupid' AND  USERNAME = '$username'";
		$unjoingroup = mysql_query($unjoingroupquery) or die("Failed to unjoin");
		$deleteid = mysql_insert_id();
	
		if(isset($deleteid)) {
			echo '<script type="text/javascript">';
			echo 'alert("Removed from group")';
			echo '</script>';
		}	
	} 
}
?>

<form name="unjoingroup" method="post">
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
	<tr>
	<td>
	<?php 
	$loadgroupsquery = sprintf("SELECT GROUP_ID, GROUP_NAME FROM MT_GROUPS WHERE GROUP_ID IN
								(SELECT GROUP_ID FROM MT_GROUP_MEMBERS WHERE
								USERNAME = '$username')");
	$loadgroups = mysql_query($loadgroupsquery) or die('Failed to load groups');
	echo "<select name='groupname' id='groupname' style='width: 300px;'>";
	echo "<option value='0'>Select Groups</option>";
	if((mysql_num_rows($loadgroups)) > 0) {
		while($groupresult = mysql_fetch_array($loadgroups)) {
			$groupid = $groupresult["GROUP_ID"];
			$groupname = $groupresult["GROUP_NAME"];
			echo "<option value='$groupid'>$groupname</option>";
		}
		echo "</select>";
	}
	?>
	</td>
	<td><input type='submit' name='unjoingroup' id='unjoingroup' value='Unjoin Group' style='background:none;border:0;color:#4C4646;font-size: 18px;'/></td>
	</tr>
	
</table>
</div>
</form>


</body>


</html>
