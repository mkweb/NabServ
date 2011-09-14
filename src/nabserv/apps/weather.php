<?php
namespace nabserv\apps;

/**
* package nabserv.apps
*/
class Weather extends App {

	protected $data = array(
		'code' 		=> 'weather',
		'name' 		=> 'Wetter',
		'description' 	=> 'Lass dir von deinem Nabaztag das Wetter ansagen',
		'inuse'		=> false,
		'needed'	=> array(
			'city'  => array('type' => 'text', 'description' => 'Ort'),
			'trigger' => array()
		),
		'multiple'	=> true
	);

	public function validate($key, $value, $all) {

		if(parent::validate($key, $value, $all)) {

			return true;
		}

		if($key == 'city') {

			if(strlen($value) > 0) {

				return true;
			}
		}

		return false;
	}

	public function execute(){

		$data = $this->nabaztag->getConfig('apps');
		$data = $data['weather'];
		$city = $data['city'];

		$this->sendApi('weather', $city);
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
