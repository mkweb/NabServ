<?php
namespace nabserv\apps;

/**
* package nabserv.apps
*/
class Speak extends App {

	protected $data = array(
		'code' 		=> 'speak',
		'name' 		=> 'Sprechen',
		'description' 	=> 'Lass dir von deinem Nabaztag etwas erzählen',
		'inuse'		=> false,
		'needed'	=> array(
			'text'  => array('type' => 'text', 'description' => 'Was dein Nabaztag sagt:'),
			'trigger' => array()
		),
		'multiple' 	=> true
	);

	public function validate($key, $value, $all) {

		if(parent::validate($key, $value, $all)) {

			return true;
		}

		if($key == 'text') {

			if(strlen($value) > 0) {

				return true;
			}
		}

		return false;
	}

	public function execute(){

		$data = $this->userdata;
		$text = $data['text'];

		$this->sendApi('tts', $text);
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
