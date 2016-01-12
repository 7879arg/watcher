<?php

updateGit('initial check of updateGit');


function updateGit($message) {
	$dirs = array("md5", "raw", ".");
	foreach ($dirs as $dir) {
		$dh = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			if (($filename <> '.') && ($filename <> '..')) {
				$cmd[] = 'git add ' . $dir . '/' . $filename;
			}
		}
	}
	$cmd[] = 'git commit -m "' . $message . '"';
	$cmd[] = 'git push -u origin master';
	foreach ($cmd as $c) echo $c . "\n" . trim(`$c`) . "\n";
}
