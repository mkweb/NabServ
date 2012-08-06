<?php

$request = explode("/", $_SERVER['REQUEST_URI']);

if($request[2] == 'last.wav') {

	header('Content-Type: audio/x-wav');
	readfile('last.wav');
	exit;
}

array_shift($request);
array_shift($request);

$fh = fopen('debug.log', 'a+');

if($request[0] == 'tts') {

	$tmpfile = md5(microtime()) . '.mpga';
	fputs($fh, "Filename: " . $tmpfile . "\n");

	$search = array('ä', 'ö', 'ü', 'ß');
	$replace = array('ae', 'oe', 'ue', 'sz');

	$string = urlencode(str_replace($search, $replace, urldecode($request[1])));

	$url = "http://translate.google.com/translate_tts?ie=UTF-8&q=" . $string . "&tl=de&total=1&idx=0&textlen=10&prev=input";
	
	header('Content-Type: audio/mpg');
	readfile($url);

	$res = file_put_contents($tmpfile, file_get_contents($url));
	fputs($fh, "Save: " . var_export($res, true) . "\n");

	exec('./speedup.sh ' . $tmpfile, $output);
	fputs($fh, "Speedup: " . print_r($output, true) . "\n");

	$tmpfile .= '.mpg';
	fputs($fh, "New Filename: " . $tmpfile . "\n");

	header('Content-Type: audio/mpg');
	readfile($tmpfile);

	unlink($tmpfile);
}

?>
