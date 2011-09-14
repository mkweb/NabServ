<?php
namespace nabserv\plugins;

use nabserv\Nabaztag;
use nabserv\MessageBlock;
use \base\Logger;

/**
* MessagePlugin
*
* This Plugin sends a message to Nabaztag by given data from API
*
* @package nabserv.plugins
*
* @author Mario Klug <mario.klug@mk-web.at>
*/
class Message extends Plugin {

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

		if(strlen($data) < 1) {

			return false;
		}

		Nabaztag::getInstance(NAB_SERIAL)->setNewData('message', $data);

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

		if(isset($data['message'])) {

			$this->data = MessageBlock::getInstance()->getHex($data['message']);

			Nabaztag::getInstance(NAB_SERIAL)->setSeen('message', true);
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
			'type' => 'textarea',
			'name' => 'Nachricht',
			'value' => "ID 123456\nMW\nCH broadcast/chor/NewBlinkingWithEars.chor\nMW\n"
		);
	}
}

?>
