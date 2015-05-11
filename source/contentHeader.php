
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

<?php

if(isset($_GET['searchbutton'])) {
	$mediatype = $_GET['mediatype'];
	$searchinput = $_GET['searchinput'];
	
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
            $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID FROM MT_CONTENT_TAGS WHERE TAGS LIKE ?");
            $value = '%' . $value . '%';
            mysqli_stmt_bind_param($stmt, 's', $value);
            mysqli_stmt_execute($stmt);
            $valuesearch = mysqli_stmt_get_result($stmt) or die('Failed to search tags');
            mysqli_stmt_close($stmt);

			while($valuesearchresults = mysqli_fetch_array($valuesearch)) {
				$contentidsintags[] = $valuesearchresults["CONTENT_ID"];				
			}
		}
		foreach ($contentidsintags as $id) {
            $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
									               FROM MT_CONTENT WHERE CONTENT_TYPE = ?
									               AND CONTENT_ID = ?");
            mysqli_stmt_bind_param($stmt, 'si', $mediatype, $id);
            mysqli_stmt_execute($stmt);
            $contentsearch = mysqli_stmt_get_result($stmt) or die('Failed to search content');
            mysqli_stmt_close($stmt);

			while($contentsearchresults = mysqli_fetch_array($contentsearch)) {
				$contentids[] = $contentsearchresults["CONTENT_ID"]; 
				$contenttitles[] = $contentsearchresults["CONTENT_TITLE"];
			}
		}	
		echo "<table>";
		for($i=0;$i<count($contentids);$i++) {
			echo "<tr><td><a href='./content.php?content_id=".$contentids[$i]."'><img src='fileUploads/image/photo.jpg' height=180 width=150/></a></td>";
			echo "<td><a href='./content.php?content_id=".$contentids[$i]."' class='stylish-link'><font size='-2'>$contenttitles[$i]</font></a></td></tr>";
		}
		echo "</table>";
	}
} elseif(isset($_GET['music'])) {
	$mediatype = $_GET['mediatype'];
	
	if($mediatype == 'N') {
		$mediatypeerror = 1;
	} else {
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
									           FROM MT_CONTENT WHERE CONTENT_CATEGORY = 'M' AND CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 's', $mediatype);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search music');
        mysqli_stmt_close($stmt);
		
		if((mysqli_num_rows($search)) > 0) {
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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
								               FROM MT_CONTENT WHERE CONTENT_CATEGORY = 'S' AND CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 's', $mediatype);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search sports');
        mysqli_stmt_close($stmt);
		
		if((mysqli_num_rows($search)) > 0) {
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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
								               FROM MT_CONTENT WHERE CONTENT_CATEGORY = 'G' AND CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 's', $mediatype);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search games');
        mysqli_stmt_close($stmt);
		
		if((mysqli_num_rows($search)) > 0) {
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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
								               FROM MT_CONTENT WHERE CONTENT_CATEGORY = 'F' AND CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 's', $mediatype);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search films');
        mysqli_stmt_close($stmt);
		
		if((mysqli_num_rows($search)) > 0) {
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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
								               FROM MT_CONTENT WHERE CONTENT_CATEGORY = 'T' AND CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 's', $mediatype);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search TV Shows');
        mysqli_stmt_close($stmt);
		
		if((mysqli_num_rows($search)) > 0) {
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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
								               FROM MT_CONTENT WHERE CONTENT_CATEGORY = 'N' AND CONTENT_TYPE = ?");
        mysqli_stmt_bind_param($stmt, 's', $mediatype);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search News');
        mysqli_stmt_close($stmt);

		if((mysqli_num_rows($search)) > 0) {
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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
											   FROM MT_CONTENT WHERE CONTENT_TYPE = ?
											   ORDER BY VIEW_COUNT DESC LIMIT 10");
        mysqli_stmt_bind_param($stmt, 's', $mediatype);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search Popular Media');
        mysqli_stmt_close($stmt);

		if((mysqli_num_rows($search)) > 0) {
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
        $stmt = mysqli_prepare($dbconnection, "SELECT CONTENT_ID, CONTENT_TITLE, CONTENT_LOCATION
											   FROM MT_CONTENT WHERE CONTENT_TYPE = ?
											   ORDER BY UPLOAD_DATE DESC LIMIT 10");
        mysqli_stmt_bind_param($stmt, 's', $mediatype);
        mysqli_stmt_execute($stmt);
        $search = mysqli_stmt_get_result($stmt) or die('Failed to search Recently Uploaded media');
        mysqli_stmt_close($stmt);

		if((mysqli_num_rows($search)) > 0) {
			$searchresult = 1;
		} else {
			echo '<script type="text/javascript">';
			echo 'alert("No Popular videos found")';
			echo '</script>';
		}	
	}
} 

if(isset($searchresult)) {
	echo "<br/>";
	echo "<br/>";
	echo "<table align='center'>";
	while($searchresult = mysqli_fetch_array($search)) {
		$contentid = $searchresult["CONTENT_ID"];
		$contenttitle = $searchresult["CONTENT_TITLE"];
		echo "<tr><td><a href='./content.php?content_id=".$contentid."'><img src='fileUploads/image/photo.jpg' height=180 width=150/></a></td>";
		echo "<td><a href='./content.php?content_id=".$contentid."' class='stylish-link'><font size='-2'>$contenttitle</font></a></td></tr>";
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