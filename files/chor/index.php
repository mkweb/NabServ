<?php
$removeAfter = false;

$request = explode("/", $_SERVER['REQUEST_URI']);

array_shift($request);
array_shift($request);

if(strstr($request[0], 'tmp_') !== false) {

	$removeAfter = true;
	$request[0] = str_replace('tmp_', 'tmp/', $request[0]);
}

$filename = dirname(__FILE__) . '/choreographies/' . $request[0];

if(file_exists($filename)) {

	readfile($filename);

	if($removeAfter) {

		unlink($filename);
	}
}

?>
