<?php 
session_start();

if(!isset($_SESSION['initial']))
	die("not logged in");

$action = $_POST['action'];
$folder = $_POST['folder']; // e.g. /foo/bar or /

if(!isset($folder))
	die("no folder");

if(!isset($_POST['files']))
	die("no files");

$files = explode(',',$_POST['files']);

switch($action) {
	case 'rename':
		$newname = $_POST['name'];
		if(!isset($newname))
			die("no new name");
		if(rename("../../files/".$_SESSION['initial'].$folder.'/'.$files[0],"../../files/".$_SESSION['initial'].$folder.'/'.$newname)) {
			echo("success");
			return;
		} else die("rename failed");
		break;
	case 'move':
		$newpath = $_POST['path'];
		$rootpath = $_POST['root'];
		if(!isset($newpath))
			die("no new path");
		if($rootpath == "true") $newpath = "";
		foreach($files as $file) {
			if(!rename("../../files/".$_SESSION['initial'].$folder.'/'.$file,"../../files/".$_SESSION['initial'].$newpath."/".$file))
				die("moving failed");
		}
		echo("success");
		break;
	case 'delete':
		foreach($files as $file) {
			if(!unlink("../../files/".$_SESSION['initial'].$folder.'/'.$file))
				die("moving failed");
		}
		echo("success");
		break;
	default:
		die("invalid action");
}
?>