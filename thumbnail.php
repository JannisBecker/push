<?php
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('memory_limit', '128M');

// Example for dynamic image
// See www.mywebmymail.com for more details

if (isset($_GET['thumb'])) {
/*
	header("Cache-Control: private, max-age=10800, pre-check=10800");
	header("Pragma: private");
	// Set to expire in 7 days
	header("Expires: " . date(DATE_RFC822,strtotime(" 7 day")));
	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
		// if the browser has a cached version of this image, send 304
		header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304);
		exit;
	} */




	include_once('res/php/easyphpthumbnail.class.php');
	    // Your full path to the image
    	$full_img = getcwd()."/files/".$_GET['thumb'];
    	$thumb_img = getcwd()."/thumbnails/".$_GET['thumb'];
	$filename = basename($full_img);
	$dir = "thumbnails/".dirname($_GET['thumb'])."/";

	/*echo("fullimg: ".$full_img);echo("<br>");
	echo("thumbimg: ".$thumb_img);echo("<br>");
	echo("filename: ".$filename);echo("<br>");
	echo("dir: ".$dir);
	exit;*/

	if(!file_exists("thumbnails/".$_GET['thumb'])) {
		if(!file_exists($dir))
			mkdir($dir,0755,true);
		$thumb = new easyphpthumbnail;
		$thumb -> Thumbsize = 250;
		$thumb -> Thumblocation = $dir;
		$thumb -> Createthumb($full_img,'file');
	}
	displayimage($thumb_img,$full_img);
}

function displayimage($path,$altimg) {
	if(file_exists($path)) {
		$fileinfo = pathinfo($path);
		switch($fileinfo['extension']) {
			case "gif":
				header("Content-Type: image/gif");
				break;
			case "jpg":
				header("Content-Type: image/jpeg");
				break;
			case "JPG":
				header("Content-Type: image/jpeg");
				break;
			case "jpeg":
				header("Content-Type: image/jpeg");
				break;
			case "png":
				header("Content-Type: image/png");
				break;
		}
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
		readfile($path);
		exit;
	} else {
		displayimage($altimg,null);
	}
}
?>
