<?php
function generate_breadcrumbs() {
	$dir = $_GET['dir'];
	$crumbs = explode("/",$dir);
	$i = 0;
	$len = count($crumbs);
	foreach ($crumbs as $c) {
		if ($i == $len - 1) {
			$current = true;
		}
		
		if ($i == 0) {
			echo((($current)?"<li class=\"current\">":"<li><a href=\"./\">")."<i class=\"fa fa-home\"></i> Home".(($current)?"</li>":"</a></li>"));
	  	} else {
	  		echo((($current)?"<li class=\"current\">":"<li><a href=\"./?dir=".dirname($dir)."\">")." ".$c.(($current)?"</li>":"</a></li>"));
	  	}

	  	if(!($current)) {
	  		echo("<li>&nbsp/&nbsp</li>");
	  	}

	  	$i++;
	}
}

function navbar_fixed() {
	echo ("".(($_SESSION["prefs"]["navbar"] == 0)?: "style=\"position:fixed\""));
}
?>
