<?php

$request = explode("/", $_SERVER['REQUEST_URI']);

if($request[2] == 'last.wav') {

	header('Content-Type: audio/x-wav');
	readfile('last.wav');
	exit;
}

array_shift($request);
array_shift($request);

if($request[0] == 'tts') {

	$search = array('ä', 'ö', 'ü', 'ß');
	$replace = array('ae', 'oe', 'ue', 'sz');

	$string = urlencode(str_replace($search, $replace, urldecode($request[1])));

    $max = 60;

    if(strlen($string) > $max) {

        $string = substr($string, 0, $max);
    }

	$url = "http://translate.google.com/translate_tts?tl=de&q=" . $string;

	header('Content-Type: audio/mpeg');
	readfile($url);
}

?>
