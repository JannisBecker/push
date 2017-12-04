<?php
session_start();

if(!isset($dir))
	$dir = $_POST['dir'];

$size = $_SESSION["prefs"]["tilesize"];
$page = $_POST['page'];
$listView = ($_POST['listView'] === "true")? true : false;
$basepath = "../../files/";

$searchterm = $_POST['search'];
$search = ($searchterm != "");

/* No Ajax call */
if(!isset($page)) {
	$page = 1;
	$basepath = "files/";
}

$tiles = $_SESSION["prefs"]["tilesperpage"];
$offset = ($page-1) * $tiles; 
$maxoffset = $offset + $tiles;
$curitem = 0;
$searchitem = 0;

//Iterate Directories
foreach (new DirectoryIterator($basepath.$_SESSION['initial'].$dir) as $folder) {
	if($folder->isDot()) continue;
	if($folder->isDir())
	{
		if(!$search || $search && stripos($folder->getFilename(),$searchterm) !== false) {
			if($curitem >= $offset && $curitem < $maxoffset) {
				$fi = new FilesystemIterator($folder->getPathname(), FilesystemIterator::SKIP_DOTS);
				echo("<li class=\"tile".(($listView)?" linetile":"").(($size < 1)?" ":" tile".$size."x ")."folder\" data-name=\"".$folder->getFilename()."\">");
				echo("<div class=\"folder-preview\">");
				echo ("<span class=\"folder-name\">".$folder->getFilename()."</span>");
				echo ("<span class=\"folder-filecount\">(".((($n = iterator_count($fi)) == 1)?$n." Datei":$n." Dateien").")</span>");
				echo("</div>");		
			}
		}
		$curitem++;
		$searchitem++;
	}
}

//Get Files and sort by creation date
chdir(getcwd()."/".$basepath);
array_multisort(array_map('filemtime', ($files = glob($_SESSION['initial'].$dir."/*.*"))), SORT_DESC, $files);
$imgexts = array("jpg","jpeg","JPG","png","PNG","gif");

//Iterate Files
$i = 0;
foreach($files as $file) {
	$extension = pathinfo($file, PATHINFO_EXTENSION);
	$filename = pathinfo($file, PATHINFO_BASENAME);
	if ($file != "." && $file != ".." && !is_dir($file))
	{
		if(!$search || $search && stripos($filename,$searchterm) !== false) {
			if($curitem >= $offset && $curitem < $maxoffset) {
				echo("<li class=\"tile".(($listView)?" linetile":"").(($size < 1)?" ":" tile".$size."x ")."file\" data-index=\"".$i."\" data-name=\"".$filename."\">");
					echo("<div class=\"file-preview\">");
						if(in_array($extension, $imgexts)) {
							echo("<img draggable=\"false\" src=\"/thumbnails/".$file."\" />");
						} else {
							echo("<span class=\"file-other\">".$extension."</span>");
						}
					echo("</div><span title=\"".$filename."\" class=\"file-name\">".$filename."</span>");
				echo("</li>");
			}
		}
		$searchitem++;
		$curitem++;
	}
	$i++;
}
?>