<?php
header("Content-Type: text/text");

$key = "<upload key here>";
$uploadhost = "http://".$_SERVER["HTTP_HOST"]."/";
$redirect = $uploadhost;

if ($_SERVER["REQUEST_URI"] == "/robot.txt") { die("User-agent: *\nDisallow: /"); }

if (isset($_POST['k']) && isset($_POST['user'])) {
	if ($_POST['k'] == $key) {
		$initial = $_POST['user'];
		$folder = ($_POST['folder'])?$_POST['folder']:"/";
		$target = getcwd()."/../files/". $initial . $folder . basename($_FILES['d']['name']);
		$extension = end(explode(".", $_FILES["d"]["name"]));
		if(!in_array($extension, array("php"))) {	
			if (move_uploaded_file($_FILES['d']['tmp_name'], $target)) {
				$random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6); 
				if(isset($_POST['random'])) {
					$path = $initial.$folder.$random.".".$extension;
					rename($target, getcwd()."/../files/".$path);
					echo $uploadhost.$path;
				} else {
					$path = $initial.$folder.clean(basename($_FILES['d']['name']));
					rename($target, getcwd()."/../files/".$path);
					echo $uploadhost.$path;
				}
			} else echo "Sorry, there was a problem uploading your file.";
		} else echo("Upload of scriptfiles not allowed!");
	} else echo("ERROR: Key is incorrect!");
} else {
	//header('Location: '.$redirect);
}

function clean($string) {
	$str = preg_replace('/[^A-Za-z0-9\-\ \.\_]/', '', $string); // Removes special chars.
	//return str_replace(" ", "%20", $str);
	return $str;
}
?>

