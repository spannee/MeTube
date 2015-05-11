<?php

function dbConnect(){
	global $dbconnection;
	
	$host = 'mmlab.cs.clemson.edu';
	$database = 'g5';
	$username = 'g5';
	$password = 'physics';

    $dbconnection = mysqli_connect($host, $username, $password, $database)
        or die("<p>Could not connect: " . mysqli_error($dbconnection));
}

?>