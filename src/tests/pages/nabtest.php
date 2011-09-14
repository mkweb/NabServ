<?php
namespace tests\pages;

use \base;

use \ReflectionClass;
use \ReflectionProperty;
use nabserv\Nabaztag;

/**
* @package tests.pages
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Nabtest extends base\Page {

	public $title = 'NabTest';

	public function process() {

		$nabaztag = null;

		$serial = $this->request->get('serial');

		if($serial) {

			$nabaztag = Nabaztag::getInstance($serial);
		}

		if($this->request->get('unset')) {

			list($key, $value) = explode("-", $this->request->get('unset'));

			if($key == 'config') {

				$nabaztag->load();				
				$nabaztag->removeConfig($value);
				$nabaztag->save();
			}
		}

		$this->set('serial', $serial);
		$this->set('data', $data);
		$this->set('nabaztag', $nabaztag);
	}
}

?>
