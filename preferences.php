<?php
//Session Stuff
session_start();
if(!isset($_SESSION['initial'])) {
	header("Location: login.php");
}

//Save Preferences
if(isset($_POST["save"])) {
	require_once("res/php/sql-config.php");
	$conn = mysqli_connect($db_host,$db_user,$db_pass);
	if($conn) {
		$db = mysqli_select_db($conn, "anuk_push");
		if($db) {
			$tilesize = $_POST["tilesize"];
			$navbar = $_POST["navbar"];
			$listview = $_POST["listview"];
			$tiles = $_POST["tiles"];

			$query = "UPDATE preferences AS p, users AS u SET p.tilesize = ".$tilesize.", p.tiles = ".$tiles.", p.navbar = ".$navbar.", p.listview = ".$listview." WHERE u.username = '".$_SESSION["username"]."' AND u.id = p.user_id";
			if(mysqli_query($conn,$query)) {
				$_SESSION["prefs"]["tilesize"] = $tilesize;
				$_SESSION["prefs"]["navbar"] = $navbar;
				$_SESSION["prefs"]["tilesperpage"] = $tiles;
				$_SESSION["prefs"]["listview"] = $listview;
				echo ("success");
				exit;
			} else die ("query failed");
		} else die ("couldnt open database");
	} else die("cant connect to mysql server");
}
?>

<html>
<head>
	<title>push: Preferences</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="res/css/style.css">
	<script type="text/javascript" src="res/js/jquery.js"></script>
</head>
<body>
	<div class="header">
		<div class="logo">
			<img class="logo-img" src="res/img/logo.png">
		</div>
		<ul class="menubar">
			<li><div class="page-title"><?php echo($_SESSION["username"]);?>'s preferences</div></li>
		</ul>
		<ul class="actionbar">
			<li class="button apply">Apply</li>
		</ul>
	</div>
	<div class="content-wrapper">
		<div class="error-alert2" style="display:none"></div>
		<div class="wrapped">
			<p class="pref-title">Fixed Navigation bar <span class="checkbox navbar <?php echo((($_SESSION["prefs"]["navbar"]!=1)?:"checked"));?>"></span></p>
			<p class="pref-title">Use List View by default <span class="checkbox listview <?php echo((($_SESSION["prefs"]["listview"]!=1)?:"checked"));?>"></span></p>
			<p class="pref-title">Items per page <input class="pref-tiles" type="text" value="<?php echo($_SESSION["prefs"]["tilesperpage"]);?>" /></p>
			<li class="tile tile1x pref" data-size="1">
				<div class="file-preview">
					<span class="file-other">Tile Size x1</span>
				</div>
			</li>
			<li class="tile tile2x pref" data-size="2">
				<div class="file-preview">
					<span class="file-other">Tile Size x2</span>
				</div>
			</li>
			<li class="tile tile3x pref" data-size="3">
				<div class="file-preview">
					<span class="file-other">Tile Size x3</span>
				</div>
			</li>
			<li class="tile tile4x pref" data-size="4">
				<div class="file-preview">
					<span class="file-other">Tile Size x4</span>
				</div>
			</li>
		</div>
	</div>
</body>



<script>
var selected_tile = 1;

$(document).ready(function() {
	$('li.tile.tile<?php echo($_SESSION["prefs"]["tilesize"]);?>x').addClass('selected');
	selected_tile = <?php echo($_SESSION["prefs"]["tilesize"]);?>;
});

$("li.button.apply").click(function() {
	var fnbar = $('span.checkbox.navbar').hasClass('checked')?1:0;
	var listview = $('span.checkbox.listview').hasClass('checked')?1:0;
	var tiles = $('input.pref-tiles').val();
	if(!isNumeric(tiles))
		tiles = <?php echo($_SESSION["prefs"]["tilesperpage"]);?>;

	$.ajax({
		type: "POST",
		url: "preferences.php",
		data: { save: "true", tiles: tiles, tilesize: selected_tile, navbar: fnbar, listview: listview }
	}).done(function( msg ) {
		if(msg == "success") {
	    		location.href = "./";
	    	} else {
	    		$("div.error-alert2").text("Error while saving your preferences: "+msg);
	    		$("div.error-alert2").fadeIn();
	    	}
	 });
});

$("div.logo").click(function() {
	location.href = "./";
});

$("span.checkbox").click(function() {
	$(this).toggleClass("checked");
});

$("li.tile").click(function() {
	$(this).toggleClass("selected");
	$('li.tile'+selected_tile+'x').removeClass("selected");
	selected_tile = $(this).data('size');
});

function isNumeric(input)
{
    return (input - 0) == input && (''+input).trim().length > 0;
}
</script>
</html>
