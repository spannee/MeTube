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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Tag Cloud</title>
<style type="text/css">
#tagcloud {
    width: 300px;
    position:fixed;
    top:30%;
    left:72%;
    background:#CFE3FF;
    color:#0066FF;
    padding: 10px;
    border: 1px solid #559DFF;
    text-align:center;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px;
}
 
#tagcloud a:link, #tagcloud a:visited {
    text-decoration:none;
    color: #333;
}
 
#tagcloud a:hover {
    text-decoration: underline;
}
 
#tagcloud span {
    padding: 4px;
}
 
#tagcloud .smallest {
    font-size: x-small;
}
 
#tagcloud .small {
    font-size: small;
}
 
#tagcloud .medium {
    font-size:medium;
}
 
#tagcloud .large {
    font-size:large;
}
 
#tagcloud .largest {
    font-size:larger;
}
</style>
</head>

<body>

<?php
$tags = array(); 
$maximum = 0;

$stmt = mysqli_prepare($dbconnection, "SELECT TAGS, COUNTER FROM MT_TAGS ORDER BY COUNTER DESC LIMIT 30");
mysqli_stmt_execute($stmt);
$cloud = mysqli_stmt_get_result($stmt) or die('Failed to load tag cloud');
mysqli_stmt_close($stmt);

if(mysqli_num_rows($cloud) > 0) {
	while ($row = mysqli_fetch_array($cloud)) {
		$tag = $row['TAGS'];
		$counter = $row['COUNTER'];
	
		if ($counter > $maximum) {
		 	$maximum = $counter;
		}
		
		$tags[] = array('tags' => $tag, 'counter' => $counter);
		shuffle($tags);
	}
}
?>

<div id="tagcloud">
<?php 

if(isset($tags)) {
	foreach ($tags as $tag) { 
 	$percent = floor(($tag['counter'] / $maximum) * 100); 
	 
 	if ($percent < 20) {
   		$class = 'smallest'; 
 	} elseif ($percent >= 20 and $percent < 40) {
   		$class = 'small'; 
 	} elseif ($percent >= 40 and $percent < 60) {
   		$class = 'medium';
 	} elseif ($percent >= 60 and $percent < 80) {
   		$class = 'large';
 	} else {
	 	$class = 'largest';
 	}
 	
?>
<span class="<?php echo $class; ?>">

<a href="index.php?search_tag=<?php echo $tag['tags']; ?>"><?php echo $tag['tags']; ?></a>
</span>
<?php } } ?>
</div>


</body>
</html>