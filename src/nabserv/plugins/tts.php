<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use nabserv\MessageBlock;
use \base\Logger;

/**
* TtsPlugin
*
* This Plugin forces Nabaztag to play generates Audio via Google tts
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Tts extends Plugin {

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

		Logger::debug("Entering TTS::api with data: " . $data);

		if(strlen($data) < 1) {

			return false;
		}

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('tts', $data);
		Logger::debug("Called Nabaztag::setNewData");

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

		if(isset($data['tts'])) {

			$mb = new MessageBlock(rand(11111, 99999));
			$mb->addLocalStream('broadcast/broad/tts/' . urlencode($data['tts']));
			$this->data = $mb->getHex();

			Nabaztag::getInstance(NAB_SERIAL)->setSeen('tts', true);
			return true;
		}

		return false;
	}

	/**
	* This method gives demo the information which fields are used for this Plugin
	* The return can be 1 or 2-dimensional
	*
	* If 2-dimensional the fields must include an id
	*
	* Possible Types are:
	* <ul>
	*     <li>type - [text,textare,dropdown]</li>
	*     <li>id - for more than one field</li>
	*     <li>name - legend for input on demopage</li>
	*     <li>value - preselected defaultvalue</li>
	* </ul>
	*
	* @access public
	*
	* @return Array
	*/
	public function demo() {

		return array(
			'type' => 'text',
			'name' => 'Text',
			'value' => 'Ich bin lebendig'
		);
	}
}

?>
