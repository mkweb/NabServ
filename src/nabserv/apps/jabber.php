<?php
namespace nabserv\apps;

/**
* package nabserv.apps
*/
class Jabber extends App {

	protected $data = array(
		'code' 		=> 'jabber',
		'name' 		=> 'Quasseln',
		'description' 	=> 'Lass einen Nabaztag einfach darauf los quasseln',
		'inuse'		=> false,
		'needed'	=> array(
			'trigger' => array()
		),
		'multiple' 	=> true
	);

	public function validate($key, $value, $all) {

		return true;
	}

	public function execute(){

		$this->sendApi('mood');
	}

	public function onPing() {

		// Nothing to do
	}

	public function onCron(){

		$this->execute();
	}

	public function onAction(){

		$this->execute();
	}
}

?>
