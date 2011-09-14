<?php
namespace tests\pages;

use \base;
use \base\LookupTable;

use \ReflectionClass;
use \ReflectionProperty;
use nabserv\Nabaztag;

/**
* @package tests.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Lookup extends base\Page {

	public $title = 'LookupTable Viewer';

	public function process() {

		$nabaztag = null;

		$tablename = $this->request->get('tablename');

		if($tablename) {

			$table = LookupTable::getInstance($tablename);
		}

		$this->set('tablename', $tablename);
		$this->set('table', $table);
	}
}

?>
