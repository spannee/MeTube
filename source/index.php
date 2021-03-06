<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Search</title>
<link rel="stylesheet" type="text/css" href="/css/searchStyle.css" />
<link rel="stylesheet" type="text/css" href="css/linkStyle.css" />
</head>

<body>

<div>
<?php
include("./header.php");
?>
</div>

<div>
<?php
include("./tagCloud.php");
?>
</div>

<?php

if(isset($_GET['searchbutton']) || isset($_GET['search_tag'])) {
	if(isset($_GET['mediatype'])) {
		$mediatype = $_GET['mediatype'];
	}
	if(isset($_GET['searchbutton'])) {
		$searchinput = $_GET['searchinput'];
	} elseif(isset($_GET['search_tag'])) {
		$searchinput = $_GET['search_tag'];
		$mediatype = 'V';
	}
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} elseif($searchinput == NULL) {
		$searcherror = 1;
	} else {	
		$valuesinsearch = explode(' ',$searchinput);
		$contentidsintags = array();
		$contentids = array();
		$contenttitles = array();
		foreach ($valuesinsearch as $value) {			
			$valuesearch = "SELECT CONTENT_ID
							FROM MT_CONTENT_TAGS WHERE
							TAGS LIKE '%$value%'";
			$valuesearchquery = mysql_query($valuesearch) or die('Failed to search tags');
			if(mysql_num_rows($valuesearchquery) > 0) {
				$tagsearch = "SELECT *
							  FROM MT_TAGS WHERE
							  TAGS = '$value'";
				$tagsearchquery = mysql_query($tagsearch) or die('Failed to search tags');
				if(mysql_num_rows($tagsearchquery) == 0) {
					$inserttagquery = "INSERT INTO MT_TAGS
									   VALUES('$value', '1')";
					$inserttag = mysql_query($inserttagquery) or die('Failed to insert tags');
				} elseif(mysql_num_rows($tagsearchquery) == 1) {
					$updatecounterquery = "UPDATE MT_TAGS
										   SET COUNTER=COUNTER+1
										   WHERE TAGS = '$value'";
					$updatecounter = mysql_query($updatecounterquery) or die('Failed to update tags');
				}
			}
			while($valuesearchresults = mysql_fetch_array($valuesearchquery)) {
				$contentidsintags[] = $valuesearchresults["CONTENT_ID"];				
			}
		}
		foreach ($contentidsintags as $id) {
			$contentsearch = sprintf("SELECT CONTENT_ID,
									   CONTENT_TITLE,
									   CONTENT_LOCATION
									   FROM MT_CONTENT WHERE
									   CONTENT_TYPE = '$mediatype'
									   AND 
									   CONTENT_ID = '$id' AND 
									   CONTENT_SHARING != 'P'");
			$valuecontentquery = mysql_query($contentsearch) or die('Failed to search content');
			while($contentsearchresults = mysql_fetch_array($valuecontentquery)) {
				$contentids[] = $contentsearchresults["CONTENT_ID"]; 
				$contenttitles[] = $contentsearchresults["CONTENT_TITLE"];
			}
		}	
		echo "<table style='margin-left:300px;'>";
		for($i=0;$i<count($contentids);$i++) {
			echo "<tr><td><a href='./content.php?content_id=".$contentids[$i]."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
			echo "<td><a href='./content.php?content_id=".$contentids[$i]."' class='stylish-link'><font size='4'>$contenttitles[$i]</font></a></td></tr>";
		}
		echo "</table>";
	}
} elseif(isset($_GET['music'])) {
	$mediatype = $_GET['mediatype'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$musicsearch = sprintf("SELECT CONTENT_ID,
									   CONTENT_TITLE,
									   CONTENT_LOCATION
									   FROM MT_CONTENT WHERE
									   CONTENT_CATEGORY = 'M' AND
									   CONTENT_TYPE = '$mediatype' AND 
									   CONTENT_SHARING != 'P'");
		$search = mysql_query($musicsearch) or die('Failed to search music');
		
		if((mysql_num_rows($search)) > 0) {
			$searchresult = 1;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Music files found")';
			echo '</script>';
		}
	}	
} elseif(isset($_GET['sport'])) {
	$mediatype = $_GET['mediatype'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$sportsearch = sprintf("SELECT CONTENT_ID,
									   CONTENT_TITLE,
									   CONTENT_LOCATION
								       FROM MT_CONTENT WHERE
									   CONTENT_CATEGORY = 'S' AND
									   CONTENT_TYPE = '$mediatype' AND 
									   CONTENT_SHARING != 'P'");
		$search = mysql_query($sportsearch) or die('Failed to search sports');
		
		if((mysql_num_rows($search)) > 0) {
			$searchresult = 1;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Sport files found")';
			echo '</script>';
		}
	}
} elseif(isset($_GET['gaming'])) {
	$mediatype = $_GET['mediatype'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$gamesearch = sprintf("SELECT CONTENT_ID,
									   CONTENT_TITLE,
									   CONTENT_LOCATION
									   FROM MT_CONTENT WHERE
									   CONTENT_CATEGORY = 'G' AND
									   CONTENT_TYPE = '$mediatype' AND 
									   CONTENT_SHARING != 'P'");
		$search = mysql_query($gamesearch) or die('Failed to search games');
		
		if((mysql_num_rows($search)) > 0) {
			$searchresult = 1;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Game files found")';
			echo '</script>';
		}
	}
} elseif(isset($_GET['films'])) {
	$mediatype = $_GET['mediatype'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$filmsearch = sprintf("SELECT CONTENT_ID,
									  CONTENT_TITLE,
									  CONTENT_LOCATION
									  FROM MT_CONTENT WHERE
									  CONTENT_CATEGORY = 'F' AND
									  CONTENT_TYPE = '$mediatype' AND 
									  CONTENT_SHARING != 'P'");
		$search = mysql_query($filmsearch) or die('Failed to search films');
		
		if((mysql_num_rows($search)) > 0) {
			$searchresult = 1;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Film files found")';
			echo '</script>';
		}		
	}
} elseif(isset($_GET['tvshows'])) {
	$mediatype = $_GET['mediatype'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$tvshowsearch = sprintf("SELECT CONTENT_ID,
									  CONTENT_TITLE,
									  CONTENT_LOCATION
									  FROM MT_CONTENT WHERE
									  CONTENT_CATEGORY = 'T' AND
									  CONTENT_TYPE = '$mediatype' AND 
									  CONTENT_SHARING != 'P'");
		$search = mysql_query($tvshowsearch) or die('Failed to search TV Shows');
		
		if((mysql_num_rows($search)) > 0) {
			$searchresult = 1;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No TV Show files found")';
			echo '</script>';
		}		
	}
} elseif(isset($_GET['news'])) {
	$mediatype = $_GET['mediatype'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$tvshowsearch = sprintf("SELECT CONTENT_ID,
										CONTENT_TITLE,
										CONTENT_LOCATION
										FROM MT_CONTENT WHERE
										CONTENT_CATEGORY = 'N' AND
										CONTENT_TYPE = '$mediatype' AND 
									    CONTENT_SHARING != 'P'");
		$search = mysql_query($tvshowsearch) or die('Failed to search News');

		if((mysql_num_rows($search)) > 0) {
			$searchresult = 1;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No News files found")';
			echo '</script>';
		}	
	}
} elseif(isset($_GET['popular'])) {
	$mediatype = $_GET['mediatype'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$popularitysearch = sprintf("SELECT CONTENT_ID,
											CONTENT_TITLE,
											CONTENT_LOCATION
											FROM MT_CONTENT WHERE
											CONTENT_TYPE = '$mediatype' AND 
									   		CONTENT_SHARING != 'P'
											ORDER BY VIEW_COUNT DESC LIMIT 10");
		$search = mysql_query($popularitysearch) or die('Failed to search Popular Media');

		if((mysql_num_rows($search)) > 0) {
			$searchresult = 1;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Popular media found")';
			echo '</script>';
		}	
	}
} elseif(isset($_GET['recentlyuploaded'])) {
	$mediatype = $_GET['mediatype'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
		$recentlyuploaded = sprintf("SELECT CONTENT_ID,
											CONTENT_TITLE,
											CONTENT_LOCATION
											FROM MT_CONTENT WHERE
											CONTENT_TYPE = '$mediatype' AND 
									   		CONTENT_SHARING != 'P'
											ORDER BY UPLOAD_DATE DESC LIMIT 10");
		$search = mysql_query($recentlyuploaded) or die('Failed to search Recently Uploaded media');

		if((mysql_num_rows($search)) > 0) {
			$searchresult = 1;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Popular videos found")';
			echo '</script>';
		}	
	}
} else {
	$onload = sprintf("SELECT CONTENT_ID,
							  CONTENT_TITLE,
							  CONTENT_LOCATION
							  FROM MT_CONTENT WHERE 
							  CONTENT_SHARING != 'P'
							  ORDER BY VIEW_COUNT DESC LIMIT 10");
	$search = mysql_query($onload) or die('Failed to load media');

	if((mysql_num_rows($search)) > 0) {
		$searchresult = 1;
	} 
}

if(isset($searchresult)) {
	echo "<br/>";
	echo "<br/>";
	echo "<table style='margin-left:300px;'>";
	while($searchresult = mysql_fetch_array($search)) {
		$contentid = $searchresult["CONTENT_ID"];
		$contenttitle = $searchresult["CONTENT_TITLE"];
		echo "<tr><td><a href='./content.php?content_id=".$contentid."'><img src='fileUploads/image/photo.jpg' height=90 width=170/></a></td>";
		echo "<td><a href='./content.php?content_id=".$contentid."' class='stylish-link'><font size='4'>$contenttitle</font></a></td></tr>";
	}
	echo '</table>';
} elseif(isset($mediatypeerror)) {
	echo '<script type="text/javascript">';
	echo 'alert("Please select a type")';
	echo '</script>';
} elseif(isset($searcherror)) {
	echo '<script type="text/javascript">';
	echo 'alert("Please enter a value for search")';
	echo '</script>';
}
?>



</body>
</html>