<?php

function dbConnect(){
	global $connection;
	
	$host = 'mmlab.cs.clemson.edu';
	$database = 'g5';
	$username = 'g5';
	$password = 'physics';

	$connection = mysql_connect($host, $username, $password) or die("<p>Could not connect: " . mysql_error());
	mysql_select_db($database);
}


?>