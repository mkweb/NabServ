<?php

foreach($files as $file) {

	$tmp = explode('/', $file);
	$name = array_pop($tmp);

	if(array_pop($tmp) == 'tmp') {

		$name = 'tmp/' . $name;
	}

	echo '<a href="/tests/?page=chor&chor=' . $name . '">' . $name . '</a><br />';
}
?>
