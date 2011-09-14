<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use nabserv\MessageBlock;
use \base\Logger;

/**
* ClockPlugin
*
* This Plugins forces Nabaztag tell time via tts
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Clock extends Plugin {

	/**
	* Blocktype 0A for Message
	* @tutorial http://www.cs.uta.fi/hci/spi/jnabserver/documentation/index.html#blocks
	* @param String
	*/
	protected $blockType = '0A';

	/**
	* Called if Api has Requested this Plugin
	* Returns true if data is valid
	*
	* @access public
	*
	* @param String
	*
	* @return Boolean
	*/
	public function api($data) {

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('clock', true);
		return true;
	}

	/**
	* Called by ping-request from Nabaztag
	* Returns true if there is something to do
	*
	* @access public
	* 
	* @return Boolean
	*/
	public function ping() {

		$data = Nabaztag::getInstance(NAB_SERIAL)->getNewData();

		if(isset($data['clock'])) {

			if(date("H") == 0 && date("i") == 0) {

				$string = "Mitternacht";
			} elseif(date('i') != 0) {

				$string = sprintf("Es ist jetzt %d Uhr und %d Minuten", date("H"), date("i"));
			} else {

				$string = sprintf("Es ist jetzt genau %d Uhr", date("H"));
			}

			$mb = MessageBlock::getInstance(rand(11111, 99999));
			$mb->addLocalStream('broadcast/broad/tts/' . urlencode($string));

			$this->data = $mb->getHex();

			Nabaztag::getInstance(NAB_SERIAL)->setSeen('clock', true);
			return true;
		}

		return false;
	}
}

?>
