<?php
function generate_breadcrumbs() {
	$dir = $_GET['dir'];
	$curdir = "";
	$crumbs = explode("/",$dir);
	$i = 0;
	$count = count($crumbs);
	$len = strlen($dir);
	$maxlen = 50;

	foreach ($crumbs as $c) {
		if ($i == $count - 1) {
			$current = true;
		}
		
		if ($i == 0) {
			echo((($current)?"<li class=\"current\">":"<li><a href=\"./\">")."<i class=\"fa fa-home\"></i> Home".(($current)?"</li>":"</a></li>"));
	  	} else {
	  		$curdir .= "/".$c;
	  		echo((($current)?"<li class=\"current\">":"<li><a href=\"./?dir=".$curdir."\">")." ".$c.(($current)?"</li>":"</a></li>"));
	  	}

	  	if(!($current)) {
	  		echo("<li>&nbsp/&nbsp</li>");
	  	}

	  	$i++;
	}
}

/*function generate_breadcrumbs() {
	$dir = $_GET['dir'];
	$curdir = "";
	$crumbs = explode("/",$dir);
	$i = 0;
	$count = count($crumbs);
	$len = strlen($dir);
	$maxlen = 20;
	
	foreach ($crumbs as $c) {
		if ($i == $count - 1) {
			$current = true;
		}
		
		if ($i == 0) {
			echo((($current)?"<li class=\"current\">":"<li><a href=\"./\">")."<i class=\"fa fa-home\"></i> Home".(($current)?"</li>":"</a></li><li>&nbsp/&nbsp</li>"));
	  	} else {
	  		$curdir .= "/".$c;
	  		if($current || $len <= $maxlen) {
	  			echo((($current)?"<li class=\"current\">":"<li><a href=\"./?dir=".$curdir."\">")." ".$c." (".$len.") ".(($current)?"</li>":"</a></li><li>&nbsp/&nbsp</li>"));
	  		} else {
	  			$len -= strlen($c);
	  			if($len <= $maxlen) {
	  				echo("<li><a href=\"./?dir=".$curdir."\">...</a></li><li>&nbsp/&nbsp</li>");
	  			}
	  		}
	  	}
	  	$i++;
	}
}*/

function getPartDir($dircrumbs, $layer) {
	$partdir = "";
	for($i = 0;  $i < $layer; $i++) {
		$partdir += "/.".$dircrumbs[$i];
	}
	return $partdir;
}


function navbar_fixed() {
	echo ("".(($_SESSION["prefs"]["navbar"] == 0)?: "style=\"position:fixed\""));
}
?>
