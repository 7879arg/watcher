<?php

$urls = file('urls.txt');
$updatemessage = '';
foreach ($urls as $uu) {
	$url = trim($uu); $filename = urlencode($url);
	if ($url <> '') {
		$oldmd5 = implode(file('md5/' . $filename));
		$md5 = md5(@implode(file($url)));

		if ($oldmd5 <> $md5) {
			$updatemessage = "Found change on " . trim($url) . "\n";
			$fp = fopen('md5/' . $filename, 'w+');
			fwrite($fp, $md5);
			fclose($fp);
			$fp = fopen('raw/' . $filename, 'w+');
			fwrite($fp, implode(file(trim($url))));
			fclose($fp);
		}
	}

}

if ($updatemessage <> '') {
	updateGit($updatemessage);
	echo $updatemessage;
}
function updateGit($message) {
	$fp = fopen('README.md', 'w+');
	fwrite($fp, $message);
	fclose($fp);
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
	sendEmail($message);
}
function sendEmail($message) {
	include('../email.php');
        $to = $email;
        $subject = 'Update';
        $headers = 'From: ' . $from . "\r\n" .
                'Reply-To: ' . $from . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $message, $headers);

}
