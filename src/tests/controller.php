<?php
namespace tests;

use \base;

/**
* @package tests
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Controller extends base\Controller {

	public function handle() {

		if(count($this->request) == 0) {

			$this->request = array('index.php');
		}

		$base = ucfirst(array_shift($this->request));
		
		if(substr($base, -4) == '.php') {

			$base = substr($base, 0, -4);
		}

		$className = 'tests\\filerequest\\' . $base;

		$file = new $className;
		$file->execute();
	}
}

?>
