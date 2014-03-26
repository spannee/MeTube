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

if(isset($_SESSION['groupid'])) {
	$groupid = $_SESSION['groupid'];
} else {
	print "<meta http-equiv='refresh' content='0;url=index.php'>";
}


?>

<!DOCTYPE html PUBLIC -//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd>
<html xmlns=http://www.w3.org/1999/xhtml>

<head>
<meta http-equiv=Content-Type content=text/html charset=UTF-8 />
<title>Remove From Group</title>
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

<form name="searchFriendForm" method="post">
<br/>
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
	$friendsquery = sprintf("SELECT USERNAME FROM MT_GROUP_MEMBERS WHERE
							 GROUP_ID = '$groupid'");
	$friends = mysql_query($friendsquery) or die('Failed to load friends');
	echo "<tr><td><select name='friends' id='friends' style='width: 300px;'>";
	echo "<option value='selectfriend'>Select Friend</option>";
	if((mysql_num_rows($friends)) > 0) {
		while($friendsresult = mysql_fetch_array($friends)) {
			$groupmember = $friendsresult["USERNAME"];
			echo "<option value='$groupmember'>$groupmember</option>";
		}
	}
	echo "</select>";
	echo "<td><input type='submit' name='removefromgroup' id='removefromgroup' value='RemoveFromGroup' style='background:none;border:0;color:#4C4646;font-size: 18px;' class='stylish-link'/>";
	echo "<input type='submit' name='donotremove' id='donotremove' value='GoToGroup' style='background:none;border:0;color:#4C4646;font-size: 18px;' class='stylish-link'/></td></tr>";

	if(isset($_POST['removefromgroup'])) {
		$membername = $_POST['friends'];
		
		if($membername == 'selectfriend') {
			echo '<script type="text/javascript">';
			echo 'alert("Please select a person")';
			echo '</script>';
		} else {
			$deletequery = "DELETE FROM MT_GROUP_MEMBERS WHERE
						    USERNAME = '$membername' AND
							GROUP_ID = '$groupid'";
			$delete = mysql_query($deletequery) or die('Failed to delete friends');
			$deleteid = mysql_insert_id();
			
			if(isset($deleteid)) {
				echo '<script type="text/javascript">';
				echo 'alert("Removed from group")';
				echo '</script>';
			}
		}

	} elseif(isset($_POST['donotremove'])) {
		print '<meta http-equiv="refresh" content="0;url=./groupTopics.php?">';
	}
?>


</table>
</div>
</form>