<?php
namespace nabserv\apps;

/**
* package nabserv.apps
*/
class Clock extends App {

	protected $data = array(
		'code' 		=> 'clock',
		'name' 		=> 'Zeitansage',
		'description' 	=> 'Lass dir von deinem Nabaztag die Zeit ansagen',
		'inuse'		=> false,
		'needed'	=> array(
			'trigger' => array()
		)
	);

	public function execute(){

		$this->sendApi('clock', null);
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
