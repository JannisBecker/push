<?php
$user = $_GET["user"];
$file = $_GET["file"];
$ext = pathinfo($file, PATHINFO_EXTENSION);

$file_url = "/files/".$user."/".$file;

/* Direct image request? */
$accept = $_SERVER['HTTP_ACCEPT'];
$pos = strpos($accept,"html");
if($pos === False) {
	header("Content-Type: image/".$ext);
	header("Content-length: ".filesize(getcwd().$file_url));
	echo readfile(getcwd().$file_url);
	exit;
}

/* Download File? */
if(isset($_GET["dl"])) {
    	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary");
	header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
	header("Content-length: ".filesize(getcwd().$file_url));
	readfile(getcwd().$file_url);
  	flush();
	exit;
}

$imgexts = array("jpg","jpeg","JPG","png","PNG","gif");
$videoexts = array("mp4","webm");
$audioexts = array("mp3","wav");
$txtexts = array("txt");

/* Determine file type */
if(file_exists(getcwd().$file_url)) {
	if(in_array($ext,$imgexts)):
		$type = "img";
	else: if(in_array($ext,$videoexts)):
		$type = "vid";
	else: if(in_array($ext,$audioexts)):
		$type = "aud";
	else: if(in_array($ext,$txtexts)):
		$type = "txt";
	else:
		$type = "oth";
	endif;endif;endif;endif;
} else {
	$type = "none";
}
?>

<html>
<head>
	<title>push: <?php echo(basename($file_url)); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8" />
	<?php if($type == "img"): ?>
		<meta property="og:image" content="<?php echo($file_url);?>" />
	<?php endif; ?>
	<link rel="stylesheet" href="/res/css/style.css">
	<?php if($type == "vid" || $type == "aud") echo("<script src=\"http://api.html5media.info/1.1.8/html5media.min.js\"></script>"); ?>
	<script type="text/javascript" src="/res/js/jquery.js"></script>
</head>
<body>
	<div class="header">
		<div class="logo">
			<img class="logo-img" src="/res/img/logo.png">
		</div>
		<ul class="menubar">
			<li><div class="page-title"><?php echo(basename($file_url)); ?></div></li>
		</ul>
		<ul class="actionbar"><li class="button download">Download</li></ul>
	</div>

	<div class="content-wrapper">
		<?php switch($type) { case "img": ?>
				<img class="wrapped image zoomed" src="<?php echo($file_url);?>">
			<?php break; case "vid": ?>
				<video class="wrapped media" width="640" height="400" src="<?php echo($file_url);?>" controls preload></video>
			<?php break; case "aud": ?>
				<audio class="wrapped media" width="640" height="400" src="<?php echo($file_url);?>" controls preload></audio>
			<?php break; case "txt": ?>
				<p class="wrapped txt">
					<?php echo nl2br(); 
						$content = file_get_contents(".".$file_url);
						$content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
						echo nl2br($content);
					?>
				</p>
			<?php break; case "oth":
				if(file_exists("res/img/filetypes/".$ext.".png")) {?>
					<img class="file-img" src="/res/img/filetypes/<?php echo($ext);?>.png">
				<?php } else {?>
					<img class="file-img" src="/res/img/filetypes/_blank.png">
				<?php } ?>
			<?php break; case "none": ?>
				<span class="wrapped error">Your requested file was not found on this server!</span>
				<span class="wrapped error sm">Check your url or contact the site administrator for help</span>
		<?php break; } ?>
	</div>
</body>
<script>
$("li.button.download").click(function() {
	location.href += "?dl";
});

$("img.wrapped").click(function() {
	$(this).toggleClass("zoomed");
});

$("div.logo").click(function() {
	location.href = "/./";
});
</script>
</html>
