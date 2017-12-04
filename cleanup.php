<?php
chdir('thumbnails');
$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('./', RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST,
    RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
);

$cleanedfiles = 0;
$files = 0;
$cleanedfolders = 0;
$folders = 0;

foreach ($iter as $path => $dir) {
	$rpath = realpath('../files/'.$path);

	if ($dir->isDir()) {
		$folders++;
		if($rpath === false) {
			$cleanedfolders++;
			delTree($path);	
		}
	} else {
		if(strpos($path,"htaccess") === false) {
			if(!file_exists($rpath)) {
				unlink($path);
				$cleanedfiles++;
			}	
		}
		$files++;
    	}
}
echo("Cleaned ".$cleanedfiles." of ".$files." files and ".$cleanedfolders." of ".$folders." folders!");

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