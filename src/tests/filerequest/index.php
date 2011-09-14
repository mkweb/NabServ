<?php
namespace tests\filerequest;

use base\mvc\Controller;
use homepage\Request;

/**
* @package tests.filerequest
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Index {

	public function execute() {

		$request = new Request();
		new Controller($request);
	}
}
?>
