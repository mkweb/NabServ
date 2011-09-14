<?php
namespace nabserv;

use \base\Logger;

/**
* Class ActionProcessor
*
* This class is used to read Actions Nabaztag sends with sd-value or via rfid.php
*
* Currently implemented:
* <ul>
*     <li>Button pressed once</li>
*     <li>Button pressed twice</li>
*     <li>Ears got moved</li>
*     <li>Rfid</li>
* </ul>
*
* @package nabserv
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class ActionProcessor {

	/**
	* Read sd-value before response to Nabaztag is generated
	* Every action will be responsed directly
	*
	* @access public
	*
	* @param Int
	*/
	public function beforePing($action) {

		switch($action) {

			// Button pressed once
			case 3:
				
				$appc = AppController::getInstance(NAB_SERIAL);
				$appc->loadApps();
				$appc->executeActions('head-1');
				break;

			// Button pressed twice
			case 1:
				$appc = AppController::getInstance(NAB_SERIAL);
				$appc->loadApps();
				$appc->executeActions('head-2');
				break;
		}
	} 

	/**
	* Read sd-value after response to Nabaztag is generated
	* Actions generated in this Method will be responsed to Nabaztag at the nex ping-request
	*
	* @access public
	*
	* @param Int
	*/
	public function afterPing($action) {

		// Ear got moved
		if(substr($action, 0, 1) == "8") {

			$left  = hexdec($action[2 * 1 + 1]);
			$right = hexdec($action[2 * 0 + 1]);

			$before = Nabaztag::getInstance(NAB_SERIAL)->getData('ears');

			Logger::debug("before: " . print_r($before, true));
			Logger::debug("new: " . print_r(array('left' => $left, 'right' => $right), true));

			if($before['left'] != $left && $before['right'] != $right) {

				$appc = AppController::getInstance(NAB_SERIAL);
				$appc->loadApps();
				$appc->executeActions('ear-both');

			} elseif($before['left'] != $left) {

				$appc = AppController::getInstance(NAB_SERIAL);
				$appc->loadApps();
				$appc->executeActions('ear-left');

			} elseif($before['right'] != $right) {

				$appc = AppController::getInstance(NAB_SERIAL);
				$appc->loadApps();
				$appc->executeActions('ear-right');
			}

			Nabaztag::getInstance(NAB_SERIAL)->setData('ears', array('left' => $left, 'right' => $right));
		}
	}

	/**
	* To some action by Rfid_ID
	*
	* This method is triggered by rfid.php
	*
	* @access public
	* 
	* @param String
	*/
	public function rfid($id) {

		$sn = $_GET['sn'];

		$appc = AppController::getInstance($sn);
		$appc->loadApps();
		$appc->executeActions('rfid-' . $id);

		$nab = Nabaztag::getInstance($sn);
		$nab->setConfig('lastrfid', $id);
		$nab->setConfig('lastrfidts', time());
	}

	/**
	* Generating an API-Call to response TTS to Nabaztag
	*
	* @access private
	* 
	* @param String
	*/
	private function sendTts($msg) {

		$url = BASE_URL . "/vl/api.php?sn=" . NAB_SERIAL . "&token=" . NAB_TOKEN . "&tts=" . urlencode($msg);
		Logger::debug("Polling API with " . $url);
		Logger::debug("Result from API-Call: " . file_get_contents($url));

		echo file_get_contents($url);
	}

	/**
	* Generating an general API-Call
	*
	* @access private
	*
	* @param String
	* @param String
	*/
	private function sendApi($plugin, $data) {

		$url = BASE_URL . "/vl/api.php?sn=" . NAB_SERIAL . "&token=" . NAB_TOKEN . "&" . $plugin . "=" . urlencode($data);
		file_get_contents($url);
	}
}

?>
