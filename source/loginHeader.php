<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>LoginHeader</title>
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
</head>

<body>
<?php
if(isset($_SESSION['username'])) {
	$username = $_SESSION['username'];
	$displayname = ucfirst($username);

	echo '<table align="right">';
	echo '<tr>';
	echo '<td>';
	echo '<a href="./index.php" class="stylish-button">Search Videos</a>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td>';
	echo '<a href="./userProfile.php" class="stylish-button">';
	echo $displayname;
	echo '</a>';
	echo '</td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td>';
	echo '<a href="./upload.php" class="stylish-button">Upload</a>';
	echo '</td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td>';
	echo '<a href="./logout.php" class="stylish-button">Logout</a>';
	echo '</td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '</tr>';
	echo '</table>';
}
?>

</body>
</html>