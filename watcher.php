<?php

$urls = file('urls.txt');
$updatemessage = '';
foreach ($urls as $url) {
	$url = trim($url); $filename = urlencode($url);
	$oldmd5 = implode(file('md5/' . $filename));
	$md5 = md5(implode(file($url)));

	if ($oldmd5 <> $md5) {
		$updatemessage = "Found change on " . $url . "\n";
		$fp = fopen('md5/' . $filename, 'w+');
		fwrite($fp, $md5);
		fclose($fp);
		$fp = fopen('raw/' . $filename, 'w+');
		fwrite($fp, file($url));
		fclose($fp);
	}

}

if ($updatemessage <> '') {
	updateGit($updatemessage);
	echo $updatemessage;
}

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
