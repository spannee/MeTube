<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Header</title>
<link rel="stylesheet" type="text/css" href="/css/searchStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
</head>

<body>

<?php 
if(isset($_SESSION['username']) && $_SESSION['username'] != NULL) {
	echo '<div>';
	include("./loginHeader.php");
	echo '</div>';
} else {
	echo '<div>';
	include("./registerHeader.php");
	echo '</div>';
}
?>
	

<form name="searchform" action="index.php" method="get">
<br/>
<table align="center">
	<tr>
		<td> 
			<select name="mediatype" id="mediatype"> 
 				<option value="N">Select Type</option> 
				<option value="A">Audio</option> 
				<option value="V">Video</option>
				<option value="I">Image</option>
			</select>
		</td>
		<td> <input type="text" size="60" class="inputtext" maxlength="300" name="searchinput" id="searchinput"/></td>
		<td> <input type="submit" name="searchbutton" value="Search" style="background:none;border:0;color:#4C4646;font-size: 18px;"/> </td>		
	</tr>	
</table>


<br/>
<br/>
<br/>
<br/>
<br/>
<table align="left">
	<tr><td> <input type="submit" name="popular" value="Most Popular" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="recentlyuploaded" value="Most Recent" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr></tr>
	<tr><td> <strong><font size="4">&nbsp;Category</font></strong> </td></tr>
	<tr><td> <input type="submit" name="music" value="Music" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="sport" value="Sport"style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="gaming" value="Gaming" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="films" value="Films" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="tvshows" value="Tv Shows" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
	<tr><td> <input type="submit" name="news" value="News" style="background:none;border:0;color:#4C4646;font-size: 16px;"/> </td></tr>
</table>

</form>
</body>
</html>