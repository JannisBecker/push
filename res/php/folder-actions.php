<?php 
session_start();

if(!isset($_SESSION['initial']))
	die("not logged in");

$action = $_POST['action'];
$folder = $_POST['folder']; // e.g. /foo/bar or /

if(!isset($folder))
	die("no folder");

switch($action) {
	case 'new':
		$newname = $_POST['name'];
		if(!isset($newname))
			die("no new name");
		if(mkdir("../../files/".$_SESSION['initial'].$folder."/".$newname)) {
			echo("success");
			return;
		} else die("mkdir failed");
		break;
	case 'rename':
		$newname = $_POST['name'];
		if(!isset($newname))
			die("no new name");
		if(rename("../../files/".$_SESSION['initial'].$folder,dirname("../../files/".$_SESSION['initial'].$folder)."/".$newname)) {
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
		$foldername = basename("../../files/".$_SESSION['initial'].$folder);
		if(rename("../../files/".$_SESSION['initial'].$folder,"../../files/".$_SESSION['initial'].$newpath."/".$foldername)) {
			echo("success");
			return;
		} else die("moving failed");
		break;
		break;
	case 'delete':
		if(is_dir("../../files/".$_SESSION['initial'].$folder)) {
			delTree("../../files/".$_SESSION['initial'].$folder);
			echo("success");
			return;
		} else die("no dir");
		break;
	default:
		die("invalid action");
}


function delTree($dir) {
    $files = glob( $dir . '*', GLOB_MARK );
    foreach( $files as $file ){
        if( substr( $file, -1 ) == '/' )
            delTree( $file );
        else
            unlink( $file );
    }
    rmdir( $dir );
}
?>